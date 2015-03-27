<?

class PluginComponent extends CApplicationComponent{

    protected $pluginModel;
    
    public function __construct($pluginModel){
        $this->pluginModel = $pluginModel;
    }
    
    public function render(){
        
        $name = strtolower(str_replace("PluginComponent", "", $this->pluginModel->name));
        Yii::app()->controller->renderPartial("application.components.plugins.views.{$name}", array("pluginModel"=>$this->pluginModel));
        
    }
    
    public function messageMQ($state, $message){
        Yii::log($message, 'info', 'main');
        Yii::getLogger()->flush(true);
        
        Yii::app()->amqp->publish_message(json_encode(array('recieve'=>array(array('command'=>'PLUGIN_REFRESH', 'ID'=>$this->pluginModel->id, 'state'=>$state, 'message'=>$message)))), 'amq.topic', 'domoWebPage', '');
        
    }
    
    
}