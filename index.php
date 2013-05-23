<?php
/*
 * 统一入口文件
 *
 *
 * 文件扩展名
 */
define('EXT', '.php');
/*
 * 定义网站根目录
 */
defined('ROOT') or define('ROOT', dirname(__FILE__).DIRECTORY_SEPARATOR);

/**网站目录结构
 * app			  	应用程序文件所在路径（用户不能直接访问）
 *  	controller  	控制器类文件所在路径(app/controller)
 *  	model     		应用模型类文件所在路径(app/model)
 *  	view  			模板文件所在路径(app/view)
 *  	config.php		网站配制全局文件(app/config.php)
 *  	bootstrap.php   网站引导类文件(app/bootstrap.php)
 *  	
 * library   		本站类库文件夹所在路径
 * third			导入的第三方库文件夹所在路径
 * public           公用文件所在路径（用户直接访问如css,js,images）
 *  	js				js路径(public/js)
 *  	css  			css路径public/css)
 *  	images			images路径public/images)
 * upload			网站上传文件所在路径
 * logs             网站logs
 * cache            网站文件缓存
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
 * 加载引导类文件
 * */
require_once APP_PATH.'bootstrap.php';
$bootstrap =new bootstrap() ;
$bootstrap->dispatch();

//end