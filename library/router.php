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
		if ($frame->config['urlRule']==1){
			//this is urlRule 2
			if (preg_match("/&|\?|\=/i", $pathInfo)){
				throw new Exception("sorry !Illegal url Refer urlRule! ");
			}
			//use controller and action
			if (!empty($pathInfo) && $pathInfo!='/' ){
				$vars 						= @explode('/', $pathInfo);
				$mappingArr['controller'] 	= isset($vars[1]) ?$vars[1]:$dController;
				$mappingArr['method']	 	= isset($vars[2]) && !empty($vars[2])?$vars[2]:$dMethod;
				print_r($vars);
				$params	 					= array_slice($vars, 3);
			}else {//default controller and action
				$mappingArr['controller'] 	= $dController;
				$mappingArr['method']	 	= $dMethod;
				$mappingArr['params']		= array();
			}
			//Parameters such as :a/2/b/3/.....
			if (!empty($params)){
				for ($i = 0; $i < count($params); $i+=2) {
					//Illegal url out of
					if (empty($params[$i]) || !isset($params[$i+1])){
						break ;
					}
					//set query paramers
					$frame->request->setQuery($params[$i],$params[$i+1]);
				}
			}
		}else if ($frame->config['urlRule']==2){
			$pathArr					= @explode('?', $pathInfo);
			//use controller and action
			if (!empty($pathArr[0]) && $pathArr[0]!='/' ){
				$vars 						= @explode('/', $pathArr[0]);
				$mappingArr['controller'] 	= isset($vars[1]) ?$vars[1]:$dController;
				$mappingArr['method']	 	= isset($vars[2]) && !empty($vars[2])?$vars[2]:$dMethod;
			}else {//default controller and action
				$mappingArr['controller'] 	= $dController;
				$mappingArr['method']	 	= $dMethod;
			}
			//Parameters such as:a=1&b=2...
			if (!empty($pathArr[1])){
				$paramsArr 				= @explode('&', $pathArr[1]);
				//Array ( [0] => a=2 [1] => b=3 ) 
				if (!empty($paramsArr)){
					foreach ($paramsArr as $param) {
						//Array([0]=>a [1]=>2)
						$paramArr 		= @explode('=', $param);
						//set query paramers
						if (!empty($paramArr[0]) && !empty($paramArr[1])){
							$frame->request->setQuery($paramArr[0],$paramArr[1]);
						}
					}
				}
			}else {
				//this is urlRule 1
				if (isset($vars) && count($vars)>3 && !empty($vars[3])){
					throw new Exception("sorry !Illegal url Refer urlRule! ");
				}
			}
		}
		return $mappingArr ;	
	}
}