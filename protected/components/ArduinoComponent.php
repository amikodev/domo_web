<?

class ArduinoComponent extends CApplicationComponent{
    
    public $radio;
    
    
    public function init(){
        $this->radio = new RadioComponent();
    }
    
    
    /**
     * Отправление команды arduino
     * @param int $deviceID ID устройства
     * @param int $type тип команды (CMD_TYPE_*)
     * @param string $value значение
     * @return string|null команда arduino
     */
    public function command($deviceID, $type, $value=null){
        
        if(($deviceModel = Device::model()->with(array('parent'))->findByPk($deviceID)) !== null){
            if(($controllerModel = $deviceModel->getControllerModel()) !== null){

                $date = date("Y-m-d H:i:s");
                
                $cmd = array();

                if($deviceModel->parent == null && $deviceModel->type == DEV_ARDUINO){
                    //$cmd = $value;
                } elseif($deviceModel->parent !== null && $deviceModel->parent->type == DEV_ARDUINO){
                    if($deviceModel->connectType == CONNECT_PIN){

                        if($deviceModel->type == DEV_REGISTR_IN){
                            
                        } elseif($deviceModel->type == DEV_REGISTR_OUT){
                            
                        } else{
                            if($type == CMD_TYPE_READ){
                                $cmd = array(CMD_PIN, CMD_TYPE_PIN_READ, intval($deviceModel->pin));
                            } elseif($type == CMD_TYPE_WRITE){
                                $cmd = array(CMD_PIN, CMD_TYPE_PIN_WRITE, intval($deviceModel->pin), intval($value)&0xFF);
                            }
                        }   
                        
                    } elseif($deviceModel->connectType == CONNECT_ONEWIRE){
                        if($type == CMD_TYPE_READ){
                            $cmd = array(CMD_1WIRE, CMD_TYPE_1WIRE_READ, intval($deviceModel->pin), hex2bin($deviceModel->onewireID) );
                        } elseif($type == CMD_TYPE_WRITE){
                        } elseif($type == CMD_TYPE_COUNT){
                            $cmd = array(CMD_1WIRE, CMD_TYPE_1WIRE_COUNT, intval($deviceModel->pin));
                        } elseif($type == CMD_TYPE_LIST){
                            $cmd = array(CMD_1WIRE, CMD_TYPE_1WIRE_LIST, intval($deviceModel->pin));
                        }

                    } elseif($deviceModel->connectType == CONNECT_I2C){
                           
                    } elseif($deviceModel->connectType == CONNECT_OTHER){
                        if($deviceModel->type == DEV_DHT){
                            if($type == CMD_TYPE_READ){
                                $cmd = array(CMD_DHT, CMD_TYPE_DHT_READ, intval($deviceModel->pin));
                            }
                        }
                    }
                    
                } elseif($deviceModel->parent !== null && $deviceModel->parent->type != DEV_ARDUINO && $deviceModel->parent->parent->type == DEV_ARDUINO){
                    $parentModel = $deviceModel->parent;
                    if($parentModel->connectType == CONNECT_PIN){
                        if($deviceModel->connectType == CONNECT_PIN){
                            if($parentModel->type == DEV_REGISTR_IN){

                            } elseif($parentModel->type == DEV_REGISTR_OUT){

                                
                            }                            
                        }   
                        
                    } elseif($parentModel->connectType == CONNECT_ONEWIRE){
                        if($deviceModel->connectType == CONNECT_PIN){

                            if($type == CMD_TYPE_READ){
                                $cmd = array(CMD_1WIRE, CMD_TYPE_1WIRE_READ, intval($parentModel->pin), hex2bin($parentModel->onewireID));
                            } elseif($type == CMD_TYPE_WRITE){
                                
                                if($parentModel->type == DEV_1WIRE2){       // DS2413
                                    $data = array();
                                    foreach($parentModel->childs as $childModel){
                                        if(in_array($childModel->type, Yii::app()->params['inputDevices'])){
                                            $data[$childModel->pin] = 1;
                                        } elseif($childModel->id != $deviceModel->id){
                                            $data[$childModel->pin] = $childModel->value?1:0;
                                        } else{
                                            $data[$childModel->pin] = $value?1:0;
                                        }
                                    }

                                    $keys = array_keys($data);
                                    for($i=0; $i<max($keys); $i++){
                                        if(!isset($data[$i])){
                                            $data[$i] = 0;
                                        }
                                    }
                                    krsort($data);

                                    $oneWireValue = decbin(0);
                                    foreach($data as $dat){
                                        $oneWireValue = ($oneWireValue << 1) | decbin($dat);
                                    }

                                    $cmd = array(CMD_1WIRE, CMD_TYPE_1WIRE_WRITE, intval($parentModel->pin), hex2bin($parentModel->onewireID), $oneWireValue);
                                
                                } elseif($parentModel->type == DEV_1WIRE8){     // DS2408
                                    
                                    
                                    if($deviceModel->value !== $value){
                                        $deviceModel->datechange = $date;
                                        $deviceModel->value = $value;
                                        $deviceModel->save();

                                        $this->updateWidgetsByDevice($deviceModel);

                                        Yii::log("{$deviceModel->caption}: {$value}", CLogger::LEVEL_INFO, 'main');
                                    }    
                                    $historyModel = new DeviceHistory;
                                    $historyModel->deviceID = $deviceModel->id;
                                    $historyModel->datechange = $date;
                                    $historyModel->value = $value;
                                    $historyModel->save();
                                    
                                    Yii::app()->scenario->doScenaries($deviceModel->scenaries);
                                    
                                    $dat = array();
                                    foreach($parentModel->childs as $childModel){
                                        if(in_array($childModel->type, Yii::app()->params['inputDevices'])){
                                            $dat[$childModel->pin] = 0;
                                        } else{
                                            $dat[$childModel->pin] = $childModel->value?1:0;
                                        }
                                        
                                    }           
                                    
                                    for($i=0; $i<8; $i++) if(!isset($dat[$i])) $dat[$i] = 0;
                                    ksort($dat);
                                    $hdat = sprintf("%02X", intval(base_convert(implode("", $dat), 2, 10)));
                                    
                                    $cmd = array(CMD_1WIRE, CMD_TYPE_1WIRE_WRITE, intval($parentModel->pin), hex2bin($parentModel->onewireID), hex2bin($hdat));
                                    
                                    
                                    
                                }
                                    
                            }

                            
                        }   
                        
                    }
                    
                }

                // сдвиговые регистры
                //if($deviceModel->type == DEV_REGISTR_IN || $deviceModel->type == DEV_REGISTR_OUT){
                    
                //}
                // родителем является выходной сдвиговый регистр
                if(in_array($deviceModel->type, Yii::app()->params['outputDevices']) && $deviceModel->parent !== null && $deviceModel->parent->type == DEV_REGISTR_OUT){
                    $shmodel = $deviceModel->parent;
                    $firstShmodel = $deviceModel->parent;
                    
                    $data = array();
                    while(intval($shmodel->type) !== DEV_ARDUINO){
                        
                        $dat = array();
                        
                        foreach($shmodel->childs as $childModel){
                            if(!in_array($childModel->type, array(DEV_REGISTR_IN, DEV_REGISTR_OUT))){
                                $dat[$childModel->pin] = $childModel->value?1:0;
                            }
                        }
                        for($i=0; $i<8; $i++) if(!isset($dat[$i])) $dat[$i] = 0;
                        
                        ksort($dat);
                        $hdat = sprintf("%02X", intval(base_convert(implode("", $dat), 2, 10)));
                        
                        $data[] = $hdat;
                        
                        if($shmodel->parent !== null){
                            $shmodel = $shmodel->parent;
                            if($shmodel->type == DEV_REGISTR_OUT) 
                                $firstShmodel = $shmodel;
                        } else{
                            break;
                        }
                    }
                    
                    $params = json_decode($firstShmodel->params, true);
                    krsort($data);
                    
                    $cmd = array(CMD_SHIFT, CMD_TYPE_SHIFT_WRITE, $firstShmodel->pin, $params['clockPin'], $params['latchPin'], sizeof($data), hex2bin(implode("", $data)));
                    
                } elseif(in_array($deviceModel->type, Yii::app()->params['inputDevices']) && $deviceModel->parent !== null && $deviceModel->parent->type == DEV_REGISTR_IN){
                // родителем является входной сдвиговый регистр
                    $shmodel = $deviceModel->parent;
                    $firstShmodel = $deviceModel->parent;

                    $count = 0;
                    while($shmodel->type !== DEV_ARDUINO){
                        $count++;
                        
                        if($shmodel->parent !== null){
                            $shmodel = $shmodel->parent;
                            if($shmodel->type == DEV_REGISTR_OUT) 
                                $firstShmodel = $shmodel;
                        } else{
                            break;
                        }
                        
                    }                    

                    $params = json_decode($firstShmodel->params, true);
                    $cmd = array(CMD_SHIFT, CMD_TYPE_SHIFT_READ, $firstShmodel->pin, $params['clockPin'], $params['latchPin'], $count);
                    
                    
                }

                if($controllerModel->connectType == CONNECT_RADIO){
                    $cmd = array_merge(array(CMD_RADIO, CMD_TYPE_RADIO_SEND, sizeof($cmd)), $cmd);
                    $controllerModel = $controllerModel->parent;
                    // TODO: ставить сообщение в очередь на повтор если ответа не получили, а он нужен
                }
                
                
                $this->sendCommand($cmd, $controllerModel);
                return $cmd;
                
            } else{
                return null;
            }
        } else{
            return null;
        }         
        
    }
    
