<?php
/******   备份数据库结构 ******/
class Backup extends Model
{
	/*
	函数名称：table2sql()
	函数功能：把表的结构转换成为SQL
	函数参数：$table: 要进行提取的表名
	返 回 值：返回提取后的结果，SQL集合
	函数作者：heiyeluren
	*/
	
	function table2sql($table) 
	{
		$tabledump = "DROP TABLE IF EXISTS `$table`;\n";
		$createtable = $this->db->getOneResult("SHOW CREATE TABLE `$table`");
		
		$tabledump .= $createtable['Create Table'].";\n\n";
	}
	
	
	/****** 备份数据库结构和所有数据 ******/
	/*
	函数名称：data2sql()
	函数功能：把表的结构和数据转换成为SQL
	函数参数：$table: 要进行提取的表名
	返 回 值：返回提取后的结果，SQL集合
	函数作者：heiyeluren
	*/
	function data2sql($table) 
	{
		$tabledump = "DROP TABLE IF EXISTS `$table`;\n";
		$createtable = $this->db->getOneResult("SHOW CREATE TABLE `$table`");
		
		$tabledump .= $createtable['Create Table'].";\n\n";
		
		
		$rows = $this->db->query("SELECT * FROM $table" );
		if (empty($rows)){
			return $tabledump ;
		}
		$numfields = count($rows[0]);
		$numrows = count($rows);
		foreach($rows as $k=>$row){
		   $comma = "";
		   $tabledump .= "INSERT INTO $table VALUES(";
		   foreach ($row as $kk=>$vv) {
		   		$tabledump .= $comma."'".mysql_escape_string($vv)."'";
				$comma = ",";
		   }
		   $tabledump .= ");\n";
		}
		$tabledump .= "\n";
		
		return $tabledump;
	}
	
	function restore($fname){
		if (file_exists($fname)) {
			
			$sql_value="";
			$cg=0;
			$sb=0;
			$sqls=file($fname);
			
			
			foreach($sqls as $sql){
		    	$sql_value.=$sql;
		    }
			$a=explode(";", $sql_value);  //根据";\r\n"条件对数据库中分条执行
			$total=count($a)-1;
		    for ($i=0;$i<$total;$i++){
			    //执行命令
			    $sql = $a[$i].";";
			    if($this->db->exec($sql)){
			    	$cg+=1;
			    }else{
			    	$sb+=1;
			    	$sb_command[$sb]=$sql;
		    	}
		    }
//		    $msg= "操作完毕，共处理 {$total}条命令,成功 {$cg}条，失败 {$sb}条";
		    //显示错误信息 
//		    if ($sb>0){
//		    	$msg.= "<hr><br><br>失败命令如下：<br>";
//		    	for ($ii=1;$ii<=$sb;$ii++){
//		     		$msg.= "<p><b>第 ".$ii." 条命令（内容如下）：</b><br>".$sb_command[$ii]."</p><br>";
//		    	}
//		    }
		    $msg="操作完毕，共处理 {$total}条命令";   
		}else{
	   		$msg=  "MySQL备份文件不存在，请检查文件路径是否正确！";
		}
		Common::formRequest("/admin/db",array("msg"=>base64_encode($msg)));
	}
}
			