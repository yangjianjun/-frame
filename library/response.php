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
			Header("content-type","text/html;charset=utf-8");
		}
		return $this ;
	}
}