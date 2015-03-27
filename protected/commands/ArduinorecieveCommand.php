<?

class ArduinorecieveCommand extends CConsoleCommand{
    

    public function actionStart(){
        try{
            Yii::app()->amqp->publish_message(json_encode(array('type'=>'start')), 'amq.topic', 'domoArduinoProcess', '');
        } catch(Exception $e){
            
        }
        exec("nohup ".Yii::app()->basePath."/yiic {$this->name} begin > /dev/null &");
        
        Yii::log('Начало работы с контроллерами', 'info', 'main');

    }
    
    public function actionBegin(){
        $phs = array();

        try{
            Yii::app()->amqp->publish_message(json_encode(array('type'=>'begin')), 'amq.topic', 'domoArduinoProcess', '');
        } catch(Exception $e){
            
        }
        
        do{
            // проверка на отвалившиеся и новые устройства
            $breaks = array_keys($phs);
            foreach(glob('/dev/serial/by-id/*') as $n=>$device){
                if(strpos($device, 'Arduino') !== false && Yii::app()->systemParam->get('excluded-controllers') !== null && !in_array($device, explode(" | ", Yii::app()->systemParam->get('excluded-controllers')->value))){   // устройство является arduino и не входит в список исключений
                    if(($pos = array_search($device, $breaks)) !== false){
                        array_splice($breaks, $pos, 1);
                    } else{
                        //print "new device {$device}" ."\n";
                        Yii::log('Подключен контроллер '.basename($device), 'info', 'main');
                        try{
                            Yii::app()->amqp->publish_message(json_encode(array('type'=>'connect', 'serialport'=>basename($device))), 'amq.topic', 'domoArduinoProcess', '');
                        } catch(Exception $e){

                        }
                        // вызываем этот же скрипт, но с параметром $device
                        $ph = popen(Yii::app()->basePath."/yiic {$this->name} serialdevice --device={$device}", "r");
                        $phs[$device] = $ph;
                    }
                }
            }
            if(sizeof($breaks) > 0){
                foreach($breaks as $device){
                    //print "break device {$device}" ."\n";
                    Yii::log('Отключен контроллер '.basename($device), 'info', 'main');
                    try{
                        Yii::app()->amqp->publish_message(json_encode(array('type'=>'disconnect', 'serialport'=>basename($device))), 'amq.topic', 'domoArduinoProcess', '');
                    } catch(Exception $e){

                    }
                    pclose($phs[$device]);
                    unset($phs[$device]);
                }
            }
            Yii::getLogger()->flush(true);
        } while(true);
        
    }
    
//    public function actionSerialdevice($device){
//        if(file_exists($device)){
//            ob_start();
//            exec("stty -F {$device} cs8 57600 ignbrk -brkint -icrnl -imaxbel -opost -onlcr -isig -icanon -iexten -echo -echoe -echok -echoctl -echoke noflsh -ixon -crtscts raw");
//            $dh = fopen($device, 'r+b');
//            ob_clean();
//            ob_end_clean();
//
//            $tcontent = '';
//            if($dh){
//                try{
//                    while(($content = fgets($dh)) !== false){
//                        $tcontent .= $content;
//                        //Yii::log("tcontent: {$tcontent} :: {$device}", CLogger::LEVEL_INFO, "arduino.rawrecieve");
//                        if(substr($tcontent, strlen($tcontent)-1) == "\n" && strlen(trim($tcontent)) > 0){
//                            $tcontent = trim($tcontent) ."\n";
//                            try{
//                                Yii::app()->amqp->publish_message(json_encode(array('type'=>'recieve', 'serialport'=>basename($device), 'content'=>$tcontent)), 'amq.topic', 'domoArduinoProcess', '');
//                            } catch(Exception $e){
//
//                            }
//                            $tcontent = '';
//                        }
//                    }
//                } catch(Exception $e){
//
//                }
//                fclose($dh);
//            }
//        }
//    }
    
