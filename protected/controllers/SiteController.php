<?php

/*
 * + TODO: оформить ar0.php как консольное приложение yii
 * + TODO: ответ от контроллера отправлять не в файл, а в БД
 * + TODO: список объектов для прослушивания
 * TODO: изображения исполнительных устройств
 * TODO: в ArduinorecieveCommand добавить проверку статуса выполнения
 * 
 * 
 * Музыка: mpg321 tmp/05.\ Let\ It\ Burn.mp3 
 * Изменение громкости: 
 *      amixer sget PCM
 *      amixer sset PCM 70%
 *      alsamixer   - ручное управление
 * 
 * 
 * RabbitMQ
 * после остановки сервера может остаться процесс
 * ps aux | grep epmd
 * ps aux | grep erl
 * 
*/



class SiteController extends Controller{

    
    public $defaultAction = 'scene';
    

    public function actionIndex(){
        $this->redirect(array('/site/scene'));
	}

    
    public function actionTest(){

    }
    
    
    public function actionTest3d(){
        $this->render('test3d');
    }
    
    
    public function actionInfo(){
        phpinfo();
    }
    
    public function actionDevices(){
        $usbSerials = glob("/dev/serial/by-id/usb-*");
        
        $videoDevices = glob("/dev/v4l/by-id/*");

        $criteria = new CDbCriteria;
        $criteria->condition = 'connectType=:connectType';
        $criteria->params = array(':connectType'=>CONNECT_ONEWIRE);
        $onewireDevices = Device::model()->findAll($criteria);
        
        $criteria = new CDbCriteria;
        $criteria->condition = 'connectType=:connectType';
        $criteria->params = array(':connectType'=>CONNECT_I2C);
        $i2cDevices = Device::model()->findAll($criteria);
        
        $criteria = new CDbCriteria;
        $criteria->condition = 'parentID is null';
        $controllerDevices = Device::model()->findAll($criteria);
        
        $criteria = new CDbCriteria;
        $criteria->condition = 'type<:type and parentID is not null';
        $criteria->params = array(':type'=>DEV_ARDUINO);
        //$criteria->addInCondition('type', array());
        $executiveDevices = Device::model()->findAll($criteria);
        
        $this->render('devices', array(
            'onewireDevices' => $onewireDevices,
            'i2cDevices' => $i2cDevices,
            'controllerDevices' => $controllerDevices,
            'usbSerials' => $usbSerials,
            'videoDevices' => $videoDevices,
            'executiveDevices' => $executiveDevices,
        ));
    }
    
    public function actionScene($id=null){
        $sceneModels = Scene::model()->with(array('sceneDevices', 'sceneDevices.device'))->findAll();
        
        $criteria = new CDbCriteria;
        $criteria->condition = 'actived=1';
        $pluginModels = Plugin::model()->findAll($criteria);
        
        $criteria = new CDbCriteria;
        $criteria->condition = 'logtime>:logtime';
        $criteria->params = array(':logtime'=>time()-24*60*60);
        $criteria->order = 'id desc';
        $logMainModels = LogMain::model()->findAll($criteria);
        
        $deviceImages = array();
        foreach(glob(Yii::app()->basePath.'/../images/icons/*.*') as $fname){
            $fname = basename($fname);
            $deviceImages[] = $fname;
        }
        
        if(sizeof($sceneModels) > 0){
            $this->render('scene', array(
                'id'=>$id,
                'sceneModels'=>$sceneModels, 
                'pluginModels'=>$pluginModels, 
                'logMainModels'=>$logMainModels,
                'deviceImages'=>$deviceImages,
            ));
        } else{
            $this->render('scenenotfound');
        }
    }
    
    public function actionSceneupdate($id=null){
        
        $sceneModels = Scene::model()->with(array('sceneDevices', 'sceneDevices.device'))->findAll();
        
        $criteria = new CDbCriteria;
        $criteria->condition = 'type<:type and parentID is not null';
        $criteria->params = array(':type'=>DEV_ARDUINO);
        //$criteria->addInCondition('type', array());
        $executiveDevices = Device::model()->findAll($criteria);
        
        $sceneWidgetModel = new SceneWidget;
        $sceneWidgetModel->sceneID = $id;
        
        $deviceImages = array();
        foreach(glob(Yii::app()->basePath.'/../images/icons/*.*') as $fname){
            $fname = basename($fname);
            $deviceImages[] = $fname;
        }
        
        $this->render('sceneupdate', array(
            'sceneModels'=>$sceneModels, 
            'id'=>$id,
            'executiveDevices' => $executiveDevices,
            'sceneWidgetModel' => $sceneWidgetModel,
            'deviceImages' => $deviceImages,
        ));
        
    }
    
    
    public function actionSceneputdevice($sceneID){
        $params = $_POST;

        if(($sceneModel = Scene::model()->findByPk($sceneID)) !== null && ($deviceModel = Device::model()->findByPk($params['id'])) !== null ){
            //$x = intval($params['x']);
            //$y = intval($params['y']);

            $criteria = new CDbCriteria;
            $criteria->condition = 'sceneID=:sceneID and deviceId=:deviceID';
            $criteria->params = array(':sceneID'=>$sceneModel->id, ':deviceID'=>$deviceModel->id);
            if(($sceneDeviceModel = SceneDevice::model()->find($criteria)) === null){
                $sceneDeviceModel = new SceneDevice;
                $sceneDeviceModel->sceneID = $sceneModel->id;
                $sceneDeviceModel->deviceID = $deviceModel->id;
            }
            
            if(isset($params['x']) && isset($params['y'])){
                $x = intval($params['x']);
                $y = intval($params['y']);
                $sceneDeviceModel->x = $x;
                $sceneDeviceModel->y = $y;
            }
            
            if(isset($params['angle'])){
                $sceneDeviceModel->angle = intval($params['angle']);
            }
            
            if(isset($params['width']) && isset($params['height'])){
                //$x = intval($params['x']);
                //$y = intval($params['y']);
                $sceneDeviceModel->width = intval($params['width']);
                $sceneDeviceModel->height = intval($params['height']);
            }
            
            $sceneDeviceModel->save();
            
            $this->renderPartial('_sceneDeviceItem', array('sceneDeviceModel'=>$sceneDeviceModel, 'edit'=>true));
            
        }
        
    }
    
