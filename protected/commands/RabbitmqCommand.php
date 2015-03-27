<?

class RabbitmqCommand extends CConsoleCommand{
    
    public function actionStart(){
        
    }
    
    public function actionSendMessage($message, $routing='domoWebPage'){
        
        Yii::app()->amqp->publish_message($message, 'amq.topic', $routing, '');
    
    }
    
    public function actionProcess(){
        
        $queueArduino = 'arduinoProcess';
        $queueWeb = 'webProcess';
        
        Yii::app()->amqp->declareQueue($queueArduino, false, false, false, true);
        Yii::app()->amqp->declareQueue($queueWeb, false, false, false, true);

        Yii::app()->amqp->bindQueueExchanger($queueArduino, 'amq.topic', 'domoArduinoProcess');
        Yii::app()->amqp->bindQueueExchanger($queueWeb, 'amq.topic', 'domoWebProcess');
        
        $connect = Yii::app()->amqp->getConnect();
        $channel = Yii::app()->amqp->getChannel();
        
        $channel->basic_consume($queueArduino, 'consumerArduino', false, false, false, false, array($this, 'processMessageArduino'));
        $channel->basic_consume($queueWeb, 'consumerWeb', false, false, false, false, array($this, 'processMessageWeb'));

        Yii::log('Запущен главный процесс', 'info', 'main');
        
        while(count($channel->callbacks)) {
            $channel->wait();
        }
        
        $channel->close();
        $connect->close();
        
    }
    
    public function processMessageArduino($msg){
        
        print "message arduino: {$msg->body} \n";
        
        Yii::log($msg->body, CLogger::LEVEL_INFO, "rabbitmq.domoArduinoProcess");
        Yii::getLogger()->flush(true);
        
        $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
        
        if(($data = json_decode($msg->body, true)) !== null){
            
            if($data['type'] == 'recieve'){
                $hcontent = $data['content'];
                $serial = $data['serialport'];
                
                $content = hex2bin($hcontent);
                
                $controllerID = Yii::app()->arduino->getIdBySerial($serial);
                if($controllerID !== null){
                    $recieve = Yii::app()->arduino->recieve($controllerID, $content);
                    Yii::app()->amqp->publish_message(json_encode(array('recieve'=>$recieve)), 'amq.topic', 'domoWebPage', '');
                }
            }
            
        }
    }
    
    public function processMessageWeb($msg){
        
        print "message web: {$msg->body} \n";
        
        Yii::log($msg->body, CLogger::LEVEL_INFO, "rabbitmq.domoWebProcess");
        Yii::getLogger()->flush(true);
        
        $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);

        if(($data = json_decode($msg->body, true)) !== null){
            $ret = Yii::app()->arduino->command($data['deviceID'], $data['command'], $data['value']);
            
        }   
        
    }
    
}

?>