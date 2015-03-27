<?

/**
 * Плагин "Выключение всего света"
 */

class LightsoffPluginComponent extends PluginComponent{
    
    public function turn_off(){
        $state = 'off';
        
        //Yii::app()->arduino->command(10, CMD_WRITE, 1);
        //Yii::app()->arduino->command(12, CMD_WRITE, 0);
        
        $message = 'Освещение выключено';
        $resultState = 'success';
        $this->messageMQ($resultState, $message);
        
        return array('state'=>$state);
    }
    
}