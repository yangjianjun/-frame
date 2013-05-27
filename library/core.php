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
		common::go404();
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

/**
 * 模型类
 *
 */
class Model{
	public $db		=null;
	public $tbName	=null;
	public function __construct($id = null){
		$this->db = db::instance();
	}
	//封装sum ,max ,min,avg  
	public function assemble($fnName=null,$fieldName=null,$where=null){
		return $this->db->assemble($this->tbName,$fnName,$fieldName,$where);
	}
	//封装count
	public function count($where=null){
		return $this->db->count($this->tbName,$where);
	}
	//封装fetchAll
	public function fetchAll($where=null,$limit=null){
		return $this->db->fetchAll($this->tbName,$where,$limit);
	}

	//封装fetchRow
	public function fetchRow($where=null){
		return $this->db->fetchRow($this->tbName,$where);
	}
	//封装insert
	public function insert(array $data=array()){
		return $this->db->insert($this->tbName,$data);
	}
	//封装update
	public function update(array $data=array(),$where=null){
		return $this->db->update($this->tbName,$data,$where);
	}
	
	//封装delete
	public function delete($where=null){
		return $this->db->delete($this->tbName,$where);
	}
}

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

/**
 * 自动载入类
 */
class Load {
	
	static function loadClass($class_name)
	{
		if (preg_match('/Controller/i',$class_name)){
			$file = CON_PATH.DIRECTORY_SEPARATOR.substr($class_name, strlen('Controller')).EXT;
			if (file_exists($file))	{
				return include_once $file;
			}
			else {
				echo $file = LIB_PATH.str_replace("_", "/", strtolower($class_name)).EXT;
				if ($file && file_exists($file)) {
					return include_once $file;
				}
			}
		}else if (preg_match('/Model/i',$class_name)){
			$file = MOD_PATH.DIRECTORY_SEPARATOR.strtolower(substr($class_name, strlen('Model_'))).EXT;
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

/**
* 设置对象的自动载入
*/
spl_autoload_register(array('Load', 'loadClass'));

/**
 * $_GET, $_POST, $_SERVER 控制 HTTP vars
 *
 */
class input{

	/**
     * Returns an array with all the variables in the GET header, fetching them
     * @static
     */
	public static function get($key = '', $value = '')
	{
		if ($value !== '') $_GET[$key] = $value;
		if ($key) return @$_GET[$key];
		return $_GET;
	}

	/**
     * Returns an array with all the variables in the POST header, fetching them
     */
	public static function post($key = '', $value = '')
	{
		if ($value !== '') $_POST[$key] = $value;
		if ($key) return @$_POST[$key];
		return $_POST;
	}


	/**
     * Returns an array with all the variables in the session, fetching them
     */
	public static function session($key = '', $value = '')
	{
		if ($value !== '') $_SESSION[$key] = $value;
		if ($key) return @$_SESSION[$key];
		return $_SESSION;
	}

	/**
     * Returns an array with the contents of the $_COOKIE global variable
     */
	public static function cookie($key = '', $value = '')
	{
		if ($value !== '') $_COOKIE[$key] = $value;
		if ($key) return @$_COOKIE[$key];
		return $_COOKIE;
	}

	/**
     * Returns the value of the $_REQUEST array. In PHP >= 4.1.0 it is defined as a mix
     * of the $_POST, $_GET and $_COOKIE arrays, but it didn't exist in earlier versions.
     */
	public static function request($key = '', $value = '')
	{
		if ($value !== '') $_REQUEST[$key] = $value;
		if ($key) return @$_REQUEST[$key];
		return $_REQUEST;
	}

	/**
     * Returns the $_SERVER array, otherwise known as $HTTP_SERVER_VARS in versions older
     * than PHP 4.1.0
     */
	public static function server($key = '', $value = '')
	{
		if ($value !== '') $_SERVER[$key] = $value;
		if ($key) return @$_SERVER[$key];
		return $_SERVER;
	}

	/**
     * Returns the $_SERVER array, otherwise known as $HTTP_SERVER_VARS in versions older
     * than PHP 4.1.0
     */
	public static function file($key = '')
	{
		if ($key) return @$_FILES[$key];
		return $_FILES;
	}
	

	/**
     * Returns the base URLs of the script
     * base/path/request/query/self
     */
	public static function uri($key = '')
	{
		switch ($key){
			case 'base': {
				return 'http://'.self::server('HTTP_HOST').'/';
			}
			case 'path': return self::server('PATH_INFO');
			case 'request': return self::server('REQUEST_URI');
			case 'query': return self::server('QUERY_STRING');
			case 'self': return self::server('PHP_SELF');
			default:	return '';
		}
	}

}