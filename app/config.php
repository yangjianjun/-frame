<?php
/*
 * 网站全局配制文件
 * */
return  array(
	'controller'			=>'index',  	//默认控制器
	'action'				=>'index',  	//默认控制器
	'layout'				=>'layout',  	//默认布局
	'openperformance'		=>true,  		//开启性能检测
	'db'=>array(					    	//数据库链接设置
		'host'				=>'localhost',  //数据库主机名或IP
		'user'				=>'root',		//用户名
		'password'			=>'123456',		//密码
		'dbname'			=>'test',		//数据库名称
		'charset'			=>'utf8',		//字符集
		'conmode'			=>false			//true为长久连接模式，false为短暂连接模式
	),
	'upload'=>array(
		'direct'			=>'upload',
		'size'				=>2097152    	//2M
	),
	'page'=>array(
		'psize'				=>10,			//每页显示的记录数
		'pnum'				=>5				//页码偏移量
	),
	'cache'=>array(
		'driver'		   	=>"memcache",//apc //file
		'host'             	=> '192.168.1.62',
		'port'             	=> 11211,
		'persistent'       	=> FALSE,
		'weight'           	=> 1,
		'timeout'          	=> 1,
		'retry_interval'   	=> 15,
		'status'           	=> TRUE,
		'instant_death'	   	=> TRUE,
		'failure_callback' 	=> null
	),
	'core'=>array('now'		=>date('Y-m-d H:i:s'))
);