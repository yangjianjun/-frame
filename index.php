<?php
/*
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
 * config		    Site preparation config directly(app/config)
 * 
 * backend          diary statistics and cache
 * 
 * 
 * logs             Web logs(app/logs)
 * cache            File Cache website(app/cache)
 * 
 * bootstrap.php   	Website bootstrap class files(app/bootstrap.php)
 *  	
 * library   		Site library path to the folder where the
 * public           Public documents the path (such as the user direct access to the css, js, images)
 * js				js path (public / js)
 * css  			css path (public/css)
 * images			images path(public/images)
 * upload			Website where the uploaded file path
 */

defined('APP_PATH')  or define('APP_PATH',  ROOT.'app'.  		    DIRECTORY_SEPARATOR);
defined('CON_PATH')  or define('CON_PATH',  APP_PATH.'controller'.	DIRECTORY_SEPARATOR);
defined('MOD_PATH')  or define('MOD_PATH',  APP_PATH.'model'.		DIRECTORY_SEPARATOR);
defined('VIE_PATH')  or define('VIE_PATH',  APP_PATH.'view'.		DIRECTORY_SEPARATOR);
defined('LIB_PATH')  or define('LIB_PATH',  ROOT.'library'.			DIRECTORY_SEPARATOR);
defined('BAC_PATH')  or define('BAC_PATH',	ROOT.'backend'.			DIRECTORY_SEPARATOR);
defined('LOG_PATH')  or define('LOG_PATH',	BAC_PATH.'logs'.		DIRECTORY_SEPARATOR);
defined('CAC_PATH')  or define('CAC_PATH',	BAC_PATH.'cache'.		DIRECTORY_SEPARATOR);
defined('PUB_PATH')  or define('PUB_PATH',	ROOT.'public'.			DIRECTORY_SEPARATOR);
defined('UPL_PATH')  or define('UPL_PATH',	ROOT.'upload'.			DIRECTORY_SEPARATOR);

/*
 * Loading boot class files
 * */
require_once APP_PATH.'bootstrap.php';
$bootstrap =new bootstrap() ;
$bootstrap->dispatch();

//end
