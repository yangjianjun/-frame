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
		require_once 		LIB_PATH.'frame.php';	
		$frame 				= Frame::getInstance();
		/*
		 * request
		 * */
		$request 			= new Request($frame->config);
		
		$pathInfo		 	= $request->getRequestUri();
		
		$frame->request		= $request ;
		/**
		 * router
		 * */
		$router 			= new Router();
		$mappingArr 		= $router->mapping($pathInfo);
		$frame->router		= $router ;
		/*
		 * frame run and return response
		 * */
		$response 			= $frame->run($mappingArr);
		
		/*
		 * response's body output return html
		 */
		$response->output();
	}
}