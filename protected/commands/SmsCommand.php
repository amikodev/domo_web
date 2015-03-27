<?

class SmsCommand extends CConsoleCommand{
    
    public function actionReceive(){
        
        $messageCount = $_SERVER['SMS_MESSAGES'];
        
        $fullMessage = "";
        $number = null;
        for($i=1; $i<=$messageCount; $i++){
            $number = $_SERVER["SMS_{$i}_NUMBER"];
            $message = $_SERVER["SMS_{$i}_TEXT"];
            $fullMessage .= $message;
        }
        
        if( ($receiveValidNumbers = Yii::app()->systemParam->get("sms_valid_numbers")) !== null && in_array($number, explode(',', $receiveValidNumbers->value))){        // приём СМС только с определённых номеров

            $fullMessage = trim(mb_strtolower($fullMessage, "UTF-8"));

            if($fullMessage == ""){
                $message = "- список" ."\n"
                         . "- значение ID" ."\n"
                         . "- значение ID val" ."\n"
                        ;
                Yii::app()->sms->send($number, $message);
            } else{
            
                $ps = split(" ", $fullMessage);

                $command = $ps[0];

                if(in_array($command, array('list', 'список'))){
                    $message = "";

                    $criteria = new CDbCriteria;
                    $criteria->condition = 'type<:type and parentID is not null';
                    $criteria->params = array(':type'=>DEV_ARDUINO);
                    $executiveDevices = Device::model()->findAll($criteria);
                    foreach($executiveDevices as $executiveDevice){
                        $message .= "[{$executiveDevice->id}] {$executiveDevice->caption}" ."\n";
                    }

                    Yii::app()->sms->send($number, $message);
                } elseif(in_array($command, array('value', 'значение'))){
                    if(isset($ps[1])){
                        $id = $ps[1];

                        if(($deviceModel = Device::model()->findByPk($id)) !== null){
                            if(isset($ps[2])){          // установить значение
                                $value = $ps[2];
                                
                                $controllerModel = null;
                                if($deviceModel->parent->type == DEV_ARDUINO) $controllerModel = $deviceModel->parent;
                                else $controllerModel = $deviceModel->parent->parent;
                                
                                Yii::app()->arduino->command($deviceModel->id, CMD_WRITE, $value);

                                $message = "[{$deviceModel->id}] {$deviceModel->caption} : команда отправлена";
                            } else{
                                $message = "[{$deviceModel->id}] {$deviceModel->caption} : {$deviceModel->value} ({$deviceModel->datechange})";
                            }

                        } else{
                            $message = "[{$id}] устройство не найдено";
                        }

                        Yii::app()->sms->send($number, $message, true);
                    } else{
                        
                    }
                } else{
                    $message = "Команда '{$command}' не определена.";
                    Yii::app()->sms->send($number, $message);
                }
            }
        }

    }
    
    public function actionTest(){
        
    }
    
    public function actionSend($number, $message, $flash=false){
        Yii::app()->sms->send($number, $message, $flash);
    }
    
    public function actionSendUSSD($number){
        Yii::app()->sms->sendUSSD($number);
    }
    
    /**
     * Проверка работоспособности модема.
     */
    public function actionCheckdevice(){
        if(file_exists(Yii::app()->sms->gammu['config'])){
            $conf = parse_ini_file(Yii::app()->sms->gammu['config'], true);
            $logFile = $conf['smsd']['LogFile'];

            if(($fh = fopen($logFile, 'r')) !== false){
                $position = filesize($logFile);
                fseek($fh, $position-1024);
                $data = fread($fh, 1024);
                fclose($fh);
                //var_dump($data);
                
                if(strpos($data, 'Error at init connection') !== false){
                    Yii::app()->sms->sendUSSD('*100#');
                    
                    // записать в лог информацию об ошибке
                    
                }
                
            }
            
        }
    }
    
    
//    public function actionRestart(){
//        $f = fopen(Yii::app()->sms->gammu['device0'], "rw+");
//        fputs($f, "AT+CFUN=1\r\n");
//        fclose($f);
//    }

    
}

?>