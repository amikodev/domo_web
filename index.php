<?

date_default_timezone_set('Europe/Moscow');

$webRoot=dirname(__FILE__);

//error_reporting(E_ERROR);
//error_reporting(E_ALL);
//error_reporting(~E_WARNING);
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL', 3);

$yiiRoot = dirname(__FILE__).'/../../yii';
if(!is_dir($yiiRoot)){
    $yiiRoot = dirname($webRoot).'/yii';
}

require_once($yiiRoot.'/framework/yii.php');
$configFile=$webRoot.'/protected/config/main.php';

$config = include_once($configFile);

header('Content-Type: text/html; charset=UTF-8');
$app = Yii::createWebApplication($config);


foreach($app->log->getRoutes() as $route){
    if($route instanceof CEmailLogRoute && ($sp = $app->systemParam->get('email-log-route_emails')) !== null){
        $route->setEmails($sp->value);
    }
}

$app->run(); 


?>