    /**
     * Получение данных от arduino
     * @param int $controllerID ID контроллера
     * @param text $content содержимое
     * @return array распарсенные данные
     */
    public function recieve_bak($controllerID, $content){
        
        $controllerModel = Device::model()->findByPk($controllerID);
        
        Yii::log("<<< {$controllerModel->caption}: ".trim($content), CLogger::LEVEL_INFO, "arduino.recieve");
        Yii::getLogger()->flush(true);
        
        $ret = array();
        if(substr($content, strlen($content)-1, 1) == "\n"){    // содержимое завершается переводом на новую строку
            $ls = explode("\n", $content);
            foreach($ls as $l){
                $date = date("Y-m-d H:i:s");
                $l = trim($l);
                if($l){
                    
                    $ps = explode(":", $l);
                    
                    // парсинг ответа от контроллера
                    $command = $ps[0];
                    
                    if($command == 'CONTROLLER'){
                        if($ps[1] == "ID"){
                            $ret[] = array('controllerID'=>$controllerID, 'ID'=>$ps[2]);
                            // после получения ID контроллера:
                            // отправляем ему режим работы пинов
                            $this->modes($ps[2]);
                            // отправляем ему список прослушиваемых устройств
                            $this->listens($ps[2]);
                        }
                    } if($command == 'PIN'){
                        $pin = $ps[1];
                        $value = $ps[2];
                        
                        $criteria = new CDbCriteria;
                        $criteria->condition = 't.connectType=:connectType and t.pin=:pin and ((`parent`.type=:controllerType and `parent`.id=:controllerID) or (`pparent`.type=:controllerType and `pparent`.id=:controllerID))';
                        $criteria->params = array(
                            ':connectType' => CONNECT_PIN,
                            ':pin' => $pin,
                            ':controllerType' => DEV_ARDUINO,
                            ':controllerID' => $controllerID,
                        );
                        $criteria->with = array(
                            'parent', 
                            'parent.parent' => array('alias'=>'pparent'),
                        );
                        if(($deviceModel = Device::model()->find($criteria)) !== null){

                            if(!in_array($value, array('INPUT', 'OUTPUT'))){
                                if($deviceModel->value != $value){
                                    $deviceModel->datechange = $date;
                                    $deviceModel->value = $value;
                                    $deviceModel->save();
                                    
                                    $this->updateWidgetsByDevice($deviceModel);
                                    
                                    Yii::log("{$deviceModel->caption}: {$value}", CLogger::LEVEL_INFO, 'main');
                                }
                                $historyModel = new DeviceHistory;
                                $historyModel->deviceID = $deviceModel->id;
                                $historyModel->datechange = $date;
                                $historyModel->value = $value;
                                $historyModel->save();

                                Yii::app()->scenario->doScenaries($deviceModel->scenaries);

                                $ret[] = array('controllerID'=>$controllerID, 'command'=>'DEVICE_VALUE', 'ID'=>$deviceModel->id, 'value'=>$value);
                            } else{
                                $ret[] = array('controllerID'=>$controllerID, 'command'=>'DEVICE_MODE', 'ID'=>$deviceModel->id, 'mode'=>$value);
                            }
                        } else{
                            $ret[] = array('controllerID'=>$controllerID, 'command'=>'DEVICE_NOTFOUND', 'content'=>$l);
                        }
                    } elseif($command == 'REGIN'){
                        
                    } elseif($command == 'REGOUT'){
                        
                    } elseif($command == '1WIRE'){
                        $pin = $ps[1];
                        if($ps[2] == "COUNT"){
                            $ret[] = array('controllerID'=>$controllerID, 'command'=>'1WIRE_COUNT', 'pin'=>$pin, 'value'=>$ps[3]);
                        } elseif($ps[2] == "LIST"){
                            $ret[] = array('controllerID'=>$controllerID, 'command'=>'1WIRE_LIST', 'pin'=>$pin, 'values'=>explode(',', $ps[3]));
                        } else{
                            $onewireID = $ps[2];
                            $values = $ps[3];
                            
                            $criteria = new CDbCriteria;
                            $criteria->condition = 't.connectType=:connectType and t.pin=:pin and t.onewireID=:onewireID and (`parent`.type=:controllerType and `parent`.id=:controllerID)';
                            $criteria->params = array(
                                ':connectType' => CONNECT_ONEWIRE,
                                ':pin' => $pin,
                                ':onewireID' => $onewireID,
                                ':controllerType' => DEV_ARDUINO,
                                ':controllerID' => $controllerID,
                            );
                            $criteria->with = array(
                                'parent',
                            );
                            if(($deviceModel = Device::model()->find($criteria)) !== null){

                                // прямые 1wire устройства, без посредников
                                if(in_array($deviceModel->type, Yii::app()->params['direct1WireDevices'])){
                                    $value = $ps[3];

                                    if($deviceModel->value != $value){
                                        $deviceModel->datechange = $date;
                                        $deviceModel->value = $value;
                                        $deviceModel->save();
                                        
                                        $this->updateWidgetsByDevice($deviceModel);
                                    
                                        Yii::log("{$deviceModel->caption}: {$value}", CLogger::LEVEL_INFO, 'main');
                                    }    
                                    $historyModel = new DeviceHistory;
                                    $historyModel->deviceID = $deviceModel->id;
                                    $historyModel->datechange = $date;
                                    $historyModel->value = $value;
                                    $historyModel->save();

                                    Yii::app()->scenario->doScenaries($deviceModel->scenaries);
                            
                                    $ret[] = array('controllerID'=>$controllerID, 'command'=>'DEVICE_VALUE', 'ID'=>$deviceModel->id, 'value'=>$value);
                                } else{     // устройство, являющееся посредником
                                    $pinChildData = array();
                                    foreach($deviceModel->childs as $childModel){
                                        $pinChildData[$childModel->pin] = $childModel;
                                    }

                                    if($deviceModel->value != $values){
                                        $deviceModel->datechange = $date;
                                        $deviceModel->value = $values;
                                        $deviceModel->save();
                                    }                                        
                                    $historyModel = new DeviceHistory;
                                    $historyModel->deviceID = $deviceModel->id;
                                    $historyModel->datechange = $date;
                                    $historyModel->value = $values;
                                    $historyModel->save();

                                    Yii::app()->scenario->doScenaries($deviceModel->scenaries);
                                    
                                    foreach(str_split($values) as $n=>$value){
                                        $ind = strlen($values) - $n - 1;
                                        if(isset($pinChildData[$ind]) && ($childModel = $pinChildData[$ind]) !== null){
                                            $ret[] = array('controllerID'=>$controllerID, 'command'=>'DEVICE_VALUE', 'ID'=>$childModel->id, 'value'=>$value);
                                            
                                            if($childModel->value != $value){
                                                $childModel->datechange = $date;
                                                $childModel->value = $value;
                                                $childModel->save();
                                                
                                                $this->updateWidgetsByDevice($childModel);
                                    
                                                Yii::log("{$childModel->caption}: {$value}", CLogger::LEVEL_INFO, 'main');
                                            }
                                            $historyModel = new DeviceHistory;
                                            $historyModel->deviceID = $childModel->id;
                                            $historyModel->datechange = $date;
                                            $historyModel->value = $value;
                                            $historyModel->save();
                                            
                                            Yii::app()->scenario->doScenaries($childModel->scenaries);
                                            
                                        }
                                    }
                                }
                            } else{
                                $ret[] = array('controllerID'=>$controllerID, 'command'=>'DEVICE_NOTFOUND', 'content'=>$l);
                            }
                        }
                    } elseif($command == 'NFC'){
                        
                        $criteria = new CDbCriteria;
                        $criteria->condition = 't.connectType=:connectType and t.type=:type and `parent`.type=:controllerType and `parent`.id=:controllerID';
                        $criteria->params = array(
                            ':connectType' => CONNECT_I2C,
                            ':type' => DEV_NFC,
                            ':controllerType' => DEV_ARDUINO,
                            ':controllerID' => $controllerID,
                        );
                        $criteria->with = array('parent');
                        if(($deviceModel = Device::model()->find($criteria)) !== null){
                        
                        
                            if($ps[1] == "FIRMWARE"){
                                $firmware = $ps[2];
                                $ret[] = array('controllerID'=>$controllerID, 'command'=>'NFC_FIRMWARE', 'ID'=>$deviceModel->id, 'firmware'=>$firmware);
                            } elseif($ps[1] == "UID"){
                                $uuid = $ps[2];
                                $block = $ps[3];
                                $data = $ps[4];

                                if(preg_match('/^[0-9A-F]+$/U', $data)){
                                    
                                    Yii::log("{$deviceModel->caption} [NFC:{$deviceModel->id}] блок: {$block}; данные: {$data}", CLogger::LEVEL_INFO, 'main');
                                    
                                    if($deviceModel->value != $uuid){
                                        $deviceModel->datechange = $date;
                                        $deviceModel->value = $uuid;
                                        $deviceModel->save();

                                        $this->updateWidgetsByDevice($deviceModel);
                                    }                                        
                                    $historyModel = new DeviceHistory;
                                    $historyModel->deviceID = $deviceModel->id;
                                    $historyModel->datechange = $date;
                                    $historyModel->value = $uuid;
                                    $historyModel->save();

                                    //$this->updateWidgetsByDevice($deviceModel);
                                    
                                    if($block == 4){
                                        $authModel = Yii::app()->nfc->getAuthModel($uuid, $data);
                                        Yii::app()->nfc->addAuthHistory($uuid, $data, $authModel);

//                                        if($authModel && $authModel->scenario && $authModel->scenario->actived){
//                                            $authModel->scenario->content = str_replace("<?", "", $authModel->scenario->content);
//                                            $newfunc = create_function('$scenarioModel, $nfcData', $authModel->scenario->content);
//                                            $newret = $newfunc($authModel->scenario, array('uuid'=>$uuid, 'block'=>$block, 'data'=>$data));
//                                        }
                                        if($authModel){
                                            Yii::app()->scenario->execScenario($authModel->scenario, array('nfcData'=>array('uuid'=>$uuid, 'block'=>$block, 'data'=>$data)));
                                        }
                                    }
                                }

                                $ret[] = array('controllerID'=>$controllerID, 'command'=>'NFC_DATA', 'ID'=>$deviceModel->id, 'uuid'=>$uuid, 'block'=>$block, 'data'=>$data);
                            }
                        } else{
                            $ret[] = array('controllerID'=>$controllerID, 'command'=>'DEVICE_NOTFOUND', 'content'=>$l);
                        }
                        
                    }
                    
                }
            }
        }    
        
        return $ret;
    }
    
    
    public function recieve($controllerID, $content){
        
        $controllerModel = Device::model()->findByPk($controllerID);
        
        //Yii::log("<<< {$controllerModel->caption}: ".trim($content), CLogger::LEVEL_INFO, "arduino.recieve");
        //Yii::getLogger()->flush(true);
        
        // var_dump("content: ".bin2hex($content));
        
        $ret = array();

        $date = date("Y-m-d H:i:s");

        $ps = unpack("c*", $content);
        $ps = array_values($ps);
        
        if(sizeof($ps) == 0) return $ret;

        // собираем разбитый ответ по частям
        $fname = Yii::app()->runtimePath."/pcmd_{$controllerID}.txt";
        if(file_exists($fname)){
            $content = file_get_contents($fname).$content;
            unlink($fname);
            $ps = unpack("c*", $content);
            $ps = array_values($ps);
        } elseif(!file_exists($fname) && (
                ($ps[0] == CMD_RADIO && $ps[1] == CMD_TYPE_RADIO_RESPONCE) ||
                ($ps[0] == CMD_NFC && $ps[1] == CMD_TYPE_NFC_RECIEVE)
                    )){
            file_put_contents($fname, $content);
            return $ret;
        }

        
        $command = $ps[0];
        
        if($command == CMD_CONTROLLER){
            $ret[] = array('controllerID'=>$controllerID, 'ID'=>$ps[1]);
            // после получения ID контроллера:
            // отправляем ему режим работы пинов
            $this->modes($ps[1]);
            // отправляем ему список прослушиваемых устройств
            $this->listens($ps[1]);
            // инициализация радио
            $this->radio->initialize($ps[1]);
        } elseif($command == CMD_PIN){
            
            $type = $ps[1];
            $pin = $ps[2];
            $value = $ps[3];
            
            $criteria = new CDbCriteria;
            $criteria->condition = 't.connectType=:connectType and t.pin=:pin and (`parent`.type=:controllerType and `parent`.id=:controllerID)';
            $criteria->params = array(
                ':connectType' => CONNECT_PIN,
                ':pin' => $pin,
                ':controllerType' => DEV_ARDUINO,
                ':controllerID' => $controllerID,
            );
            $criteria->with = array(
                'parent', 
            );
            if(($deviceModel = Device::model()->find($criteria)) !== null){
                if($type == CMD_TYPE_PIN_MODE_INPUT){
                    $ret[] = array('controllerID'=>$controllerID, 'command'=>'DEVICE_MODE', 'ID'=>$deviceModel->id, 'mode'=>"INPUT");
                } elseif($type == CMD_TYPE_PIN_MODE_OUTPUT){
                    $ret[] = array('controllerID'=>$controllerID, 'command'=>'DEVICE_MODE', 'ID'=>$deviceModel->id, 'mode'=>"OUTPUT");
                } elseif($type == CMD_TYPE_PIN_VALUE){
                    if($deviceModel->value !== $value){
                        $deviceModel->datechange = $date;
                        $deviceModel->value = $value;
                        $deviceModel->save();

                        $this->updateWidgetsByDevice($deviceModel);

                        Yii::log("{$deviceModel->caption}: {$value}", CLogger::LEVEL_INFO, 'main');
                    }
                    $historyModel = new DeviceHistory;
                    $historyModel->deviceID = $deviceModel->id;
                    $historyModel->datechange = $date;
                    $historyModel->value = $value;
                    $historyModel->save();

                    Yii::app()->scenario->doScenaries($deviceModel->scenaries);

                    $ret[] = array('controllerID'=>$controllerID, 'command'=>'DEVICE_VALUE', 'ID'=>$deviceModel->id, 'value'=>$value);
                    
                }
            } else{
                $ret[] = array('controllerID'=>$controllerID, 'command'=>'DEVICE_NOTFOUND', 'content'=>bin2hex($content));
            }            
            
            
        } elseif($command == CMD_1WIRE){
            $type = $ps[1];
            $pin = $ps[2];
            
            if($type == CMD_TYPE_1WIRE_COUNT){
                $ret[] = array('controllerID'=>$controllerID, 'command'=>'1WIRE_COUNT', 'pin'=>$pin, 'value'=>$ps[3]);
            } elseif($type == CMD_TYPE_1WIRE_LIST){
                $onewireID = bin2hex(substr($content, 3, 8));
                $onewireID = strtoupper($onewireID);
                //$ret[] = array('controllerID'=>$controllerID, 'command'=>'1WIRE_LIST', 'pin'=>$pin, 'values'=>$onewireID);
                $ret[] = array('controllerID'=>$controllerID, 'command'=>'1WIRE_ADDR', 'pin'=>$pin, 'value'=>$onewireID);
            } elseif($type == CMD_TYPE_1WIRE_VALUE){
                $onewireID = bin2hex(substr($content, 3, 8));
                $onewireID = strtoupper($onewireID);
                
                $criteria = new CDbCriteria;
                $criteria->condition = 't.connectType=:connectType and t.pin=:pin and upper(t.onewireID)=:onewireID and (`parent`.type=:controllerType and `parent`.id=:controllerID)';
                $criteria->params = array(
                    ':connectType' => CONNECT_ONEWIRE,
                    ':pin' => $pin,
                    ':onewireID' => $onewireID,
                    ':controllerType' => DEV_ARDUINO,
                    ':controllerID' => $controllerID,
                );
                $criteria->with = array(
                    'parent',
                );
                if(($deviceModel = Device::model()->find($criteria)) !== null){
                    // прямые 1wire устройства, без посредников
                    if(in_array($deviceModel->type, Yii::app()->params['direct1WireDevices'])){
                        //$value = $ps[3];
                        $value = $ps[11];
                        if($deviceModel->type == DEV_TEMPERATURESENSOR){
                            $f = unpack("f", substr($content, 11, 4));
                            $value = $f[1];
                        }

                        
                        if($deviceModel->value !== $value){
                            $deviceModel->datechange = $date;
                            $deviceModel->value = $value;
                            $deviceModel->save();

                            $this->updateWidgetsByDevice($deviceModel);

                            Yii::log("{$deviceModel->caption}: {$value}", CLogger::LEVEL_INFO, 'main');
                        }    
                        $historyModel = new DeviceHistory;
                        $historyModel->deviceID = $deviceModel->id;
                        $historyModel->datechange = $date;
                        $historyModel->value = $value;
                        $historyModel->save();

                        Yii::app()->scenario->doScenaries($deviceModel->scenaries);

                        $ret[] = array('controllerID'=>$controllerID, 'command'=>'DEVICE_VALUE', 'ID'=>$deviceModel->id, 'value'=>$value);
                        

                    } else{     // устройство, являющееся посредником
                        $values = $ps[11];
                        
                        $pinChildData = array();
                        foreach($deviceModel->childs as $childModel){
                            $pinChildData[$childModel->pin] = $childModel;
                        }

                        if($deviceModel->value != $values){
                            $deviceModel->datechange = $date;
                            $deviceModel->value = $values;
                            $deviceModel->save();
                        }                                        
                        $historyModel = new DeviceHistory;
                        $historyModel->deviceID = $deviceModel->id;
                        $historyModel->datechange = $date;
                        $historyModel->value = $values;
                        $historyModel->save();

                        Yii::app()->scenario->doScenaries($deviceModel->scenaries);

                        
                        for($ind=0; $ind<8; $ind++){
                            $value = ($values >> $ind) & 0x1;
                            
                            if(isset($pinChildData[$ind]) && ($childModel = $pinChildData[$ind]) !== null){
                                $ret[] = array('controllerID'=>$controllerID, 'command'=>'DEVICE_VALUE', 'ID'=>$childModel->id, 'value'=>$value);

                                if(in_array($childModel->type, Yii::app()->params['inputDevices'])){
                                    if($childModel->value != $value){
                                        $childModel->datechange = $date;
                                        $childModel->value = $value;
                                        $childModel->save();

                                        $this->updateWidgetsByDevice($childModel);

                                        Yii::log("{$childModel->caption}: {$value}", CLogger::LEVEL_INFO, 'main');
                                    }
                                    $historyModel = new DeviceHistory;
                                    $historyModel->deviceID = $childModel->id;
                                    $historyModel->datechange = $date;
                                    $historyModel->value = $value;
                                    $historyModel->save();

                                    Yii::app()->scenario->doScenaries($childModel->scenaries);
                                }
                            }
                            
                        }
                        
                    }
                        
                } else{
                    //$ret[] = array('controllerID'=>$controllerID, 'command'=>'DEVICE_NOTFOUND', 'content'=>$l);
                }                
                
            }
            
        } elseif($command == CMD_NFC){
            
            $criteria = new CDbCriteria;
            $criteria->condition = 't.connectType=:connectType and t.type=:type and `parent`.type=:controllerType and `parent`.id=:controllerID';
            $criteria->params = array(
                ':connectType' => CONNECT_I2C,
                ':type' => DEV_NFC,
                ':controllerType' => DEV_ARDUINO,
                ':controllerID' => $controllerID,
            );
            $criteria->with = array('parent');
            if(($deviceModel = Device::model()->find($criteria)) !== null){

                if($ps[1] == CMD_TYPE_NFC_FIRMWARE){
                    $firmware = strtoupper(bin2hex(substr($content, 2, 4)));
                    $ret[] = array('controllerID'=>$controllerID, 'command'=>'NFC_FIRMWARE', 'ID'=>$deviceModel->id, 'firmware'=>$firmware);
                    
                } elseif($ps[1] == CMD_TYPE_NFC_RECIEVE){
                    $uuid = strtoupper(bin2hex(substr($content, 2, 7)));
                    $block = $ps[9];
                    // TODO: размер данных в зависимости от типа NFC-метки
                    $data = strtoupper(bin2hex(substr($content, 10, 4)));

                    Yii::log("{$deviceModel->caption} [NFC:{$deviceModel->id}] блок: {$block}; данные: {$data}", CLogger::LEVEL_INFO, 'main');

                    if($deviceModel->value != $uuid){
                        $deviceModel->datechange = $date;
                        $deviceModel->value = $uuid;
                        $deviceModel->save();

                        $this->updateWidgetsByDevice($deviceModel);
                    }                                        
                    $historyModel = new DeviceHistory;
                    $historyModel->deviceID = $deviceModel->id;
                    $historyModel->datechange = $date;
                    $historyModel->value = $uuid;
                    $historyModel->save();

                    
                    if($block == 4){
                        $authModel = Yii::app()->nfc->getAuthModel($uuid, $data);
                        Yii::app()->nfc->addAuthHistory($uuid, $data, $authModel);
                        if($authModel){
                            Yii::app()->scenario->execScenario($authModel->scenario, array('nfcData'=>array('uuid'=>$uuid, 'block'=>$block, 'data'=>$data)));
                        }
                    }

                                    
                    $ret[] = array('controllerID'=>$controllerID, 'command'=>'NFC_DATA', 'ID'=>$deviceModel->id, 'uuid'=>$uuid, 'block'=>$block, 'data'=>$data);
                                    
                    
                }
                
            } else{
                $ret[] = array('controllerID'=>$controllerID, 'command'=>'DEVICE_NOTFOUND', 'content'=>bin2hex($content));
            }            
            
            
        } elseif($command == CMD_RADIO){

            $type = $ps[1];
            
            if($type == CMD_TYPE_RADIO_RESPONCE && strlen($content) == 32){
                
                //var_dump(bin2hex($content));
                
                $ccontent = substr($content, 0, 12).substr($content, 20, 8);

                $ps = unpack("c*", $ccontent);
                $ps = array_values($ps);
                        
                var_dump(bin2hex($ccontent));
                
                        
                $pipe_num = $ps[2];
                
                $criteria = new CDbCriteria;
                $criteria->condition = "t.connectType=:connectType and t.pin=:pin and (`parent`.type=:controllerType and `parent`.id=:controllerID)";
                $criteria->params = array(
                    ':connectType' => CONNECT_RADIO,
                    ':pin' => $pipe_num,
                    ':controllerType' => DEV_ARDUINO,
                    ':controllerID' => $controllerID,
                );
                $criteria->with = array(
                    'parent', 
                );
                if(($radioControllerModel = Device::model()->find($criteria)) !== null){
                    //var_dump($radioControllerModel->caption);
                    $ret = $this->recieve($radioControllerModel->id, substr($ccontent, 4, 16));
                } else{
                    print "not found\n";
                }
                
            }
            
        } elseif($command == CMD_DHT){
            $type = $ps[1];
            $pin = $ps[2];

            
            $criteria = new CDbCriteria;
            $criteria->condition = "t.connectType=:connectType and t.pin=:pin and (`parent`.type=:controllerType and `parent`.id=:controllerID)";
            $criteria->params = array(
                ':connectType' => CONNECT_OTHER,
                ':pin' => $pin,
                ':controllerType' => DEV_ARDUINO,
                ':controllerID' => $controllerID,
            );
            $criteria->with = array(
                'parent', 
            );
            if(($deviceModel = Device::model()->find($criteria)) !== null){
                if($type == CMD_TYPE_DHT_VALUE){
                    $valTemp = $ps[3];
                    $valHum = $ps[4];

                    $value = implode("|", array($valTemp, $valHum));
                    
                    if($deviceModel->value !== $value){
                        $deviceModel->datechange = $date;
                        $deviceModel->value = $value;
                        $deviceModel->save();

                        $this->updateWidgetsByDevice($deviceModel);

                        Yii::log("{$deviceModel->caption}: {$value}", CLogger::LEVEL_INFO, 'main');
                    }
                    $historyModel = new DeviceHistory;
                    $historyModel->deviceID = $deviceModel->id;
                    $historyModel->datechange = $date;
                    $historyModel->value = $value;
                    $historyModel->save();

                    Yii::app()->scenario->doScenaries($deviceModel->scenaries);

                    $ret[] = array('controllerID'=>$controllerID, 'command'=>'DEVICE_VALUE', 'ID'=>$deviceModel->id, 'value'=>$value);
                    
                    
                } elseif($type == CMD_TYPE_DHT_ERROR){
                    $code = $ps[3];
                }
            } else{
                $ret[] = array('controllerID'=>$controllerID, 'command'=>'DEVICE_NOTFOUND', 'content'=>bin2hex($content));
            }         
            
        } elseif($command == CMD_SHIFT){
            
        } elseif($command == CMD_LISTEN){
            
        } elseif($command == CMD_FREERAM){
            
        }
        
        return $ret;
    }
    
