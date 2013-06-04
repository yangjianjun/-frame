<?php
/**
 * this is a static class
 * 
 */
class Logs{

	const LOG_FILE 			= 1;
	
	
	const LOG_DB   			= 2;
    /**
     * 防止实例化
     *
     */
    private function __construct(){}
	
    public static $operType = array(1=>'查看',2=>"添加",3=>"修改",4=>"删除",5=>"同步",6=>"搜索",8=>"导出",9=>'登陆',10=>'sql跟踪');
  
    /**
     * 日志记录
     */
    public static function setFileLogs($logName=null,$msg=null,$operType=1){
    	//check
    	if (empty($msg) || empty($logName)){
    		return false;
    	}
    	//if logName has dir  example: $logName  is aa/bb.log
    	//create log file is  LOG_PATH/aa/bb.log
    	if (preg_match("#\/#i", $logName)){
    		self::createDirByPath($logName);
    	}
    	file_put_contents(LOG_PATH.$logName, " 操作： ".self::$operType[$operType]."  ".  $msg ." \t".date("Y-m-d H:i:s")." \r\n ",FILE_APPEND);
    }
    
    //if logName has dir  example: $logName  is aa/bb.log  create log file is  LOG_PATH/aa/bb.log
    public static function createDirByPath($logName=NULL){
    	if (empty($logName)){
    		return false ;
    	}
    	$var = explode("/", $logName);
    	$dirName=LOG_PATH ;
    	for ($i = 0; $i < count($var)-1; $i++) {
    		$dirName = $dirName.'/'.$var[$i] ;
    		if (!is_dir($dirName)){
    			mkdir($dirName,0777);
    		}
    	}
    }
    
 
}    