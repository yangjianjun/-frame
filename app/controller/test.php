<?php
class Controller_Test extends Controller
{
	public function Action_index()
	{
		print_r($this->request->getQuery());
	}
	public function Action_test()
	{	
		print_r($this->request->getQuery());
		exit ("KK");
	}
	
}