    /**
     * Получение ID контроллера по серийному номеру
     * @param string $serial серийный номер
     * @return int ID контроллера
     */
    public function getIdBySerial($serial){
        $id = null;
        
        $fname = Yii::app()->basePath."/runtime/serialid/{$serial}.txt";
        if(file_exists($fname)){
            $fid = intval(file_get_contents($fname));
            if($fid) $id = $fid;
        }
        
        if($id === null){
            $criteria = new CDbCriteria;
            $criteria->condition = "type=:type and connectType=:connectType and params like :params";
            $criteria->params = array(
                ':type' => DEV_ARDUINO,
                ':connectType' => CONNECT_USB,
                ':params' => '%"serial":"%/'.$serial.'"%',
            );

            if(($model = Device::model()->find($criteria)) !== null){
                $id = $model->id;
                file_put_contents($fname, $id);
            }
        }
        
        return $id;
    }
    
    /**
     * Прослушивание устройств
     * @param int $controllerID ID контроллера
     */
    public function listens($controllerID){
        $cmds = array();
        if(($controllerModel = Device::model()->findByPk($controllerID)) !== null){
        
            $jsonParams = json_decode($controllerModel->params, true);
            $port = $jsonParams['serial'];

            foreach($controllerModel->childs as $childModel){
                if(in_array($childModel->type, Yii::app()->params['listenDevices'])){
                    if($childModel->connectType == CONNECT_PIN){
                        $cmds[] = array(CMD_LISTEN, CMD_PIN, CMD_TYPE_PIN_READ, intval($childModel->pin));
                    } elseif($childModel->connectType == CONNECT_ONEWIRE){
                        $cmds[] = array(CMD_LISTEN, CMD_1WIRE, CMD_TYPE_1WIRE_READ, intval($childModel->pin), hex2bin($childModel->onewireID));
                    }
                }
            }
            
            foreach($cmds as $cmd){
                $this->sendCommand($cmd, $controllerModel);
            }
        }
        return $cmds;
    }
    
