<?php
class Controller_Index extends Controller
{
	public function Action_index()
	{
		echo "OK";
		header("Status: 404 Not Found"); 
		$this->view->cc = "我是中国人，hello world";
	}
	
	public function Action_test()
	{
		$this->setLayout("layout");
		$this->view->bb = "mysss";
		$this->view->arr = array('one'=>'1223','two'=>'45454');
//		$this->render("index/index");
	}
	
}