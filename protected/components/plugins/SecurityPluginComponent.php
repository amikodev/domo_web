<?

class SecurityPluginComponent extends PluginComponent{
    
    public function turn_on(){
        
//        $params = json_decode($this->pluginModel->params, true);
//        $params['status'] = 'on';
//        $this->pluginModel->params = json_encode($params);
//        //var_dump( $this->pluginModel->attributes );
//        return $this->pluginModel->attributes;
        
        $state = 'on';
        Yii::app()->systemParam->save('total_security_state', $state);
        
        Yii::app()->arduino->command(10, CMD_WRITE, 0);
        Yii::app()->arduino->command(12, CMD_WRITE, 1);
        
        $message = 'Общая безопасность включена';
        $resultState = 'success';
        $this->messageMQ($resultState, $message);
        
        return array('state'=>$state);
    }
    
    public function turn_off(){
        $state = 'off';
        Yii::app()->systemParam->save('total_security_state', $state);
        
        Yii::app()->arduino->command(10, CMD_WRITE, 1);
        Yii::app()->arduino->command(12, CMD_WRITE, 0);
        
        $message = 'Общая безопасность выключена';
        $resultState = 'success';
        $this->messageMQ($resultState, $message);
        
        return array('state'=>$state);
    }
    
    
}