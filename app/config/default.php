<?php
/*
 * Website Global configuration file
 * */
return  array(
	'controller'			=>'index',  				//Default controller
	'action'				=>'index',  				//Default action
	'layout'				=>'layout',  				//Default Layout

	'openperformance'		=>true,  					//Open Performance Testing
	'errorHandler'			=>'myErrorHandler',  		// error function
	'exceptionHandler'		=>'myExceptionHandler',  	// exception function

	'baseUrl'				=>'/public',  				// front base url

	'urlRule'               =>2 ,                       //url Rule 1 is: controller/action/parameter1/value1/parameter2/value2...
														//2 is: controller/action[/]?Parameter1=value1&Parameter2=value2...
	'db'					=>array(
								array(					    					    //if db number over one default first is master
									'host'				=>'10.41.134.190',  		//Database host name or IP
									'user'				=>'yjj',					//User name
									'password'			=>'000000',					//Password
									'dbname'			=>'stu',					//DBName
									'charset'			=>'utf8',					//charset
									'conmode'			=>false						//true to the long connection mode, false for the short connection mode
								),
								array(					    			
									'host'				=>'10.41.134.250',  		//Database host name or IP
									'user'				=>'yjj',					//User name
									'password'			=>'000000',					//Password
									'dbname'			=>'stu',					//DBName
									'charset'			=>'utf8',					//charset
									'conmode'			=>false						//true to the long connection mode, false for the short connection mode
								),
								array(					    			
									'host'				=>'10.41.134.251',  		//Database host name or IP
									'user'				=>'yjj',					//User name
									'password'			=>'000000',					//Password
									'dbname'			=>'stu',					//DBName
									'charset'			=>'utf8',					//charset
									'conmode'			=>false						//true to the long connection mode, false for the short connection mode
								),
	),
	'upload'=>array(
		'direct'			=>'upload',
		'size'				=>2097152    				//2M
	),
	'page'=>array(
		'psize'				=>10,						//The number of records per page
		'pnum'				=>5							//Page Offset
	),
	'cache'=>array(
		'driver'		   	=>"memcache",				//apc //file
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