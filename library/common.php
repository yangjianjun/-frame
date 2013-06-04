<?php 
class Common{
	

	/**
     * @author guopingleee
	 * @Encrypt Function (编码)
     * @createTime: 2011-12-16
     */
	public static function encode($encrypt)
	{
		$deskey = "12345678";
		$iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND);
		$passcrypt = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $deskey, $encrypt, MCRYPT_MODE_ECB, $iv);
		$encode = base64_encode($passcrypt);
		return $encode;
	}

	/**
     * @author guopingleee
	 * @Decrypt Function (解码)
	 * @createTime: 2011-12-16
     */
	public static function decode($decrypt)
	{
		$deskey = "12345678";
		$decoded = base64_decode($decrypt);
		$iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND);
		$decrypted = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $deskey, $decoded, MCRYPT_MODE_ECB, $iv);
		return $decrypted;
	}


	/**
     * @author zhengshufa
     * @todo: format date time
     * @createTime: 2009-09-10
     */
    public static function formatDate($dateTime, $type=1, $today='', $yesterday='')
    {
        if ($today != '') {
            $date = substr($dateTime,0,10);  
            if($date == $today) return '今天';
            if($date == $yesterday) return '昨天';
        }
        $typeArr = array(1=>'Y年m月d日',2=>'y年m月d日',3=>'m月d日',4=>'m月d',5=>'m月d日 H:i');
        $timeArr = explode(' ', $dateTime);
        $tArr1   = explode('-', $timeArr[0]);
        $tArr2   = explode(':', $timeArr[1]);
        return str_replace(array('Y','m','d','H','i'),array($tArr1[0],$tArr1[1],$tArr1[2],$tArr2[0],$tArr2[1]),$typeArr[$type]);
    }
    
    /**
	 * get client ip address
	 * @return String: $ipAddress
	 */
    public static function getIp(){
        if (isset($_SERVER['HTTP_CDN_SRC_IP']) && $_SERVER['HTTP_CDN_SRC_IP'] && strcasecmp($_SERVER['HTTP_CDN_SRC_IP'], "unknown")){
            $ip = $_SERVER['HTTP_CDN_SRC_IP'];
        }elseif(getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown")){
            $ip = getenv("HTTP_CLIENT_IP");
        }else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown")){
            $ip = getenv("HTTP_X_FORWARDED_FOR");
        }else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown")){
            $ip = getenv("REMOTE_ADDR");
        }else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown")){
            $ip = $_SERVER['REMOTE_ADDR'];
        }else{
            $ip = "unknown";
        }
        return($ip);
    }

    /**
	 * get city and Lan from ip address
	 * @return String: city and Lan
	 */
    public static function convertip($ip) {

        $return = '';

        if(preg_match("/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/", $ip)) {

            $iparray = explode('.', $ip);

            if($iparray[0] == 10 || $iparray[0] == 127 || ($iparray[0] == 192 && $iparray[1] == 168) || ($iparray[0] == 172 && ($iparray[1] >= 16 && $iparray[1] <= 31))) {
                $return = '- 本地';
            } elseif($iparray[0] > 255 || $iparray[1] > 255 || $iparray[2] > 255 || $iparray[3] > 255) {
                $return = '- 无效IP地址';
            } else {
                $tinyipfile = './ipdata/tinyipdata.dat';
                if(@file_exists($tinyipfile)) {
                    $return = common::convertip_tiny($ip, $tinyipfile);
                } else{
                    $return = '- IP地址文件不存在';
                }
            }
        }

        return $return;

    }

    /**
	 * get city from ipdata file
	 * @return String: city and Lan
	 */
    public static function convertip_tiny($ip, $ipdatafile) {

        static $fp = NULL, $offset = array(), $index = NULL;

        $ipdot = explode('.', $ip);
        $ip    = pack('N', ip2long($ip));

        $ipdot[0] = (int)$ipdot[0];
        $ipdot[1] = (int)$ipdot[1];

        if($fp === NULL && $fp = @fopen($ipdatafile, 'rb')) {
            $offset = unpack('Nlen', fread($fp, 4));
            $index  = fread($fp, $offset['len'] - 4);
        } elseif($fp == FALSE) {
            return  '- 无效IP地址文件';
        }

        $length = $offset['len'] - 1028;
        $start  = unpack('Vlen', $index[$ipdot[0] * 4] . $index[$ipdot[0] * 4 + 1] . $index[$ipdot[0] * 4 + 2] . $index[$ipdot[0] * 4 + 3]);

        for ($start = $start['len'] * 8 + 1024; $start < $length; $start += 8) {

            if ($index{$start} . $index{$start + 1} . $index{$start + 2} . $index{$start + 3} >= $ip) {
                $index_offset = unpack('Vlen', $index{$start + 4} . $index{$start + 5} . $index{$start + 6} . "\x0");
                $index_length = unpack('Clen', $index{$start + 7});
                break;
            }
        }

        fseek($fp, $offset['len'] + $index_offset['len'] - 1024);
        if($index_length['len']) {

            return '- '.fread($fp, $index_length['len']);
        } else {
            return '- 未知IP';
        }
    }
    
    /**
	 * js信息提示
	 * @param string $message:提示信息
	 */
    public static function jsTip($message, $url = ''){
        header('Content-Type:text/html; charset=utf-8');
        echo "<script type='text/javascript'>alert('$message');";
        if ($url){
        	echo "window.location='$url';";
        }else {
        	echo "window.history.go(-1);";
        }
        echo "</script>";
        exit;
    }
    
    /**
	 * 执行404错误页面
	 */
    public static function go404($message = ''){
    	header("HTTP/1.1 404 Not Found");
    	header("Status: 404 Not Found");
    	header("Content-Type: text/html; charset=UTF-8");
    	if (preg_match('/MSIE/i',input::server('HTTP_USER_AGENT'))){
    		echo str_repeat(" ",512);
    	}
    	echo 'this 404 page <br />';
    	echo $message;
    	exit;
    }
    
    /**
     * 创建目录
     */
    public static function createDir($dir)
    {
    	$oldmask=umask(0);
    	mkdir($dir, 0755);
    	umask($oldmask);
    }
    
    /**
     * 读取配置
     */
    public static function config($str)
    {
    	list($main, $sub) = explode('.', $str);
    	if (isset($GLOBALS['config'][$main]) && isset($GLOBALS['config'][$main][$sub])) return $GLOBALS['config'][$main][$sub];
    	if (isset($GLOBALS['config'][$main])) return $GLOBALS['config'][$main];
    	return $GLOBALS['config'];
    }
    
    /**
	 * 重定向
	 *
	 * @param  mixed   string site URI or URL to redirect to, or array of strings if method is 300
	 * @param  string  HTTP method of redirect
	 * @return void
	 */
	public static function redirect($uri = '', $method = '302')
	{
		$codes = array
		(
			'refresh' => 'Refresh',
			'301' => 'Moved Permanently',
			'302' => 'Found',
			'303' => 'See Other',
			'304' => 'Not Modified',
			'305' => 'Use Proxy',
			'307' => 'Temporary Redirect'
		);
		// Validate the method and default to 302
		$method = isset($codes[$method]) ? (string) $method : '302';

		if (strpos($uri, '://') === FALSE){
			$uri = input::uri('base').$uri;
		}
		if ($method === 'refresh'){
			header('Refresh: 0; url='.$uri);
		} else{
			header('HTTP/1.1 '.$method.' '.$codes[$method]);
			header('Location: '.$uri);
		}
		exit('<h1>'.$method.' - '.$codes[$method].'</h1>');
	}
    
	/**
	 * 请求URL-PHP程序接口
	 * @param string:$url: 请求接口地址
	 * @param $params:string/array 参数
	 * @param $post: bool:是否使用post提交
	 */
    public static function curlExec($url, $params, $post = false){
        $ch = curl_init( );
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
        if (substr($url, 0, 7) == 'http://'){
            curl_setopt( $ch, CURLOPT_URL, $url );
        }else {
            curl_setopt( $ch, CURLOPT_URL, input::uri('base').$url );
        }
		if ($post) curl_setopt( $ch, CURLOPT_POST, 1 );
		if (is_string($params)){
		  curl_setopt( $ch, CURLOPT_POSTFIELDS, $params );
		}else {
		    $tempArr = array();
		    foreach ($params as $k=>$v){
		        $tempArr[] = $k.'='.urlencode($v);
		    }
		    curl_setopt( $ch, CURLOPT_POSTFIELDS, join('&', $tempArr) );
		}
		curl_setopt( $ch, CURLOPT_TIMEOUT, 10 );
		ob_start( );
		curl_exec( $ch );
		$contents = ob_get_contents( );
		ob_end_clean( );
		curl_close( $ch );
        return $contents;
    }
	//input data secure Filter
	public static function secureFilter($data=null){
		if (empty($data)){
			return false ;
		}
		//Proposed transfer
		if (version_compare(phpversion(), '5.4.0',">=")
		 || !get_magic_quotes_gpc()){
			if (is_array($data)){
				foreach ($data as $k=>$v) {
					$data[$k] = addslashes($data[$k]);
				}
			}else {
				$data = addslashes($data);
			}
		}
		return $data;
	}
}
