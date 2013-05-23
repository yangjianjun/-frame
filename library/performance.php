<?php
class Performance
{
	const BEGIN =1;
	const END	=0 ;
	
	public static $sql=array();
	//检测sql执行性能
	public	static function sql($sql=null,$status=self::BEGIN){
		if (empty($sql)){
			return false ;
		}
		if ($status == self::BEGIN ){
			self::$sql[$sql][self::BEGIN] = self::microtime_float();
		}else {
			self::$sql[$sql][self::END]   = self::microtime_float() ;
		}
		
	}
	
	public	static function microtime_float(){
	    list($usec, $sec) = explode(" ", microtime());
	    return $sec*1000+$usec ;
	}
} 