    /**
     * Установка режима работы пинов arduino
     * @param int $controllerID ID контроллера
     * @return array отправленные команды
     */
    public function modes($controllerID){
        $cmds = array();
        if(($controllerModel = Device::model()->findByPk($controllerID)) !== null){
        
            $jsonParams = json_decode($controllerModel->params, true);
            $port = $jsonParams['serial'];

            foreach($controllerModel->childs as $childModel){
                if(in_array($childModel->type, Yii::app()->params['outputDevices'])){
                    if($childModel->connectType == CONNECT_PIN){
                        $cmds[] = array(CMD_PIN, CMD_TYPE_PIN_MODE_OUTPUT, intval($childModel->pin));
                    }
                } elseif(in_array($childModel->type, Yii::app()->params['inputDevices'])){
                    if($childModel->connectType == CONNECT_PIN){
                        $cmds[] = array(CMD_PIN, CMD_TYPE_PIN_MODE_INPUT, intval($childModel->pin));
                    }
                }                
            }   
            
            foreach($cmds as $cmd){
                $this->sendCommand($cmd, $controllerModel);
            }
        }
        return $cmds;
    }

    /**
     * Получение ID контроллера
     * @param int $controllerID ID контроллера
     * @return array переданная команда
     */
    public function commandControllerID($controllerID){
        $cmd = array();
        if(($controllerModel = Device::model()->findByPk($controllerID)) !== null){
        
            $jsonParams = json_decode($controllerModel->params, true);
            $port = $jsonParams['serial'];

            $cmd = array(CMD_CONTROLLER);
            $this->sendCommand($cmd, $controllerModel);

        }
        return $cmd;
    }
    
