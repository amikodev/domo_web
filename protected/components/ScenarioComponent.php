<?

class ScenarioComponent extends CApplicationComponent{
    
    
    public function loop(){

        while(true){
            foreach(Scenario::model()->with('device', 'params')->findAll() as $scenarioModel){
                $ret = $this->execScenario($scenarioModel);
                if($ret === true){
                    Yii::log("Выполнение сценария [{$scenarioModel->id}]: \"{$scenarioModel->caption}\"", CLogger::LEVEL_INFO, "scenario.action");
                    Yii::log("Выполнение сценария [{$scenarioModel->id}]: \"{$scenarioModel->caption}\"", CLogger::LEVEL_INFO, "main");
                }
                
            }            
            Yii::getLogger()->flush(true);
        }
        
    }
    
    public function doScenaries($scenarioModels){

        foreach($scenarioModels as $scenarioModel){
            $this->execScenario($scenarioModel);
        }
        
    }
    
    
    public function execScenario($scenarioModel, $params=array(), $runNotActived=false){
        
        if($scenarioModel !== null){
            if($scenarioModel->actived || $runNotActived){
                foreach($params as $name=>$param){
                    $$name = $param;
                }
                
                ob_start();
                $scenarioModel->content = str_replace("<?", "", $scenarioModel->content);
                eval($scenarioModel->content);
                $evalOut = ob_get_contents();
                ob_end_clean();
                
            } else{
                return false;
            }
        } else{
            return null;
        }
        
    }
    
    
}

?>