    public function actionSerialdevice($device){
        if(file_exists($device)){
            ob_start();
            exec("stty -F {$device} cs8 57600 ignbrk -brkint -icrnl -imaxbel -opost -onlcr -isig -icanon -iexten -echo -echoe -echok -echoctl -echoke noflsh -ixon -crtscts raw");
            $dh = fopen($device, 'r+b');
            ob_clean();
            ob_end_clean();

            if($dh){
                try{

                    while(($content = fread($dh, 16)) !== false){
                        $this->hex_dump($content);
                        
                        $hcontent = bin2hex($content);
                        
                        try{
                            Yii::app()->amqp->publish_message(json_encode(array('type'=>'recieve', 'serialport'=>basename($device), 'content'=>$hcontent)), 'amq.topic', 'domoArduinoProcess', '');
                            
                        } catch (Exception $ex) {
                            //var_dump($ex);
                        }
                    }
                } catch(Exception $e){

                }
                fclose($dh);
            }
        }
    }
    
    public function actionTest($value=null){

//        $dat = array(0,0,1,0,1,1,0,1);  // 2D
//        //$bdat = pack("H", bin2hex(implode("", $dat)));
//        //var_dump(implode("", $dat));
//        
//        $arr = unpack("Hout", implode("", $dat));
//        $bdat = decbin(hexdec($arr['out']));
//        var_dump( bin2hex($bdat) );
//        
//        
//        //var_dump( bin2hex(implode("", $dat)) );
//        var_dump( sscanf(implode("", $dat), "%s*", $a) );
//        var_dump($a);
//        
//        return;
        
//        $controllerModel = Device::model()->findByPk(1);
//        $cmd = Yii::app()->arduino->sendCommand(array(CMD_1WIRE, CMD_TYPE_1WIRE_READ, 2, hex2bin("29E4F60100000063")), $controllerModel);
//        return;
        
        $controllerModel = Device::model()->findByPk(1);
        $cmd = Yii::app()->arduino->sendCommand(array(CMD_LISTEN, CMD_1WIRE, CMD_TYPE_1WIRE_READ, 2, hex2bin("29C6F101000000EA")), $controllerModel);
//        return;
//        
//        $controllerModel = Device::model()->findByPk(1);
//        $dataPin = 11;
//        $clockPin = 12;
//        $latchPin = 8;
//        $cmd = Yii::app()->arduino->sendCommand(array(CMD_SHIFT, CMD_TYPE_SHIFT_WRITE, $dataPin, $clockPin, $latchPin, 3, hex2bin($value)), $controllerModel);
//        return;
//
//        $controllerModel = Device::model()->findByPk(17);
//        $cmd = Yii::app()->arduino->sendCommand(array(CMD_DHT, CMD_TYPE_DHT_READ, 0), $controllerModel);
//        return;

        
        $controllerModel = Device::model()->findByPk(1);
        $data = hex2bin("00");
        $cmd = Yii::app()->arduino->sendCommand(array(CMD_1WIRE, CMD_TYPE_1WIRE_WRITE, 2, hex2bin("29C6F101000000EA"), $data), $controllerModel);
        return;
        
        $controllerModel = Device::model()->findByPk(1);
        $cmd = Yii::app()->arduino->sendCommand(array(CMD_NFC, CMD_TYPE_NFC_WRITE, 4, hex2bin("00000000")), $controllerModel);
        return;
        
        
        $controllerModel = Device::model()->findByPk(1);
        //$cmd = Yii::app()->arduino->sendCommand2(array(CMD_1WIRE, CMD_TYPE_1WIRE_READ, 2, hex2bin("3ACA2D0D00000046")), $controllerModel);
        Yii::app()->arduino->command(10, CMD_TYPE_WRITE, 1);
        return;
        
        
        
        $controllerModel = Device::model()->findByPk(1);
        $cmd = Yii::app()->arduino->sendCommand(array(CMD_1WIRE, CMD_TYPE_1WIRE_COUNT, 2), $controllerModel);
        $cmd = Yii::app()->arduino->sendCommand(array(CMD_1WIRE, CMD_TYPE_1WIRE_LIST, 2), $controllerModel);
        return;
        
        

        $controllerModel = Device::model()->findByPk(17);
        $cmd = Yii::app()->arduino->sendCommand(array(CMD_1WIRE, CMD_TYPE_1WIRE_COUNT, 3), $controllerModel);
        $cmd = Yii::app()->arduino->sendCommand(array(CMD_1WIRE, CMD_TYPE_1WIRE_LIST, 3), $controllerModel);
        return;
        
        $cmd = Yii::app()->arduino->radio->changeKeys(14);
        return;
        
        $cmd = Yii::app()->arduino->commandFreeRam(14);
        return;
        
        $cmd = hex2bin("0303022890e671040000500000fec000");
        $f = unpack("f", substr($cmd, 11, 4));
        $value = $f[1];
        var_dump($value);
        
        $onewireID = bin2hex(substr($cmd, 3, 8));
        var_dump($onewireID);
        
        return;
        
        //$cmd = array(CMD_1WIRE, CMD_TYPE_1WIRE_READ, intval("2"), "0x2890E67104000050");
        $cmd = array(CMD_1WIRE, CMD_TYPE_1WIRE_READ, intval("2"), hex2bin("2890E67104000050"), hex2bin("01020304"));
        //$cmd = array(CMD_1WIRE, CMD_TYPE_1WIRE_READ, intval("2"), "\x28");
        
        var_dump($cmd);
        
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
        
        return;
        
        
//        $binarydata = "\x02\x05\x07\x01";
//        $array = unpack("c*", $binarydata);
//        $array = array_values($array);
//        var_dump($array);        
//        
//        return;
        
        $cmd = Yii::app()->arduino->commandControllerID(6);
//        $cmds = Yii::app()->arduino->modes2(6);
//        $cmds = Yii::app()->arduino->listens2(6);
        //$cmd = Yii::app()->arduino->command2(12, CMD_TYPE_WRITE, 1);
        
        //$cmd = Yii::app()->arduino->command2(12, CMD_TYPE_LIST);
        
        //var_dump($cmd);
        
//        $cmd = Yii::app()->arduino->command2(12, CMD_TYPE_WRITE, 1);
//        $cmd = Yii::app()->arduino->command2(12, CMD_TYPE_WRITE, 0);
//        $cmd = Yii::app()->arduino->command2(12, CMD_TYPE_WRITE, 1);
//        $cmd = Yii::app()->arduino->command2(12, CMD_TYPE_READ);
        
        
        
        //var_dump($cmds);
        
        return;
        
        $port = "/dev/serial/by-id/usb-Arduino__www.arduino.cc__Arduino_Uno_85235333135351206201-if00";

        $array = array(0x1, 0x2, 0x30, 0x40, 0x0, 0x50, 0xAF, 0xBB, 0xFA);
        for($i=sizeof($array); $i<16; $i++) $array[] = 0x0;
        array_unshift($array, 'C*'); 
        $cmd = call_user_func_array('pack', $array);
        
        if(($fp = fopen($port, "w+")) !== null){
            fwrite($fp, $cmd, 16);
            fclose($fp);
        }
        
        
        
    }
    
