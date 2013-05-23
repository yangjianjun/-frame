<?php
/*
 * 基本路由器类
 * */
class Router 
{
	public function getDefaultController(){
		
	}
	public function mapping($pathInfo=null){
		$frame 			= Frame::getInstance();
		$dController 	= $frame->config['controller'] ;
		$dMethod 		= $frame->config['action'] ;
		$mappingArr 	= array();
		if (!empty($pathInfo)){
			$vars 						= @explode('/', $pathInfo);
			$mappingArr['controller'] 	= isset($vars[1]) ?$vars[1]:$dController;
			$mappingArr['method']	 	= isset($vars[2]) && !empty($vars[2])?$vars[2]:$dMethod;
			$mappingArr['params']	 	= array_slice($vars, 3);
		}else {
			$mappingArr['controller'] 	= $dController;
			$mappingArr['method']	 	= $dMethod;
			$mappingArr['params']		= array();
		}
		return $mappingArr ;
	}
}