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
	
	
	
	public $isLogin =false;
	public function __construct(){
		$this->view		= View::instance();
		$this->layout	= View::instance();
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
}