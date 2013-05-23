<?php
/*
 * bootstrap class
 * */
class bootstrap 
{
	function __construct(){
		
	}
	public function dispatch(){
		/*
		 * frame load
		 * */	
		require_once LIB_PATH.'frame.php';	
		$frame 				= Frame::getInstance();
		/*
		 * request
		 * */
		$request 			= new Request();
		$pathInfo		 	= $request->getPathInfo();
		$frame->request		= $request ;
		/**
		 * router
		 * */
		$router 			= new Router();
		$mappingArr 		= $router->mapping($pathInfo);
		$frame->router		= $router ;
		/*
		 * run
		 * */
		$response 			= $frame->run($mappingArr);
		
		/*
		 * return html
		 */
		$response->output();
	}
}