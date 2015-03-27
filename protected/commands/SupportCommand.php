<?

class SupportCommand extends CConsoleCommand{

    /**
     * Проверка работоспособности apache
     */
    public function actionCheckapache(){
        $url = "http://localhost/index.php";
        
        $curl = curl_init();
        
        $options = array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_TIMEOUT => 5,
        );
        
        curl_setopt_array($curl, $options);
        
        $result = curl_exec($curl);
        
        curl_close($curl);
        
        if($result === false){
            $ret = exec("sudo service apache2 restart", $output, $return_var);
        }
        
    }
    
    
    public function actionScenarioloop(){
        Yii::log('Запущен цикл сценариев', 'info', 'main');
        Yii::app()->scenario->loop();
    }
    
    public function actionExec($command){
        Yii::log($command, CLogger::LEVEL_INFO, "exec");
        Yii::log('Выполнение команды: '.$command, CLogger::LEVEL_INFO, 'main');
        Yii::getLogger()->flush(true);
        $ret = exec($command, $output, $return_var);
        Yii::log("{$command} :: ".json_encode(array('ret'=>$ret, 'output'=>$output, 'return_var'=>$return_var)), CLogger::LEVEL_INFO, "exec");
        Yii::getLogger()->flush(true);
    }

    
}

?>