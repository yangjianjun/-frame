<?php
/**
*
* date :2012-9-15 经典的文件上传类 
**/
class Up {
	public $f_size;//定义的文件大小
    public $f_sys; //接收文件属性
	public $f_name;//自定义的文件名
	public $f_dir; //自定义上传目录
	public $f_prename; //自定义上传目录
 
	//参数：文件流，目录，大小，文件名
	function __construct($sys,$dir="",$size="1",$name=""){
	 $this->f_size=$size*1000000;
	 $this->f_sys=$sys;
	 $this->f_name=$name;
	 $this->f_dir=$dir;
	 $this->f_mv();
	}
 
	//判断文件大小
   function is_size(){
   	 if($this->f_sys['size']<=$this->f_size){
	   return true;
	 }else{
	   return false;
	 }
   } //end
   
   //判断文件类型，返回扩展名
   function is_type(){ 
	switch($this->f_sys['type']){
		case "image/x-png": $ok=".png";
		break;
		case "image/png": $ok=".png";
		break;
		case "application/pdf": $ok=".pdf";
		break;
		case "image/pjpeg": $ok=".jpg";
		break;
		case "image/jpeg": $ok=".jpg";
		break;
		case "image/jpg": $ok=".jpg";
		break;
		default: $ok=false;
		break;
	}
	return $ok;
    }
   
   //终止函数
   function f_over($n){
        echo $n;
		exit();
   }
   
   //判断文件夹是否存在，并创建
   function is_dirs(){
    if($this->f_dir){
		if(!is_dir($this->f_dir)){ 
		  mkdir($this->f_dir);
		}
		return $this->f_dir;
	}else{
		if(!is_dir(date("Ymd"))){ 
		  mkdir(date("Ymd"));
		}
		return date("Ymd");
	}
   }
   
   
   //文件名的定义，不定义而使用时间戳
   function is_name(){
     if($this->f_name){
      $this->f_prename = $this->f_name;
	  $fn=$this->f_prename.$this->is_type();
	 }else{
	  $this->f_prename = time().rand(100,999);
	  $fn=$this->f_prename.$this->is_type();
	 }
	 $this->f_name = $fn ;
	 return $this->is_dirs()."/".$fn;
   }
 
 
   //上传文件
   function f_mv(){
	 $this->is_size()?null:$this->f_over("文件超过大小");
	 $this->is_type()?null:$this->f_over("文件类型不正确");;
	 move_uploaded_file($this->f_sys['tmp_name'],$this->is_name());
}
//请继续完善，水印，等等

