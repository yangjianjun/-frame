<?php
/** 
 * @author   alfa
 * @version  v1.0
 */

class upload
{
	/**
	 * 保存的物理地址
	 */
    protected $savePath; 
    
    /**
     * 文件URL地址
     */
    protected $saveUrl;
    
    protected $year;
    protected $month;
    
    //当前对象
    protected $file;
    
    //文件名
    protected $saveName;
    
    //图片允许类型
    protected $imageType = array("image/gif","image/pjpeg","image/jpeg","image/jpg");
    
    //FLASH允许类型
    protected $flashType = array("application/x-shockwave-flash","application/octet-stream");
    protected $cur_type;//当前类型
    protected $ext;//扩展名
    protected $upload_type;//上传类型
    protected $success = false;
    
	 /**
	  * @param Object $uploadImage
	  * @param string $type:art:表示插入在文章中的图片
	  * @return  
	  */
	 private function __construct($file, $upload)
	 {
	 	$this->file = & $file;
	 	if ($upload) $this->doUpload($file);
	 }
 
	 /**
	  * 创建对象
	  */
	 public static function instance($file, $upload = false){
	 	return new upload($file, $upload);
	 }
	 
	 /**
	  * 上传文件
	  */
	 public function doUpload($file = ''){
	 	if (!isset($file["tmp_name"]) || !is_uploaded_file($file["tmp_name"])){
	 		$file = & $this->file;
	 	}

	 	if (!is_uploaded_file($file["tmp_name"])) return -1;
	 	 if ($file["error"] !== 0) return -2;
	 	 if (!$this->checkSize($file["size"])) return -3;
	 	 
	     if (in_array($file["type"], $this->imageType)){
	         $this->cur_type = 0;
	     }elseif (in_array($file["type"], $this->flashType)){
	         $this->cur_type=1;
	     }
	     $this->savePath = DOCROOT.common::config('upload.direct');
	     $this->saveUrl = '/'.common::config('upload.direct');
	     
	     $this->year = date('Y');
	     $this->month = date('m');
	     $this->ext = substr($file["name"], strrpos($file["name"], "."));
	     $this->saveName = time().$this->ext;

	     $this->savePath .= DIRECTORY_SEPARATOR.$this->year.DIRECTORY_SEPARATOR.$this->month;
	     if (!file_exists($this->savePath)) common::createDir($this->savePath);

	     $this->savePath .= DIRECTORY_SEPARATOR.$this->saveName;
	     $this->saveUrl  .= '/'.$this->year.'/'.$this->month."/".$this->saveName;
	     
	 	 return $this->success = move_uploaded_file($file["tmp_name"], $this->savePath)?true:false;
	 }
	 
	 /**
	  * 检测用户提交文件大小是否合法
	  * @param int $size 用户上传文件的大小
	  * @return boolean 如果为true说明大小合法，反之不合法
	  */
	 public function checkSize($size) 
	 {
	 	$maxSize = common::config('upload.size');
	 	if ($size > $maxSize) return false;
	   	return true;
	 }

	 /**
	  * 检测用户提交文件类型是否合法
	  * @param string $ext 
	  * @param string $allow_types :多种格式用|分割
	  * @return boolean 如果为true说明类型合法，反之不合法
	  */
	 public function checkType($ext, $allow_types = 'jpg|gif|png') 
	 {
	 	$arr = explode('|', strtolower($allow_types));
	 	return in_array(strtolower($ext), $allow_types);
	 }
	 
	 
 	/**
	 * 自动读取
	 */
	public function __get($name){
		if (isset($this->$name)) return $this->$name;
	}
	
	/**
	 * 自动调用
	 */
	public function __call($name, $arguments = null){
		return $this->__get($name);
	}
}

?>