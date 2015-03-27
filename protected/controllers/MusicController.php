<?

class MusicController extends Controller{
    
    public function actionIndex(){

        if(isset($_GET['start'])){
            
            //$fname = '05 Let It Burn.mp3';
            
            //var_dump($fname);
            //var_dump( str_replace(" ", "\\ ", $fname) );
            
            
            //Yii::app()->music->start('/home/pi/tmp/05LetItBurn.mp3');
            Yii::app()->music->start();
            
            return;
        } elseif(isset($_GET['volume'])){
            Yii::app()->music->setVolume($_GET['volume']);
            return;
        }
        
        
//        if(isset($_GET['start'])){
//            $ps = popen("nohup mpg123 /home/pi/tmp/05LetItBurn.mp3 & > /dev/null", "r");
//            return;
//        } elseif(isset($_GET['volume'])){
//            $volume = intval($_GET['volume']);
//            $ps = popen("amixer sset PCM {$volume}%", "r");
//            return;
//        }
        
        $this->render('music', array());
        
    }
    
}

?>