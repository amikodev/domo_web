<?

class MusicComponent extends CApplicationComponent{
    
    public $path;
    
    public function init(){
        
    }
    
    public function start($fname=null, $folder=null){
        $this->stop();
        
        if($fname !== null){
            $cmd = "nohup mpg123 {$fname} & > /dev/null";
        } elseif($folder !== null){
            $cmd = "nohup find {$folder} -iname '*.mp3' -exec nohup mpg123 {} \\; & > /dev/null";
        } else{
            $cmd = "nohup find {$this->path} -iname *.mp3 -exec mpg123 {} \\; & > /dev/null";
            // find /home/pi/music -name *.mp3 -printf '"%h/%f"\n' | sort | xargs mpg123
            // find /home/pi/music -name *.mp3 | sort | xargs -d '\n' mpg123
        }
        
//        if($fname === null){
//            $cmd = "nohup find ".Yii::app()->basePath."/upload/music/ -name '*.mp3' -exec nohup mpg123 {} \\; & > /dev/null";
//            // nohup find /var/www/html/protected/upload/music/ -name '*.mp3' -exec mpg123 {} \; & > /dev/null
//        } else{
//            $cmd = "nohup mpg123 {$fname} & > /dev/null";
//        }
        print $cmd;
        popen($cmd, 'r');
        //$ps = popen("nohup mpg123 /home/pi/tmp/05LetItBurn.mp3 & > /dev/null", "r");
    }
    
    public function stop(){
        $cmd = 'kill $(ps aux | grep mpg123 | awk \'{print $2}\')';
        popen($cmd, 'r');
    }
    
    public function setVolume($volume){
        $volume = intval($volume);
        $cmd = "amixer sset PCM {$volume}%";
        exec($cmd);
    }
    
    public function getVolume(){
        $cmd = "amixer sget PCM";
        exec($cmd, $output);
        if(preg_match("/\[(\d+)%\]/iU", implode("\n", $output), $out)){
            return $out[1];
        } else{
            return null;
        }
    }
    
    public function next(){
        // find ./ -name "*.mp3"
        // find ./ -name "*.mp3" -exec mpg123 {} \;
    }
    
    
    
}

?>