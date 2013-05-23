<?php
class Controller_Test extends Controller
{
	protected $template = 'layout';
	public function Action_index()
	{
		$view = View::instance("index/index");
		$view->test = "test";
		$this->template->content = $view->render(false) ;
		$this->template->render(true);
	}
	
	
}