    public function actionScenedeletedevice($sceneID){
        $params = $_POST;

        if(($sceneModel = Scene::model()->findByPk($sceneID)) !== null && ($deviceModel = Device::model()->findByPk($params['id'])) !== null ){
            
            $criteria = new CDbCriteria;
            $criteria->condition = 'sceneID=:sceneID and deviceId=:deviceID';
            $criteria->params = array(':sceneID'=>$sceneModel->id, ':deviceID'=>$deviceModel->id);
            if(($sceneDeviceModel = SceneDevice::model()->find($criteria)) !== null){
                $sceneDeviceModel->delete();
            }
            print $deviceModel->id;
            
        }
    }
    
    public function actionSceneputwidget($sceneID){
        $params = $_POST;
        
        if(isset($_POST['SceneWidget'])){
            $id = $params['widgetID'];
            if(!$id){
                $sceneWidgetModel = new SceneWidget;
            } else{
                $sceneWidgetModel = SceneWidget::model()->findByPk($id);
                if($sceneWidgetModel === null)
                    $sceneWidgetModel = new SceneWidget;
            }
        }
        
        $sceneWidgetModel->attributes = $_POST['SceneWidget'];
        unset($params['SceneWidget']);
        $sceneWidgetModel->params = json_encode($params);
        

        if($sceneWidgetModel->validate() && $sceneWidgetModel->save()){
            $attrs = $sceneWidgetModel->attributes;
            $attrs['params'] = $params;
            print json_encode(array(
                'state' => 'success',
                'attributes' => $attrs,
            ));
            
        } else{
            $errors = array();
            foreach($sceneWidgetModel->getErrors() as $error){
                foreach($error as $err){
                    $errors[] = $err;
                }
            }
            
            print json_encode(array(
                'state' => 'error',
                'message' => implode("\n", $errors),
            ));
            
        }
        
    }
    
    
    public function actionHistorygraph($deviceID){
        if(($deviceModel = Device::model()->findByPk($deviceID)) !== null){
            $criteria = new CDbCriteria;
            $criteria->condition = 'deviceID=:deviceID';// and datechange>=:datechange';
            $criteria->params = array(
                ':deviceID' => $deviceModel->id,
                //':datechange' => date("Y-m-d H:i:s", time()-2*24*60*60),
            );
            $criteria->order = 'id desc';
            $criteria->limit = 100;
            
            $historyModels = DeviceHistory::model()->findAll($criteria);
            krsort($historyModels);
            
            $this->render('historygraph', array('deviceModel'=>$deviceModel, 'historyModels'=>$historyModels));
        }
    }
    
    public function actionHistorydata($deviceID){
        if(($deviceModel = Device::model()->with('history')->findByPk($deviceID)) !== null){
            $this->render('historydata', array('deviceModel'=>$deviceModel));
        }
    }
    
    public function actionDevicevalue($deviceID){
        if(($deviceModel = Device::model()->findByPk($deviceID)) !== null){
            print json_encode(array(
                'state' => 'success',
                'deviceID' => $deviceID,
                'value' => $deviceModel->value,
            ));
        } else{
            print json_encode(array(
                'state' => 'error',
                'message' => "Устройство '{$deviceID}' не найдено",
            ));
            
        }
    }
    
    
    public function actionRebootsystem(){
        
        Yii::log('Перезагрузка системы', CLogger::LEVEL_INFO, 'main');
        $c = exec("sudo reboot", $a, $b);
        
        print "!!! Перезагрузка системы...";
        
    }
    
    
    
	/**
	 * This is the action to handle external exceptions.
	 */
	public function actionError()
	{
		if($error=Yii::app()->errorHandler->error)
		{
			if(Yii::app()->request->isAjaxRequest)
				echo $error['message'];
			else
				$this->render('error', $error);
		}
	}


}