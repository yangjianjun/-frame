<?php
class Controller_User extends Controller
{
	public function Action_login(){
		
		if ($this->request->isPost()){
			$data = $this->request->getPost();
			
			$user = new Model_User();
			unset($data['sub']);
			echo $user->insert($data);
		}
	}
	public function Action_index(){
		
	}
	

}