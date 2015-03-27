<?

class MusicCommand extends CConsoleCommand{

    public function actionGetVolume(){
        $volume = Yii::app()->music->getVolume();
        print "volume: {$volume}%\n";
    }

    public function actionSetVolume($volume=0){
        Yii::app()->music->setVolume($volume);
    }
    
    public function actionStop(){
        Yii::app()->music->stop();
    }
    
    
}

?>