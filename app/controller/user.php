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
		$user = new Model_User();
//		//insert 
////		echo $user->insert(array('name'=>$name,'passwd'=>'123'));
//		//update 
//		$name= 'a';
//		$where = "name='$name' ";
//		//echo $user->update(array('name'=>$name,'passwd'=>'33'),$where);
//		
//		$user->delete($where);
		print_r($user->fetchRow());
		$this->view->data = $user->fetchAll();
		
	}
	

}