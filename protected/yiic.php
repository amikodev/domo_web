<?php

//error_reporting(~E_ALL);

$yii = dirname(__FILE__).'/../../yii/framework/yii.php';
$config = dirname(__FILE__).'/config/console.php';

require_once($yii);

$app = Yii::createConsoleApplication($config);

foreach($app->log->getRoutes() as $route){
    if($route instanceof CEmailLogRoute && ($sp = $app->systemParam->get('email-log-route_emails')) !== null){
        $route->setEmails($sp->value);
    }
}

$app->run();

