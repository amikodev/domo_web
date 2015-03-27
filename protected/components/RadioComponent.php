<?

class RadioComponent extends CApplicationComponent{
    
    public function initialize($controllerID){
        $cmds = array();
        if(($controllerModel = Device::model()->findByPk($controllerID)) !== null){
        
            $jsonParams = json_decode($controllerModel->params, true);
            $port = $jsonParams['serial'];

            if(isset($jsonParams['radio'])){
                $rp = $jsonParams['radio'];
                $cmds[] = array(CMD_RADIO, CMD_TYPE_RADIO_INIT, $rp['pin1'], $rp['pin2']);
                $cmds[] = array(CMD_RADIO, CMD_TYPE_RADIO_WPIPE, strrev(hex2bin($rp['pipe'])));
            }
            
            foreach($controllerModel->childs as $childModel){
                if($childModel->type == DEV_ARDUINO && $childModel->connectType == CONNECT_RADIO){
                    $jsonParamsChild = json_decode($childModel->params, true);
                    
                    if(isset($jsonParamsChild['radio'])){
                        $rpChild = $jsonParamsChild['radio'];
                        $cmds[] = array(CMD_RADIO, CMD_TYPE_RADIO_RPIPE, intval($childModel->pin), strrev(hex2bin($rpChild['pipe'])));
                    }
                    
                }
                
            }   
            
            foreach($cmds as $cmd){
                Yii::app()->arduino->sendCommand($cmd, $controllerModel);
            }
        }
        return $cmds;
    }

    public function changeKeys($controllerID){
        $cmd = array();
        if(($controllerModel = Device::model()->findByPk($controllerID)) !== null){
            $cmd = array(CMD_RADIO, CMD_TYPE_RADIO_CHKEYS);
            Yii::app()->arduino->sendCommand($cmd, $controllerModel);
        }
        return $cmd;
    }
    
    public function send($controllerID, $content){
        $cmd = array();
        if(($controllerModel = Device::model()->findByPk($controllerID)) !== null){
            $cmd = array(CMD_RADIO, CMD_TYPE_RADIO_SEND, strlen($content), $content);
            Yii::app()->arduino->sendCommand($cmd, $controllerModel);
        }
        return $cmd;
    }
    
    
    
    
}
