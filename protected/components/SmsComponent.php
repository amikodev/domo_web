<?

/**
 * SMS
 */
class SmsComponent extends CApplicationComponent{
    
    public $gammu;
    
    /**
     * Отправление SMS
     * @param string $number номер телефона
     * @param string $message сообщение
     * @param boolean $flash является типом flash
     */
    public function send($number, $message, $flash=false){
        if(file_exists($this->gammu['config'])){
            $conf = parse_ini_file($this->gammu['config'], true);
            
            $outboxPath = $conf['smsd']['outboxpath'];
            
            $priority = "A"; // [A-Z] A - highest priority
            $datetime = date("Ymd_His");
            $serial = "0";
            $note = "note";
            
            $f = $flash?'f':'';
            $fname = "OUT{$priority}{$datetime}_{$serial}_{$number}_{$note}.txt{$f}";
            file_put_contents("{$outboxPath}/{$fname}", $message);
        }
    }
    
    /**
     * Отправление USSD запроса
     * @param string $number номер
     */
    public function sendUSSD($number){
        print "Number of service: $number \n";

        if(($f = fopen($this->gammu['device1'], "rw+")) !== false){
            fputs($f, "AT+CUSD=1,".$this->encodePDU($number).",15\r\n");

            $time1 = time();
            while($s = fgets($f)) {
                if(substr($s, 0, 5) == "+CUSD"){
                    $s = $this->decodePDU(substr(trim($s), 10, -4));
                    print $s."\n";
                    break;
                }
                $time2 = time();
                if($time2 - $time1 > $this->gammu['timeout']){
                    break;
                }
            }

            fclose($f);
        }
    }

    
    public function decodePDU($sString = ''){
        $sString = pack("H*", $sString);
        $sString = mb_convert_encoding($sString, 'UTF-8', 'UCS-2');
        return $sString;
    }
    
    
    public function encodePDU($in) {
        $out = "";
        for($i = 0; $i < strlen($in); $i++){
            $t = $i%8 + 1;
            if($t == 8) continue;
            $c = ord($in[$i])>>($i%8);
            $oc = $c;
            $code = isset($in[$i+1])?ord($in[$i+1]):0;
            $b = $code & ((1 << $t)-1);
            $c = ($b << (8-$t)) | $c;
            $out .= strtoupper(str_pad(dechex($c), 2, '0', STR_PAD_LEFT));
        }
        return $out;
    }    
    
    
}

?>