<?php
/**
 * 控制器类
 *
 */
class Controller {
	
	/**
	 * 默认模板地址
	 *
	 */
	
	public  	$request 	= 'request';
	public  	$router		= 'router';
	public  	$response	= 'response';
	
	
	public 		$view		= null ;
	public 		$autoview	= true ; //是否自动定位view文件,为true用默认的view (view/controller名/action名)
	public 		$layout 	= null;
	public 		$useLayout	= true ; //是否用layout文件
	public 		$autoLayout	= true ; //是否自动定位layout文件,为true用默认的layout (view/controller名)
	
	public      $action     = null;
	public      $controller = null;
	public      $config		= null;
	public 		$isLogin 	=false;
	public function __construct($request=null,$mappingArr=null){
		if (empty($this->config)){
			$this->config 		= Frame::getInstance()->config ;
		}
		if (is_object($request)){
			$this->request 		= $request ;
		}
	
		$this->view				= View::instance();
		$this->view->config 	= Frame::getInstance()->config ;
		
		
		$this->layout			= View::instance();
		$this->layout->config 	= Frame::getInstance()->config ;
		
		if (is_array($mappingArr)){
			$this->layout->action 		= $this->view->action		=$this->action 	   = $mappingArr['method'];
			$this->layout->controller 	= $this->view->controller	=$this->controller = $mappingArr['controller'];
		}
		$this->init();
	}	
	protected function init(){
	}
	
	public function __call($method = '', $params = ''){
		throw new Exception("can't find page", 404);
	}
	public function render($file=null){
		if (empty($file)){
			return false ;
		}
		$this->autoview = false ;
		$this->view->set_file($file);
	}
	public function setLayout($file=null){
		if (empty($file)){
			return false ;
		}
		$this->autoLayout = false ;
		$this->layout->set_file($file);
	}
	public function disableLayout(){
		$this->useLayout = false ;
	}
	
	public function setTitle($string=null){
		if (empty($string)){
			return false;
		}
		if (is_object($this->layout)){
			$this->layout->t = $string;
		}else {
			$this->view->t = $string;
		}
	}
	public function setKeywords($string=null){
		if (empty($string)){
			return false;
		}
		if (is_object($this->layout)){
			$this->layout->k = $string;
		}else {
			$this->view->k = $string;
		}
	}
	public function setDescription($string=null){
		if (empty($string)){
			return false;
		}
		if (is_object($this->layout)){
			$this->layout->d = $string;
		}else {
			$this->view->d = $string;
		}
	}
	
	
	
	//request function
    public function getQuery($key = null, $default = null)
    {
    	return $this->request->getQuery($key,$default);
    }

    public function getPost($key = null, $default = null)
    {
        return $this->request->getPost($key,$default);
    }
    public function getServer($key = null, $default = null)
    {
        return $this->request->getServer($key,$default);
    }

    public function getEnv($key = null, $default = null)
    {
        return $this->request->getEnv($key,$default);
    }
    public function getBaseUrl()
    {
        return $this->request->getBaseUrl();
    }

    public function getParam($key=null, $default = null)
    {
        return $this->request->getParam($key, $default );
    }

    public function getMethod()
    {
        return $this->request->getMethod( );
    }
    public function isPost()
    {
        return $this->request->isPost( );
    }
    public function isGet()
    {
        return $this->request->isGet( );
    }
    public function isPut()
    {
        return $this->request->isPut( );
    }

    public function isDelete()
    {
        return $this->request->isDelete( );
    }

    public function isHead()
    {
        return $this->request->isHead( );
    }

    public function isOptions()
    {
        return $this->request->isOptions( );
    }

    public function isXmlHttpRequest()
    {
        return $this->request->isXmlHttpRequest();
    }

    public function isFlashRequest()
    { 
        return $this->request->isFlashRequest();
    }
    public function isSecure()
    {
        return $this->request->isSecure();
    }
    
    
    //response function
	public function setHeader($key = NULL, $value = NULL)
	{
		return $this->response->setHeader($key,$value);
	}
	public function session($key = '', $value = '')
	{
		return $this->response->session($key,$value);
	}
	public function cookie($key = '', $value = '')
	{
		return $this->response->cookie($key,$value);
	}
}