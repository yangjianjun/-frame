<?php 
class Common{
	

	/**
     * 
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
     * 
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
     * 
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
    	if (preg_match('/MSIE/i',$_SERVER['HTTP_USER_AGENT'])){
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
    	$config = Frame::getInstance()->config;
    	if (isset($config[$main]) && isset($config[$main][$sub])) return $config[$main][$sub];
    	if (isset($config[$main])) return $config[$main];
    	return $config;
    }
    
	/**
	 * URL重定向
	 * @param string $url 重定向的URL地址
	 * @param integer $time 重定向的等待时间（秒）
	 * @param string $msg 重定向前的提示信息
	 * @return void
	 */
	public static function redirect($url,$param=array() ,$time=0, $msg='') {
	    if (empty($url)){
	    	return false ;
	    }
		if (!empty($param)){
			$url.="?";
			
			foreach ($param as $k=>$v) {
				$url.=$k.'='.$v.'&';
			}
			$url[strlen($url)-1]=" ";
		}
	    
		
		
		//多行URL地址支持
	    $url        = str_replace(array("\n", "\r"), '', $url);
	    if (empty($msg))
	        $msg    = "系统将在{$time}秒之后自动跳转到{$url}！";
	    if (!headers_sent()) {
	        // redirect
	        if (0 === $time) {
	            header('Location: ' . $url);
	        } else {
	            header("refresh:{$time};url={$url}");
	            echo($msg);
	        }
	        exit();
	    } else {
	        $str    = "<meta http-equiv='Refresh' content='{$time};URL={$url}'>";
	        if ($time != 0)
	            $str .= $msg;
	        exit($str);
	    }
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
    public static $filterArray = array(
    	'alert',
    	'javascript',
    	'expression',
    );
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
					foreach (self::$filterArray as $vv) {
						$data[$k] =str_replace($vv,"", $data[$k]);
					}
					$data[$k] = addslashes($data[$k]);
				}
			}else {
				foreach (self::$filterArray as $vv) {
					$data =str_replace($vv,"", $data);
				}
				$data = addslashes($data);
			}
		}else {
			if (is_array($data)){
				foreach ($data as $k=>$v) {
					foreach (self::$filterArray as $vv) {
						$data[$k] =str_replace($vv,"", $data[$k]);
					}
				}
			}else {
				foreach (self::$filterArray as $vv) {
					$data =str_replace($vv,"", $data);
				}
			}
		}
		return $data;
	}
	
	
	public function setCache($id, $data, $lifetime = 3600){
	
	}

	public static function formRequest($url, $paramArr=array(),$method="post") {  
		if (empty($url)){
			return false;
		}
		$form= '<form id="formRequest" action="'.$url.'" method="'.$method.'"> ';
		if (!empty($paramArr)){
			foreach ($paramArr as $k=>$v) {
				$form.='<input type="hidden" name="'.$k.'" value="'.$v.'" />';
			}
		}
	  	$form.='</form>';
	  	$form.='<script>document.getElementById("formRequest").submit();</script>';
	  	echo $form ;
	}  
	
	//生成下拉框
	public static function getSelect($name=null,$data=array(),$param=array()){
		//验证
		if (empty($data) || empty($name)){
			return ;
		}
		//生成select 下拉框
		$selectedstr=NULL;
		$emptyStr = isset($param['msg'])?$param['msg']:'请选择';
		$classStr = isset($param['class'])? 'class="'.$param['class'].'"':'';
		$styleStr = isset($param['style'])? 'style="'.$param['style'].'"':'';
		$select = '<select name="'.$name.'" '.$classStr.' '.$styleStr.' ><option value="" >'.$emptyStr.'</option>';
	    
		foreach ($data as $k=>$o) {
			if (is_array($o)){
				$selectedstr = ($o["id"] == $param['id'])?'selected':'' ;
				$select.="<option $selectedstr value='".$o['id']."' >".$o['name']."</option>";
			}else {
				$selectedstr = ( $param['id'] !== "" && $k == $param['id']  )?'selected':'' ;
				$select.="<option $selectedstr value='".$k."' >".$o."</option>";
			}
		}
		$select.='</select>';
		//返回数据
		return $select ;
	}

	//截取utf8字符串
	public static function  utf8Substr($str, $from, $len)
	{
	    return preg_replace('#^(?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,'.$from.'}'.
	                       '((?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,'.$len.'}).*#s',
	                       '$1',$str);
	}
	
	
    static function buildImageVerify($length=4,$mode=1,$type='png',$width=48,$height=22,$verifyName='verify')
    {
	
        $randval = String::randString($length,$mode);
        $_SESSION[$verifyName]= $randval;
        $width = ($length*10+10)>$width?$length*10+10:$width;
        if ( $type!='gif' && function_exists('imagecreatetruecolor')) {
            $im = @imagecreatetruecolor($width,$height);
        }else {
            $im = @imagecreate($width,$height);
        }
        $r = Array(225,255,255,223);
        $g = Array(225,236,237,255);
        $b = Array(225,236,166,125);
        $key = mt_rand(0,3);

        $backColor = imagecolorallocate($im, 220,220,220);    //背景色（随机）
		$borderColor = imagecolorallocate($im, 192, 192, 192);                    //边框色
        $pointColor = imagecolorallocate($im,mt_rand(0,255),mt_rand(0,255),mt_rand(0,255));                 //点颜色

        @imagefilledrectangle($im, 0, 0, $width - 1, $height - 1, $backColor);
        @imagerectangle($im, 0, 0, $width-1, $height-1, $borderColor);
        $stringColor = imagecolorallocate($im,mt_rand(0,200),mt_rand(0,120),mt_rand(0,120));
		// 干扰
//		for($i=0;$i<10;$i++){
//			$fontcolor=imagecolorallocate($im,mt_rand(0,255),mt_rand(0,255),mt_rand(0,255));
//			imagearc($im,mt_rand(-10,$width),mt_rand(-10,$height),mt_rand(30,300),mt_rand(20,200),55,44,$fontcolor);
//		}
//		for($i=0;$i<25;$i++){
//			$fontcolor=imagecolorallocate($im,mt_rand(0,255),mt_rand(0,255),mt_rand(0,255));
//			imagesetpixel($im,mt_rand(0,$width),mt_rand(0,$height),$pointColor);
//		}
		for($i=0;$i<$length;$i++) {
			imagestring($im,5,$i*12+5,mt_rand(5,6),$randval{$i}, $stringColor);
		}
//        @imagestring($im, 5, 5, 3, $randval, $stringColor);
        self::output($im,$type);
    }
    
    static function output($im,$type='png',$filename='')
    {
        header("Content-type: image/".$type);
        $ImageFun='image'.$type;
		if(empty($filename)) {
	        $ImageFun($im);
		}else{
	        $ImageFun($im,$filename,100);
		}
        imagedestroy($im);
    }

	static function Pinyin($_String, $_Code='1'){
		$_DataKey = "a|ai|an|ang|ao|ba|bai|ban|bang|bao|bei|ben|beng|bi|bian|biao|bie|bin|bing|bo|bu|ca|cai|can|cang|cao|ce|ceng|cha".
		"|chai|chan|chang|chao|che|chen|cheng|chi|chong|chou|chu|chuai|chuan|chuang|chui|chun|chuo|ci|cong|cou|cu|".
		"cuan|cui|cun|cuo|da|dai|dan|dang|dao|de|deng|di|dian|diao|die|ding|diu|dong|dou|du|duan|dui|dun|duo|e|en|er".
		"|fa|fan|fang|fei|fen|feng|fo|fou|fu|ga|gai|gan|gang|gao|ge|gei|gen|geng|gong|gou|gu|gua|guai|guan|guang|gui".
		"|gun|guo|ha|hai|han|hang|hao|he|hei|hen|heng|hong|hou|hu|hua|huai|huan|huang|hui|hun|huo|ji|jia|jian|jiang".
		"|jiao|jie|jin|jing|jiong|jiu|ju|juan|jue|jun|ka|kai|kan|kang|kao|ke|ken|keng|kong|kou|ku|kua|kuai|kuan|kuang".
		"|kui|kun|kuo|la|lai|lan|lang|lao|le|lei|leng|li|lia|lian|liang|liao|lie|lin|ling|liu|long|lou|lu|lv|luan|lue".
		"|lun|luo|ma|mai|man|mang|mao|me|mei|men|meng|mi|mian|miao|mie|min|ming|miu|mo|mou|mu|na|nai|nan|nang|nao|ne".
		"|nei|nen|neng|ni|nian|niang|niao|nie|nin|ning|niu|nong|nu|nv|nuan|nue|nuo|o|ou|pa|pai|pan|pang|pao|pei|pen".
		"|peng|pi|pian|piao|pie|pin|ping|po|pu|qi|qia|qian|qiang|qiao|qie|qin|qing|qiong|qiu|qu|quan|que|qun|ran|rang".
		"|rao|re|ren|reng|ri|rong|rou|ru|ruan|rui|run|ruo|sa|sai|san|sang|sao|se|sen|seng|sha|shai|shan|shang|shao|".
		"she|shen|sheng|shi|shou|shu|shua|shuai|shuan|shuang|shui|shun|shuo|si|song|sou|su|suan|sui|sun|suo|ta|tai|".
		"tan|tang|tao|te|teng|ti|tian|tiao|tie|ting|tong|tou|tu|tuan|tui|tun|tuo|wa|wai|wan|wang|wei|wen|weng|wo|wu".
		"|xi|xia|xian|xiang|xiao|xie|xin|xing|xiong|xiu|xu|xuan|xue|xun|ya|yan|yang|yao|ye|yi|yin|ying|yo|yong|you".
		"|yu|yuan|yue|yun|za|zai|zan|zang|zao|ze|zei|zen|zeng|zha|zhai|zhan|zhang|zhao|zhe|zhen|zheng|zhi|zhong|".
		"zhou|zhu|zhua|zhuai|zhuan|zhuang|zhui|zhun|zhuo|zi|zong|zou|zu|zuan|zui|zun|zuo";
		$_DataValue = "-20319|-20317|-20304|-20295|-20292|-20283|-20265|-20257|-20242|-20230|-20051|-20036|-20032|-20026|-20002|-19990".
		"|-19986|-19982|-19976|-19805|-19784|-19775|-19774|-19763|-19756|-19751|-19746|-19741|-19739|-19728|-19725".
		"|-19715|-19540|-19531|-19525|-19515|-19500|-19484|-19479|-19467|-19289|-19288|-19281|-19275|-19270|-19263".
		"|-19261|-19249|-19243|-19242|-19238|-19235|-19227|-19224|-19218|-19212|-19038|-19023|-19018|-19006|-19003".
		"|-18996|-18977|-18961|-18952|-18783|-18774|-18773|-18763|-18756|-18741|-18735|-18731|-18722|-18710|-18697".
		"|-18696|-18526|-18518|-18501|-18490|-18478|-18463|-18448|-18447|-18446|-18239|-18237|-18231|-18220|-18211".
		"|-18201|-18184|-18183|-18181|-18012|-17997|-17988|-17970|-17964|-17961|-17950|-17947|-17931|-17928|-17922".
		"|-17759|-17752|-17733|-17730|-17721|-17703|-17701|-17697|-17692|-17683|-17676|-17496|-17487|-17482|-17468".
		"|-17454|-17433|-17427|-17417|-17202|-17185|-16983|-16970|-16942|-16915|-16733|-16708|-16706|-16689|-16664".
		"|-16657|-16647|-16474|-16470|-16465|-16459|-16452|-16448|-16433|-16429|-16427|-16423|-16419|-16412|-16407".
		"|-16403|-16401|-16393|-16220|-16216|-16212|-16205|-16202|-16187|-16180|-16171|-16169|-16158|-16155|-15959".
		"|-15958|-15944|-15933|-15920|-15915|-15903|-15889|-15878|-15707|-15701|-15681|-15667|-15661|-15659|-15652".
		"|-15640|-15631|-15625|-15454|-15448|-15436|-15435|-15419|-15416|-15408|-15394|-15385|-15377|-15375|-15369".
		"|-15363|-15362|-15183|-15180|-15165|-15158|-15153|-15150|-15149|-15144|-15143|-15141|-15140|-15139|-15128".
		"|-15121|-15119|-15117|-15110|-15109|-14941|-14937|-14933|-14930|-14929|-14928|-14926|-14922|-14921|-14914".
		"|-14908|-14902|-14894|-14889|-14882|-14873|-14871|-14857|-14678|-14674|-14670|-14668|-14663|-14654|-14645".
		"|-14630|-14594|-14429|-14407|-14399|-14384|-14379|-14368|-14355|-14353|-14345|-14170|-14159|-14151|-14149".
		"|-14145|-14140|-14137|-14135|-14125|-14123|-14122|-14112|-14109|-14099|-14097|-14094|-14092|-14090|-14087".
		"|-14083|-13917|-13914|-13910|-13907|-13906|-13905|-13896|-13894|-13878|-13870|-13859|-13847|-13831|-13658".
		"|-13611|-13601|-13406|-13404|-13400|-13398|-13395|-13391|-13387|-13383|-13367|-13359|-13356|-13343|-13340".
		"|-13329|-13326|-13318|-13147|-13138|-13120|-13107|-13096|-13095|-13091|-13076|-13068|-13063|-13060|-12888".
		"|-12875|-12871|-12860|-12858|-12852|-12849|-12838|-12831|-12829|-12812|-12802|-12607|-12597|-12594|-12585".
		"|-12556|-12359|-12346|-12320|-12300|-12120|-12099|-12089|-12074|-12067|-12058|-12039|-11867|-11861|-11847".
		"|-11831|-11798|-11781|-11604|-11589|-11536|-11358|-11340|-11339|-11324|-11303|-11097|-11077|-11067|-11055".
		"|-11052|-11045|-11041|-11038|-11024|-11020|-11019|-11018|-11014|-10838|-10832|-10815|-10800|-10790|-10780".
		"|-10764|-10587|-10544|-10533|-10519|-10331|-10329|-10328|-10322|-10315|-10309|-10307|-10296|-10281|-10274".
		"|-10270|-10262|-10260|-10256|-10254";
		$_TDataKey = explode('|', $_DataKey);
		$_TDataValue = explode('|', $_DataValue);
		$_Data = (PHP_VERSION>='5.0') ? array_combine($_TDataKey, $_TDataValue) : self::_Array_Combine($_TDataKey, $_TDataValue);
		arsort($_Data);
		reset($_Data);
		if($_Code != 'gb2312') $_String = self::_U2_Utf8_Gb($_String);
		$_Res = '';
		for($i=0; $i<strlen($_String); $i++)
		{
		$_P = ord(substr($_String, $i, 1));
		if($_P>160) { $_Q = ord(substr($_String, ++$i, 1)); $_P = $_P*256 + $_Q - 65536; }
		$_Res .= self::_Pinyin($_P, $_Data);
		}
		return preg_replace("/[^a-z0-9]*/", '', $_Res);
	}
	static function _Pinyin($_Num, $_Data){
		if ($_Num>0 && $_Num<160 ) return chr($_Num);
		elseif($_Num<-20319 || $_Num>-10247) return '';
		else {
		foreach($_Data as $k=>$v){ if($v<=$_Num) break; }
		return $k;
		}
	} 
	static function _U2_Utf8_Gb($_C){
		$_String = '';
		if($_C < 0x80) $_String .= $_C;
		elseif($_C < 0x800)
		{
		$_String .= chr(0xC0 | $_C>>6);
		$_String .= chr(0x80 | $_C & 0x3F);
		}elseif($_C < 0x10000){
		$_String .= chr(0xE0 | $_C>>12);
		$_String .= chr(0x80 | $_C>>6 & 0x3F);
		$_String .= chr(0x80 | $_C & 0x3F);
		} elseif($_C < 0x200000) {
		$_String .= chr(0xF0 | $_C>>18);
		$_String .= chr(0x80 | $_C>>12 & 0x3F);
		$_String .= chr(0x80 | $_C>>6 & 0x3F);
		$_String .= chr(0x80 | $_C & 0x3F);
		}
		return iconv('UTF-8', 'GB2312', $_String);
	}
	static function _Array_Combine($_Arr1, $_Arr2){
		for($i=0; $i<count($_Arr1); $i++) $_Res[$_Arr1[$i]] = $_Arr2[$i];
		return $_Res;
	}
}