    public function actionTest2($value=null){
        
        //$cmd = Yii::app()->arduino->radioSend(14, $value);
        $cmd = Yii::app()->arduino->radio->send(14, hex2bin($value));     // value = "0102A0EEFF" for example
        return;
        
        //$cmd = Yii::app()->arduino->command2(12, CMD_TYPE_WRITE, $value);
        //$cmd = Yii::app()->arduino->command2(8, CMD_TYPE_COUNT);
        $cmd = Yii::app()->arduino->command2(8, CMD_TYPE_READ);
        //var_dump($cmd);
        
    }
    
    public function actionStop(){
    
        try{
            Yii::app()->amqp->publish_message(json_encode(array('type'=>'stop')), 'amq.topic', 'domoArduinoProcess', '');
        } catch(Exception $e){

        }
        exec("kill `ps aux | grep \"yiic {$this->name}\" | awk '{print $2}'`");
        
        Yii::log('Окончание работы с контроллерами', 'info', 'main');
        
    }
    

    private function hex_dump($data, $newline="\n"){
        static $from = '';
        static $to = '';

        static $width = 16; # number of bytes per line

        static $pad = '.'; # padding for non-visible characters

        if($from===''){
            for($i=0; $i<=0xFF; $i++){
                $from .= chr($i);
                $to .= ($i >= 0x20 && $i <= 0x7E) ? chr($i) : $pad;
            }
        }

        $hex = str_split(bin2hex($data), $width*2);
        $chars = str_split(strtr($data, $from, $to), $width);

        $offset = 0;
        foreach($hex as $i => $line){
            echo sprintf('%6X',$offset).' : '.implode(' ', str_split($line,2)) . ' [' . $chars[$i] . ']' . $newline;
            $offset += $width;
        }
    }    
    
    
}

?>