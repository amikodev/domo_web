<?php

require_once dirname(__FILE__)."/defines.php";

return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'Домашняя автоматизация "Домовой"',
    'language' => 'ru',
    'sourceLanguage' => 'ru',

	// preloading 'log' component
	'preload'=>array('log'),//, 'EJSUrlManager'),

	'import'=>array(
		'application.models.*',
		'application.components.*',
		'application.components.plugins.*',
	),

	'modules'=>array(
		'gii'=>array(
			'class'=>'system.gii.GiiModule',
			'password'=>'123',
			// If removed, Gii defaults to localhost only. Edit carefully to taste.
			//'ipFilters'=>array('127.0.0.1','::1'),
			'ipFilters'=>array('*','*'),
		),
		
	),

	// application components
	'components'=>array(
		'user'=>array(
			'allowAutoLogin'=>true,
		),
		'db'=>array(
			'connectionString' => 'mysql:host=localhost;dbname=domo',
			'emulatePrepare' => true,
			'username' => 'root',
			'password' => isset($_ENV["windir"])?'':'root',
			'charset' => 'utf8',
            'tablePrefix' => 'dom_',
		),
		
		'errorHandler'=>array(
			// use 'site/error' action to display errors
			'errorAction'=>'site/error',
		),
		'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
				array(
					'class'=>'CFileLogRoute',
					'levels'=>'error, warning',
				),
                array(
                    'class' => 'CFileLogRoute',
                    'levels' => 'info',
                    'categories' => 'arduino.*',
                    'logFile' => 'arduino.log',
                    'maxFileSize' => 50*1024,   // 50 МБ
                    'maxLogFiles' => 10000,
                ),
                array(
                    'class' => 'CFileLogRoute',
                    'levels' => 'info',
                    'categories' => 'scenario.*',
                    'logFile' => 'scenario.log',
                    'maxFileSize' => 50*1024,   // 50 МБ
                    'maxLogFiles' => 10000,
                ),
                array(
                    'class' => 'CFileLogRoute',
                    'levels' => 'info',
                    'categories' => 'exec',
                    'logFile' => 'exec.log',
                    'maxFileSize' => 50*1024,   // 50 МБ
                    'maxLogFiles' => 10000,
                ),
                array(
                    'class' => 'CFileLogRoute',
                    'levels' => 'info',
                    'categories' => 'rabbitmq.*',
                    'logFile' => 'rabbitmq.log',
                    'maxFileSize' => 50*1024,   // 50 МБ
                    'maxLogFiles' => 10000,
                ),
                
                array(
                    'class' => 'CDbLogRoute',
                    'levels' => 'info',
                    'categories' => 'main',
                    'logTableName' => '{{LogMain}}',
                    'connectionID' => 'db',
                    
                ),
                
                array(
                    'class' => 'CEmailLogRoute',
                    'levels' => 'error, warning',
                    'utf8' => true,
                ),
				// uncomment the following to show log messages on web pages
				/*
				array(
					'class'=>'CWebLogRoute',
				),
				*/
			),
		),
        
        'EJSUrlManager' => array(
            'class' => 'ext.JSUrlManager.src.EJSUrlManager',
        ),
        
        'amqp' => array(
            'class' => 'application.components.AMQP.CAMQP',
            'host' => '127.0.0.1',
            'port' => '5672',
            'login'=>'guest',
            'password'=>'guest',
            'vhost'=>'/',
        ),
        'sms' => array(
            'class' => 'application.components.SmsComponent',
            'gammu' => array(
                'config' => '/etc/gammu-smsdrc',
                'device0' => '/dev/serial/by-id/usb-HUAWEI_Technology_HUAWEI_Mobile-if00-port0',
                'device1' => '/dev/serial/by-id/usb-HUAWEI_Technology_HUAWEI_Mobile-if01-port0',
                'timeout' => 30, // сек.
            ),
        ),
        'arduino' => array(
            'class' => 'application.components.ArduinoComponent',
        ),
        'music' => array(
            'class' => 'application.components.MusicComponent',
            'path' => '/home/pi/music',
        ),
        'nfc' => array(
            'class' => 'application.components.NFCComponent',
        ),
        'scenario' => array(
            'class' => 'application.components.ScenarioComponent',
        ),
        'systemParam' => array(
            'class' => 'application.components.SystemParamsComponent',
        ),
        
	),

	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
    'params' => array_merge(array(
		'adminEmail'=>'webmaster@example.com',
        'gammu' => '/etc/gammu-smsdrc',
        'amqp' => array(
            'port' => '15674',
        ),
    ), $defParams),
    
);

