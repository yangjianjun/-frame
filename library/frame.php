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
    		$configDefatul = require_once APP_PATH.'config/default.php';
    		$configCommon = require_once APP_PATH.'config/common.php';
			$this->config($configDefatul+$configCommon);
    	}
    	
    	/*
		 * core class load and base class
		 * */		
		require_once LIB_PATH.'load.php';
		/**
		* Set the object automatically loaded
		*/
		spl_autoload_register(array('Load', 'loadClass'));
    	/*
		 * set error function
		 * */
		set_error_handler(array('error', $this->config['errorHandler']));
		/*
		 *  set exception function
		 * */
		set_exception_handler(array('error', $this->config['exceptionHandler']));
    }
    
    protected function config($config)
    {
    	if (empty($config)){
    		return false;
    	}
    	$this->config = $config ;
    	//timezone
    	if (isset($config['timezone'])){
    		date_default_timezone_set($config['timezone']);
    	}
    	//timezone
    	if (isset($config['session'])){
    		session_start();
    		$_SESSION['config'] = $this->config ;
    	}
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
		//is open Performance
		Performance::monitor(null,Performance::BEGIN);
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
		
		//Registration controller instantiated
		$frame 						= self::getInstance();
		
		
		
		//Generate controller reflection class instance
		$controller = $class->newInstance($frame->request,$mappingArr);
		try{
			// Load controller method
			$method = $class->getMethod('Action_'.$mappingArr['method']);
			if ($method->isProtected() or $method->isPrivate()){
				throw new Exception("protected controller method ");
			}
		} catch (ReflectionException $e){
			throw new Exception("load Action_".$mappingArr['controller']." fail ",404);
		}
		
		if (!is_object($frame->response)){
			$controller->response = $frame->response = new Response();
		}
		
		$controller->router = $frame->router ;
		//set haeders
		$controller->response->setHeader();
		
		
		
		//Open cache
		ob_start();
		// The method of the execution controller
		$method->invokeArgs($controller,array());
		
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
		Performance::monitor(null,Performance::END);
		
		$performanceStr = "";
		//sql time memery
		if ($this->config['openperformance'] && count(Performance::$data)>0){
			$performanceStr.= "<table style='text-align:center;width:100%;border :1px solid #666;'>";
			$performanceStr.= "<tr><td>sql/page</td><td>time(ms)</td><td>memory(kb)</td></tr>";
			foreach (Performance::$data as $k=>$v) {
				$performanceStr.= "<tr>
					<td>".$k."</td>
					<td>".($v[Performance::TIME][Performance::END]-$v[Performance::TIME][Performance::BEGIN])."</td>
					<td>".($v[Performance::MEMORY][Performance::END]-$v[Performance::MEMORY][Performance::BEGIN])."</td>
				</tr>";;
			}
			$performanceStr.= "</table>";
			
			Frame::getInstance()->response->body.= $performanceStr ;
		}
		
		
	}
}