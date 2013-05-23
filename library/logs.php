<?php
//defined('SYSPATH') OR die('No direct access allowed.');
/**
 * 日志记录信息
 * this is a static class
 * 
 * @package    Core
 * @author     zhengshufa
 */
class logs{

    /**
     * 防止实例化
     *
     */
    private function __construct(){}
	
    public static $oper_type = array(1=>'查看',2=>"添加",3=>"修改",4=>"删除",5=>"同步",6=>"搜索",8=>"导出",9=>'登陆');
  
    /**
     * 日志记录
     */
    public static function setlogs($oper_type=null,$comment=null, $campaignId=0, $adgroupId=0){
    	
    	$logsmodel = new Logs_Model();

    	$adminInfo = input::session('adminInfo');
		$adminId =$adminInfo['id'];
		$loginDate = input::session('loginDate');
		$ip = common::getIp();
		
    	$form['manage_id']=$adminId;
    	$form['adminname']=$adminInfo["account"];
    	$form['logdate']= $loginDate;
    	$form["loginip"] = $ip;
    	$form['logtype']=$oper_type;
    	$form['dateandtime'] = date('Y-m-d H:i:s');
    	
    	 $defaultAccount = input::session('defaultAccount');
      	$form["accountName"] =isset($defaultAccount['accountName'])?$defaultAccount['accountName']:'';
    	$form['campaignId'] = $campaignId;
    	$form['adgroupId'] = $adgroupId;  	    	
 
    	$form['sql']=$form['adminname'].logs::$oper_type[$oper_type].$comment;
    	
    	$logsmodel->create($form);
    }
    
 
}    