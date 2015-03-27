<?
/**
 * NFC
 */
class NFCComponent extends CApplicationComponent{
    
    /**
     * Получение модели авторизации
     * @param string $uuid uuid метки
     * @param string $block4 данные блока 4
     * @return NFCAuth модель авторизации
     */
    public function getAuthModel($uuid, $block4){
        $criteria = new CDbCriteria;
        $criteria->condition = 'uuid=:uuid and block4=:block4';
        $criteria->params = array(
            ':uuid' => $uuid,
            ':block4' => $block4,
        );
        $model = NFCAuth::model()->find($criteria);

        return $model;
    }
    
    /**
     * Сохранение авторизации в историю
     * @param string $uuid uuid метки
     * @param string $block4 данные блока 4
     * @param NFCAuth $authModel модель авторизации
     */
    public function addAuthHistory($uuid, $block4, $authModel=null){
        $historyModel = new NFCAuthHistory;

        if($authModel){
            $historyModel->naID = $authModel->id;
            $historyModel->state = 1;
        } else{
            $historyModel->state = 0;
        }
        
        $historyModel->uuid = $uuid;
        $historyModel->block4 = $block4;
        $historyModel->dateoperation = date("Y-m-d H:i:s");
        
        $historyModel->save();
    }

    /**
     * Запись блока
     * @param int $deviceID ID устройства
     * @param int $block номер блока
     * @param string $data данные
     * @return array отправленная команда
     */
    public function writeBlock($deviceID, $block, $data){
        $deviceModel = Device::model()->findByPk($deviceID);
        if($deviceModel->parent->type == DEV_ARDUINO) $controllerModel = $deviceModel->parent;
        else $controllerModel = $executiveDevice->parent->parent;
        
        return Yii::app()->arduino->command($deviceModel->id, CMD_TYPE_WRITE, array('block'=>$block, 'data'=>$data));
    }
    
}

?>