    /**
     * Получение свободной памяти
     * @param int $controllerID ID контроллера
     * @return array переданная команда
     */
    public function commandFreeRam($controllerID){
        $cmd = array();
        if(($controllerModel = Device::model()->findByPk($controllerID)) !== null){
        
            $jsonParams = json_decode($controllerModel->params, true);
            $port = $jsonParams['serial'];

            $cmd = array(CMD_FREERAM);
            $this->sendCommand($cmd, $controllerModel);

        }
        return $cmd;
    }
    
    
    public function sendCommand($cmd, $controllerModel){
        $jsonParams = json_decode($controllerModel->params, true);
        $port = $jsonParams['serial'];

        Yii::log(">>> {$controllerModel->caption}: ".implode(", ", $cmd), CLogger::LEVEL_INFO, "arduino.send");
        //var_dump(">>> {$controllerModel->caption}: ".implode(", ", $cmd));
        
        $tcmd = array();
        for($i=0; $i<sizeof($cmd); $i++){
            if(is_string($cmd[$i])){
                $sarr = array_values(unpack("C*", $cmd[$i]));
                $tcmd = array_merge($tcmd, $sarr);
            } else{
                $tcmd[] = $cmd[$i];
            }
        }
        $cmd = $tcmd;
        
        for($i=sizeof($cmd); $i<16; $i++) $cmd[] = 0x0;
        array_unshift($cmd, 'C*'); 
        $binCmd = call_user_func_array('pack', $cmd);
        
        var_dump(bin2hex($binCmd));
        
        try{
            if(($fp = @fopen($port, "w+")) !== false){
                //print "file open: {$port}\n";
                fwrite($fp, $binCmd, 16);
                fclose($fp);
            } else{
                //print "file not open: {$port}\n";
            }
        } catch(Exception $e){
            print $e->getMessage();
        }
        
        Yii::getLogger()->flush(true);
        
    }
    
    /**
     * Обновить виджеты связанные с устройством
     * @param Device $deviceModel устройство
     */
    private function updateWidgetsByDevice($deviceModel){
        
        foreach(SceneWidget::model()->findAll() as $widgetModel){
            if(in_array($widgetModel->type, array(SceneWidget::TYPE_DEVICEVALUE, SceneWidget::TYPE_CHECKBOX))){
                $params = json_decode($widgetModel->params, true);
                if($params['wp_deviceID'] == $deviceModel->id){
                    $params['wp_value'] = $deviceModel->value;
                    
                    $widgetModel->params = json_encode($params);
                    $widgetModel->save();
                }
            }
        }
        
    }
    
    
}

?>