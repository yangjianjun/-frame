<?php

/**
 * 视图类
 */
class View {
	/**
	 * 视图文件
	 */
	private $file = '';
	
	public $baseUrl= null;
	
	/**
	 * 视图变量存放
	 */
	private $params = array();
	
	/**
	 * 全局变量存放
	 */
	public static $global_params = array();
	
	/**
	 * 构造函数
	 *
	 * @param $file 视图文件名
	 */
	public function __construct($file=null){
		$this->baseUrl= Frame::getInstance()->config['baseUrl'] ?Frame::getInstance()->config['baseUrl']:null;
		$this->set_file($file);
	}
	
	/**
	 * @param 静态方法
	 */
	public static function instance($file=null){
		return new View($file);
	}

	/**
	 * Magically sets a view variable.
	 *
	 * @param   string   variable key
	 * @param   string   variable value
	 * @return  void
	 */
	public function __set($key, $value)
	{
		$this->params[$key] = $value;
	}
	
	/**
	 * @param $file 视图文件名
	 */
	public function set_file($file)
	{
		$this->file = VIE_PATH.$file.EXT;
	}

	/**
	 * Magically gets a view variable.
	 *
	 * @param  string  variable key
	 * @return mixed   variable value if the key is found
	 * @return void    if the key is not found
	 */
	public function &__get($key)
	{
		$result = null; 
		if (isset($this->params[$key]))
			$result = $this->params[$key];

		if (isset(View::$global_params[$key]))
			$result =  View::$global_params[$key];

		if (isset($this->$key))
			$result =  $this->$key;
		return $result;
	}
	
	/**
	 * 自动设置变量
	 */
	public function __call($func, $args = NULL){
		return $this->__get($func);
	}
    
	/**
	 * 自动设置变量
	 */
	public function render($file=null,$render=true){
		if (!empty($file) ){
			$this->set_file($file);
		}
//		echo $this->file ;
		if (!file_exists($this->file)){
			throw new Exception('view file not exit');
			exit();
		}
		
		$data = array_merge(View::$global_params, $this->params);
		// Buffering on
		ob_start();
		// Import the view variables to local namespace
		extract($data, EXTR_SKIP);
		include $this->file;

		// Fetch the output and close the buffer
		$str = ob_get_clean();
		if ($render) echo $str;
		return $str;
	}
}