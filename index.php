<?php
/*
 * Unified import documents
 *
 *
 * File Extension
 */
define('EXT', '.php');
/*
 * Define the root of the site
 */
defined('ROOT') or define('ROOT', dirname(__FILE__).DIRECTORY_SEPARATOR);

/**Web Directory Structure
 * app			  	Where the application file path (the user can not directly access)
 * controller  		Where the controller class file path(app/controller)
 * model     		Class file path where the application model(app/model)
 * view  			Template file path where(app/view)
 * config.php		Site preparation global file(app/config.php)
 * bootstrap.php   	Website bootstrap class files(app/bootstrap.php)
 *  	
 * library   		Site library path to the folder where the
 * third			Importing third-party library folder path where
 * public           Public documents the path (such as the user direct access to the css, js, images)
 * js				js path (public / js)
 * css  			css path (public/css)
 * images			images path(public/images)
 * upload			Website where the uploaded file path
 * logs             Web logs
 * cache            File Cache website
 */
defined('APP_PATH')  or define('APP_PATH',  ROOT.'app'.  		DIRECTORY_SEPARATOR);
defined('LIB_PATH')  or define('LIB_PATH',  ROOT.'library'.		DIRECTORY_SEPARATOR);
defined('THI_PATH')  or define('THI_PATH',	ROOT.'third'.		DIRECTORY_SEPARATOR);
defined('PUB_PATH')  or define('PUB_PATH',	ROOT.'public'.		DIRECTORY_SEPARATOR);
defined('UPL_PATH')  or define('UPL_PATH',	ROOT.'upload'.		DIRECTORY_SEPARATOR);
defined('LOG_PATH')  or define('LOG_PATH',	ROOT.'logs'.		DIRECTORY_SEPARATOR);
defined('CAC_PATH')  or define('CAC_PATH',	ROOT.'cache'.		DIRECTORY_SEPARATOR);


define('CON_PATH', APP_PATH.'controller'.	DIRECTORY_SEPARATOR);
define('MOD_PATH', APP_PATH.'model'.		DIRECTORY_SEPARATOR);
define('VIE_PATH', APP_PATH.'view'.			DIRECTORY_SEPARATOR);

/*
 * Loading boot class files
 * */
require_once APP_PATH.'bootstrap.php';
$bootstrap =new bootstrap() ;
$bootstrap->dispatch();

//end