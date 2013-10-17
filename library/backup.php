<?php
/******   备份数据库结构 ******/
class Backup
{
	protected $link=null;
	function __construct(){
		$cfg = $_SESSION['config']['db']['0'] ;
		
		
		$this->link = mysql_connect($cfg['host'],$cfg['user'],$cfg['password']);
		mysql_select_db($cfg['dbname']);
		mysql_query(" set names ".$cfg['charset'],$this->link);
	}

	/*
	函数名称：table2sql()
	函数功能：把表的结构转换成为SQL
	函数参数：$table: 要进行提取的表名
	返 回 值：返回提取后的结果，SQL集合
	函数作者：heiyeluren
	*/
	
	function table2sql($table) 
	{
		$tabledump = "DROP TABLE IF EXISTS $table;\n";
		$createtable = mysql_query("SHOW CREATE TABLE $table",$this->link);
		$create = @mysql_fetch_row($createtable);
		$tabledump .= $create[1].";\n\n"; 
		return $tabledump;
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
		$tabledump = "DROP TABLE IF EXISTS $table;\n";
		$createtable = mysql_query("SHOW CREATE TABLE $table",$this->link);
		
		$create = @mysql_fetch_row($createtable);
		$tabledump .= $create[1].";\n\n";
		
		$rows = mysql_query("SELECT * FROM $table" ,$this->link);
		$numfields = mysql_num_fields($rows);
		$numrows = mysql_num_rows($rows);
		while ($row = @mysql_fetch_row($rows))
		{
		   $comma = "";
		   $tabledump .= "INSERT INTO $table VALUES(";
		   for($i = 0; $i < $numfields; $i++) 
		   {
				$tabledump .= $comma."'".mysql_escape_string($row[$i])."'";
				$comma = ",";
		   }
		   $tabledump .= ");\n";
		}
		$tabledump .= "\n";
		
		return $tabledump;
	}
}
			