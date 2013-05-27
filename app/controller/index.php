<?php
class Controller_Index extends Controller
{
	public function Action_index()
	{
		$this->view->bb = "我是中国人，hello world";
	}
	
	public function Action_test()
	{
		$this->setLayout("layout");
		$this->view->bb = "mysss";
		$this->view->arr = array('one'=>'1223','two'=>'45454');
//		$this->render("index/index");
	}
	
}