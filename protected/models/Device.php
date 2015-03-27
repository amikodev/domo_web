<?php

/**
 * This is the model class for table "{{Device}}".
 *
 * The followings are the available columns in table '{{Device}}':
 * @property integer $id
 * @property integer $type
 * @property string $caption
 * @property integer $pin
 * @property string $datechange
 * @property string $value
 * @property integer $parentID
 */
class Device extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{Device}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('type', 'required'),
            array('type, pin, parentID, connectType', 'numerical', 'integerOnly'=>true),
			array('onewireID', 'length', 'max'=>16),
            array('caption, datechange, value, onewireID, params', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, type, caption, pin, datechange, value', 'safe', 'on'=>'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'history' => array(self::HAS_MANY, 'DeviceHistory', 'deviceID'),
            'scenaries' => array(self::HAS_MANY, 'Scenario', 'deviceID'),
            'parent' => array(self::BELONGS_TO, 'Device', 'parentID'),
            'childs' => array(self::HAS_MANY, 'Device', 'parentID'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'type' => 'Тип',
            'caption' => 'Название',
            'pin' => 'Pin',
            'datechange' => 'Дата изменения',
            'value' => 'Значение',
            'parentID' => 'Родитель',
            'onewireID' => 'OneWire ID',
            'connectType' => 'Тип соединения',
            'params' => 'Параметры',
        );
    }
    
    
    public function getControllerID(){
        $controllerModel = null;
        if($this->parent->type == DEV_ARDUINO) $controllerModel = $this->parent;
        else $controllerModel = $this->parent->parent;
        if($controllerModel !== null){
            return $controllerModel->id;
        } else{
            return null;
        }
    }
    
    public function getControllerModel(){
        $controllerModel = null;
        if($this->parent !== null){
            if($this->parent->type == DEV_ARDUINO) $controllerModel = $this->parent;
            elseif(in_array($this->parent->type, array(DEV_REGISTR_IN, DEV_REGISTR_OUT))){

                $cmodel = $this->parent;
                while($cmodel->type != DEV_ARDUINO){
                    if($cmodel->parent !== null){
                        $cmodel = $cmodel->parent;
                    } else{
                        $cmodel = null;
                        break;
                    }
                }
                $controllerModel = $cmodel;

            }
            else $controllerModel = $this->parent->parent;
        }
        return $controllerModel;
    }

    
    public static function getConnectionCaption($connection=null){
        $captions = array(
            CONNECT_OTHER => 'другое',
            CONNECT_PIN => 'pin',
            CONNECT_ONEWIRE => '1wire',
            CONNECT_RADIO => 'radio',
            CONNECT_USB => 'usb',
            CONNECT_TXRX => 'txrx',
            CONNECT_ETHERNET => 'ethernet',
            CONNECT_I2C => 'i2c',
            CONNECT_WIFI => 'wifi',
        );
        
        return $connection !== null ? (isset($captions[$connection]) ? $captions[$connection] : "не известно") : $captions;
    }
    
    public static function getTypeCaption($type=null){
        
        $captions = array(
            DEV_LED => 'Светодиод',
            DEV_BUTTON => 'Кнопка',
            DEV_MOVESENSOR => 'Датчик движения',
            DEV_RELE => 'Реле',
            DEV_TEMPERATURESENSOR => 'Датчик температуры DS18B20',
            DEV_MAGNETOSENSOR => 'Геркон',
            DEV_VIBROSENSOR => 'Датчик вибрации',
            DEV_LASER => 'Лазер',
            DEV_NFC => 'NFC',
            DEV_PHOTORESISTOR => 'Фоторезистор',
            DEV_DHT => 'DHT',
            //'' => '---------------',
            DEV_ARDUINO => 'Arduino',
            DEV_REGISTR_IN => 'Входной сдвиговый регистр',
            DEV_REGISTR_OUT => 'Выходной сдвиговый регистр',
            DEV_1WIRE2 => '1wire DS2413',
            DEV_1WIRE8 => '1wire DS2408',
        );
        
        return $type !== null ? (isset($captions[$type]) ? $captions[$type] : "не известно") : $captions;
    }
    
    

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * Typical usecase:
     * - Initialize the model fields with values from filter form.
     * - Execute this method to get CActiveDataProvider instance which will filter
     * models according to data in model fields.
     * - Pass data provider to CGridView, CListView or any similar widget.
     *
     * @return CActiveDataProvider the data provider that can return the models
     * based on the search/filter conditions.
     */
    public function search()
    {
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria=new CDbCriteria;

        $criteria->compare('id',$this->id);
        $criteria->compare('type',$this->type);
        $criteria->compare('caption',$this->caption,true);
        $criteria->compare('pin',$this->pin);
        //$criteria->compare('datechange',$this->datechange,true);
        //$criteria->compare('value',$this->value,true);

        
        $criteria->compare('parentID',$this->parentID);
        $criteria->compare('onewireID',$this->onewireID, true);
        $criteria->compare('connectType',$this->connectType);
        
        
        
        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
            'pagination' => array(
                'pageSize' => 100,
            ),
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Device the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
}