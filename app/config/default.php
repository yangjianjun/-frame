<?php
/*
 * Website Global configuration file
 * */
return  array(
	'controller'			=>'index',  				//Default controller
	'action'				=>'index',  				//Default action
	'autoLayout'			=> true,  				//Default Layout
	'layout'				=>'layout',  				//Default Layout

	'openperformance'		=>false,  					//Open Performance Testing
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
	'upload'				=>array(
									'direct'			=>'upload',
									'size'				=>2097152    				//2M
	),
	'page'					=>array(
									'psize'				=>10,						//The number of records per page
									'pnum'				=>5							//Page Offset
	),
	
	
	'cache'					=>array(
									array(	
										'host'             	=> '10.41.134.190',
										'port'             	=> 10000,
										'persistent'       	=> FALSE,
										'weight'           	=> 1,
										'timeout'          	=> 1,
										'retry_interval'   	=> 15,
										'status'           	=> TRUE,
										'instant_death'	   	=> TRUE,
										'failure_callback' 	=> null
									),
									array(	
										'host'             	=> '10.41.134.190',
										'port'             	=> 10001,
										'persistent'       	=> FALSE,
										'weight'           	=> 1,
										'timeout'          	=> 1,
										'retry_interval'   	=> 15,
										'status'           	=> TRUE,
										'instant_death'	   	=> TRUE,
										'failure_callback' 	=> null
									),
									array(	
										'host'             	=> '10.41.134.190',
										'port'             	=> 10002,
										'persistent'       	=> FALSE,
										'weight'           	=> 1,
										'timeout'          	=> 1,
										'retry_interval'   	=> 15,
										'status'           	=> TRUE,
										'instant_death'	   	=> TRUE,
										'failure_callback' 	=> null
									)
	),
	'timezone'				=>'Asia/Shanghai'
);