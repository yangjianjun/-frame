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


	'db'=>array(					    				//Database Link Settings
		'host'				=>'localhost',  			//Database host name or IP
		'user'				=>'root',					//User name
		'password'			=>'123456',					//Password
		'dbname'			=>'test',					//DBName
		'charset'			=>'utf8',					//charset
		'conmode'			=>false						//true to the long connection mode, false for the short connection mode
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