	/**
	 * 生成缩略图
	 * @author yangzhiguo0903@163.com
	 * @param string     源图绝对完整地址{带文件名及后缀名}
	 * @param string     目标图绝对完整地址{带文件名及后缀名}
	 * @param int        缩略图宽{0:此时目标高度不能为0，目标宽度为源图宽*(目标高度/源图高)}
	 * @param int        缩略图高{0:此时目标宽度不能为0，目标高度为源图高*(目标宽度/源图宽)}
	 * @param int        是否裁切{宽,高必须非0}
	 * @param int/float  缩放{0:不缩放, 0<this<1:缩放到相应比例(此时宽高限制和裁切均失效)}
	 * @return boolean
	 */
	function img2thumb($src_img, $dst_img, $width = 75, $height = 75, $cut = 0, $proportion = 0)
	{
	    if(!is_file($src_img))
	    {
	        return false;
	    }
//	    echo "  目标图片=" .$dst_img;
	    $ot = $this->fileext($dst_img);
	    
//	    echo ' 后缀= ' .$ot ;
	    
	    $otfunc = 'image' . ($ot == 'jpg' ? 'jpeg' : $ot);
//	    echo '函数=' .$otfunc;
	    
//	    echo "<br />";
	    $srcinfo = getimagesize($src_img);
	    $src_w = $srcinfo[0];
	    $src_h = $srcinfo[1];
	    $type  = strtolower(substr(image_type_to_extension($srcinfo[2]), 1));
	    $createfun = 'imagecreatefrom' . ($type == 'jpg' ? 'jpeg' : $type);
	
	    $dst_h = $height;
	    $dst_w = $width;
	    $x = $y = 0;
	
	    /**
	     * 缩略图不超过源图尺寸（前提是宽或高只有一个）
	     */
	    if(($width> $src_w && $height> $src_h) || ($height> $src_h && $width == 0) || ($width> $src_w && $height == 0))
	    {
	        $proportion = 1;
	    }
	    if($width> $src_w)
	    {
	        $dst_w = $width = $src_w;
	    }
	    if($height> $src_h)
	    {
	        $dst_h = $height = $src_h;
	    }
	
	    if(!$width && !$height && !$proportion)
	    {
	        return false;
	    }
	    if(!$proportion)
	    {
	        if($cut == 0)
	        {
	            if($dst_w && $dst_h)
	            {
	                if($dst_w/$src_w> $dst_h/$src_h)
	                {
	                    $dst_w = $src_w * ($dst_h / $src_h);
	                    $x = 0 - ($dst_w - $width) / 2;
	                }
	                else
	                {
	                    $dst_h = $src_h * ($dst_w / $src_w);
	                    $y = 0 - ($dst_h - $height) / 2;
	                }
	            }
	            else if($dst_w xor $dst_h)
	            {
	                if($dst_w && !$dst_h)  //有宽无高
	                {
	                    $propor = $dst_w / $src_w;
	                    $height = $dst_h  = $src_h * $propor;
	                }
	                else if(!$dst_w && $dst_h)  //有高无宽
	                {
	                    $propor = $dst_h / $src_h;
	                    $width  = $dst_w = $src_w * $propor;
	                }
	            }
	        }
	        else
	        {
	            if(!$dst_h)  //裁剪时无高
	            {
	                $height = $dst_h = $dst_w;
	            }
	            if(!$dst_w)  //裁剪时无宽
	            {
	                $width = $dst_w = $dst_h;
	            }
	            $propor = min(max($dst_w / $src_w, $dst_h / $src_h), 1);
	            $dst_w = (int)round($src_w * $propor);
	            $dst_h = (int)round($src_h * $propor);
	            $x = ($width - $dst_w) / 2;
	            $y = ($height - $dst_h) / 2;
	        }
	    }
	    else
	    {
	        $proportion = min($proportion, 1);
	        $height = $dst_h = $src_h * $proportion;
	        $width  = $dst_w = $src_w * $proportion;
	    }
	
	    $src = $createfun($src_img);
	    $dst = imagecreatetruecolor($width ? $width : $dst_w, $height ? $height : $dst_h);
	    $white = imagecolorallocate($dst, 255, 255, 255);
	    imagefill($dst, 0, 0, $white);
	
	    if(function_exists('imagecopyresampled'))
	    {
	        imagecopyresampled($dst, $src, $x, $y, 0, 0, $dst_w, $dst_h, $src_w, $src_h);
	    }
	    else
	    {
	        imagecopyresized($dst, $src, $x, $y, 0, 0, $dst_w, $dst_h, $src_w, $src_h);
	    }
	    $otfunc($dst, $dst_img);
	    imagedestroy($dst);
	    imagedestroy($src);
	    return true;
	}

	function fileext($file)
	{
	    return pathinfo($file, PATHINFO_EXTENSION);
	}
	
	
	public static function getThumb($file=NULL){
		if (empty($file)){
			return false ;
		}
		$dirName = pathinfo($file, PATHINFO_DIRNAME) ;
		$baseName= pathinfo($file, PATHINFO_BASENAME);
		$ext     = pathinfo($file, PATHINFO_EXTENSION);
		if (empty($dirName) || $dirName == '.'){
			return '/'.str_replace(".".$ext,"", $baseName)."_thumb.".$ext ;
		}
		return $dirName.'/'.str_replace(".".$ext,"", $baseName)."_thumb.".$ext ;
	}
}