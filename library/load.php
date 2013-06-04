<?php

/**
 * 自动载入类
 */
class Load {
	
	static function loadClass($class_name)
	{   
		if (preg_match('/Controller/i',$class_name) && preg_match('/_/i',$class_name)){
			$file = CON_PATH.substr($class_name, strlen('Controller')).EXT;
			if (file_exists($file))	{
				return include_once $file;
			}
		}else if (preg_match('/Model/i',$class_name) && preg_match('/_/i',$class_name)){
			$file = MOD_PATH.strtolower(substr($class_name, strlen('Model_'))).EXT;
			if (file_exists($file))	{
				return include_once $file;
			}
		}else {
			$file = LIB_PATH.str_replace("_", "/", strtolower($class_name)).EXT;
			if ($file && file_exists($file)) {
				return include_once $file;
			}
		}
		throw new Exception("load {$class_name} fail ");
	}
}