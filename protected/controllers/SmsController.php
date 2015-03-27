<?php

class SmsController extends Controller{
    
    
    public function actionIndex(){
        
//        if(file_exists(Yii::app()->params['gammu'])){
//            $conf = parse_ini_file(Yii::app()->params['gammu'], true);
//            var_dump($conf);
//        }
        
        
        $this->render('index', array());
        
        
    }
    
    public function actionSend(){
        
    }
    
    /**
     * Получение списка sms для отправки
     */
    public function actionGetsmss(){
        $data = array();
        if(file_exists(Yii::app()->params['gammu'])){
            $conf = parse_ini_file(Yii::app()->params['gammu'], true);
            //var_dump($conf);
            $outboxPath = $conf['smsd']['outboxpath'];
            foreach(glob("{$outboxPath}*.txt*") as $path){
                $fname = basename($path);
                preg_match("/^OUT.(\d{4})(\d{2})(\d{2})_(\d{2})(\d{2})(\d{2})_(\d*)_(.+)_.*$/U", $fname, $out);
                //var_dump($out);
                
                $date = "{$out[1]}-{$out[2]}-{$out[3]} {$out[4]}:{$out[5]}:{$out[6]}";
                $number = $out[8];
                $content = file_get_contents($path);
                
                $data[] = array(
                    'fname' => $fname,
                    'date' => $date,
                    'number' => $number,
                    'content' => $content,
                );
                
            }
        }
        print json_encode($data);
    }
    
    /**
     * Уведомление о статусе отправки sms
     * @param string $fname имя файла
     * @param int $status статус
     */
    public function actionSmsdone(){
        if(isset($_POST['fname'])){
            $fname = $_POST['fname'];
            $status = $_POST['status'];
            if(file_exists(Yii::app()->params['gammu'])){
                $conf = parse_ini_file(Yii::app()->params['gammu'], true);
                $outboxPath = $conf['smsd']['outboxpath'];
                $sentsmsPath = $conf['smsd']['sentsmspath'];
                $errorsmsPath = $conf['smsd']['errorsmspath'];

                if(file_exists("{$outboxPath}{$fname}")){
                    if($status == 'ok'){
                        rename("{$outboxPath}{$fname}", "{$sentsmsPath}{$fname}");
                    } else{
                        rename("{$outboxPath}{$fname}", "{$errorsmsPath}{$fname}");
                    }

                }

            }
        }
    }
    
    
}