<?php
class Performance
{
	const BEGIN 	='begin';
	const END		='end' ;
	
	const TIME 		= 'time';
	const MEMORY	= 'memory' ;
	// Whether to open Performance Analysis
	
	public static $data 	=array();
	//sql  Performance monitor (time and memory)
	public	static function monitor($sql=null,$status=null){
		//sure openperformance
		if (!Frame::getInstance()->config['openperformance']){
			return false ;
		}
		//Single sql execution time, memory
		if (!empty($sql)){
			//time
			self::$data[$sql][self::TIME][$status] 	 = self::timeMs();
			//memory
			self::$data[$sql][self::MEMORY][$status] = self::memory();
		}else { //Total Page execution time, memory
			//time
			self::$data['page'][self::TIME][$status] 		 = self::timeMs();
			//memory
			self::$data['page'][self::MEMORY][$status] 		 = self::memory();
		}
	}
	public static function memory(){
		$size = memory_get_usage();
		return round($size/1024,2);
	}
	public	static function timeMs(){
	    list($usec, $sec) = explode(" ", microtime());
	    return $ms = $sec*1000+((int)($usec*1000)) ;
	}
} 