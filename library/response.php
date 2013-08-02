<?php
/*
 * Basic data response class
 * */
class Response 
{
	public $body = null;
	
	public function output($render=true)
	{
		if ($render){
			echo  $this->body;
		}else {
			return $this->body ;
		}
	}
	
	public function setHeader($key = NULL, $value = NULL)
	{
		if (is_string($key)){
			header("{$key}:{$value}");
		}else if (is_array($key)){
			foreach ($key as $k=>$v) {
				header("{$k}:{$v}");
			}
		}else {
			header("content-type","text/html;charset=utf-8");
		}
		//header("from:".apache_get_version().",ip=".apache_getenv("server_addr"));
		return $this ;
	}
	/**
     * Returns an array with all the variables in the session, fetching them
     */
	public function session($key = '', $value = '')
	{
		if ($value !== '') $_SESSION[$key] = $value;
		if ($key) return @$_SESSION[$key];
		return $_SESSION;
	}

	/**
     * Returns an array with the contents of the $_COOKIE global variable
     */
	public function cookie($key = '', $value = '')
	{
		if ($value !== '') $_COOKIE[$key] = $value;
		if ($key) return @$_COOKIE[$key];
		return $_COOKIE;
	}
}