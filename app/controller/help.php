<?php
class Controller_Help extends Controller
{
	public function Action_index()
	{
		echo "<hr />";
		print_r($this->request->getQuery());
		echo "<hr />";
		$this->view->bb = "我是中国人，hello world";
	}

	
}