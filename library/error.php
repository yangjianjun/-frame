<?php
class Error
{
	//先定义一个函数，也可以定义在其他的文件中，再用require()调用  
	public static function myErrorHandler($errno, $errstr,$errfile,$errline)
	{  
		$logStr = "";
	    switch ($errno) {  
	    	case E_USER_ERROR:  
		     $logStr.= "<b>My ERROR</b> [$errno] $errstr<br />\n";  
		        $logStr.= "  Fatal error on line $errline in file $errfile ";  
		        $logStr.= ", PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />\n";  
		        $logStr.= "Aborting...<br />\n";  
		        exit(1);  
		        break;  
		  
		    case E_USER_WARNING:  
		        $logStr.= "<b>My WARNING</b> [$errno] $errstr<br />\n";  
		        $logStr.= "  on line $errline in file $errfile ";  
		        $logStr.= ", PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />\n";  
		        break;  
		  
		    case E_USER_NOTICE:
		        $logStr.= "<b>My NOTICE</b> [$errno] $errstr<br />\n";  
		        $logStr.= "  on line $errline in file $errfile ";  
		        $logStr.= ", PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />\n";
		        break;  
		   	case E_NOTICE:
		        $logStr.= "<b> NOTICE</b> [$errno] $errstr<br />\n";  
		        $logStr.= "  on line $errline in file $errfile ";  
		        $logStr.= ", PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />\n";
		        break;  
		    default:  
		        $logStr.= "Unknown error type: [$errno] $errstr<br />\n";  
		        $logStr.= "  on line $errline in file $errfile ";  
		        $logStr.= ", PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />\n";  
		        break;  
	    }  
	    //log
	    file_put_contents(LOG_PATH."output_errors_".date('Y').".log" , $logStr,FILE_APPEND) ;
		//如果局域网测试段ip提示所有错误，否则屏蔽
		$ip = Common::getIp() ; 
		if (!preg_match("/(^192.168.*)|(^127.*)|(^10.*)/i", $ip)){
			$logStr=str_replace(getcwd(),"",$logStr);  
		}
		echo $logStr ;
		
	    return true;  
	}  
	//先定义一个函数，也可以定义在其他的文件中，再用require()调用  
	public static function myExceptionHandler($e)
	{   
		$logStr ='';
		$logStr.= '<br />code='.$e->getCode();
		$logStr.= '<br />File='.$e->getFile();
		$logStr.= '<br />line='.$e->getLine();
		$logStr.= '<br />Message='.$e->getMessage();
		$logStr.= '<br />TraceAsString='.$e->getTraceAsString();
		
		file_put_contents(LOG_PATH."output_exception_".date('Y').".log" , $logStr,FILE_APPEND) ;
		//如果局域网测试段ip提示所有错误，否则屏蔽
		$ip = Common::getIp() ; 
		if (preg_match("/(^192.168.*)|(^127.*)|(^10.*)/i", $ip)){
			echo $logStr ; 
		}
		if ($e->getCode() == 404){
			Common::go404();
		}
	    return true;  
	}  
}