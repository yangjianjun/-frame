<?php
//frame class Singleton
class Frame
{
	public  $request 	= 'request';
	public  $router		= 'router';
	public  $response	= 'response';
	public  $config		= null;
    public  $_baseUrl 	= null;

    protected static $_instance = null;


    protected function __construct()
    {
    	if (empty($this->config)){
			$this->config = require_once APP_PATH.'config.php';
    	}
    	/*
		 * core class load
		 * */		
		require_once LIB_PATH.'core.php';
    	/*
		 * set error function
		 * */
		set_error_handler(array('error', 'myErrorHandler'));
		/*
		 *  set exception function
		 * */
		set_exception_handler(array('error', 'myExceptionHandler'));
    }
    /**
     * Singleton instance
     *
     */
    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }


    
    
	protected function executionBefore(){
	
	}
	/**
	 * Enter description here ...
	 * @param array $mappingArr
	 * @return Response
	 */
	public function run($mappingArr=array()){
		$this->executionBefore();
		$response =  $this->_run($mappingArr);
		$this->executionAfter();
		return $response ;
	}
	
	/**
	 * @param array $mappingArr
	 * @throws Exception
	 * @return Response
	 */
	protected function _run($mappingArr){
		/**
		 * controller file
		 */
		$cFile = CON_PATH.$mappingArr['controller'].EXT;
		try {
			if (file_exists($cFile)){
				require_once $cFile;
			}else {
				throw new Exception("load {$cFile} fail ",404);
			}
			//controller class naming convention is: file name (first letter capitalized) + '_Controller'
			$class = new ReflectionClass('Controller_'.ucfirst($mappingArr['controller']));
		}catch (ReflectionException $e){
			throw new Exception("ReflectionClass controller fail ");
		}
		//Generate controller reflection class instance
		$controller = $class->newInstance($mappingArr);
		try{
			// Load controller method
			$method = $class->getMethod('Action_'.$mappingArr['method']);
			if ($method->isProtected() or $method->isPrivate()){
				throw new Exception("protected controller method ");
			}
		} catch (ReflectionException $e){
			throw new Exception("load Action_".$mappingArr['controller']." fail ",404);
		}
		
		//Registration controller instantiated
		$frame 						= self::getInstance();
		$controller->request 		= $frame->request ;
		$controller->router 		= $frame->router ;
		if (!is_object($frame->response)){
			$frame->response		=	new Response();
		}
		$controller->response 		= $frame->response ;
		
		
		//Open cache
		ob_start();
		// The method of the execution controller
		$method->invokeArgs($controller, $mappingArr['params']);
		
		//View Control
		if ($controller->useLayout){
			$viewFile 					= $mappingArr['controller'].'/'.$mappingArr['method'];
			$controller->layout->content= $controller->view->render($controller->autoview ? $viewFile:null,false);
			$layoutFile 				= $frame->config['layout'] ;
			$controller->layout->render($controller->autoLayout? $layoutFile:null,true);
		}else {
			$viewFile 					= $mappingArr['controller'].'/'.$mappingArr['method'];
			$controller->view->render($controller->autoview ? $viewFile:null,true);
		}
	
		//response filling body
		$frame->response->body		= 	ob_get_clean() ;
		//Return response
		return $frame->response;
		
	}
	protected function executionAfter(){
		//Performance Analysis
		print_r(Performance::$sql);
		if (count(Performance::$sql)>0){
			echo "<table border=1>";
			
			foreach (Performance::$sql as $sql=>$v) {
				echo "<tr><td>".$sql."</td><td>".($v[Performance::END]-$v[Performance::END])."</td></tr>";;
			}
			echo "</table>";
		}
	}
}