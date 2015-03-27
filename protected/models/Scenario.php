<?php

/**
 * This is the model class for table "{{Scenario}}".
 *
 * The followings are the available columns in table '{{Scenario}}':
 * @property integer $id
 * @property string $caption
 * @property integer $delay
 * @property integer $deviceID
 * @property string $content
 */
class Scenario extends CActiveRecord
{
    
    private $_params = array();
    
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{Scenario}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
            array('deviceID', 'required'),
			array('delay, deviceID, actived', 'numerical', 'integerOnly'=>true),
			array('caption', 'length', 'max'=>255),
            array('content', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, caption, delay, deviceID, actived', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations(){
		return array(
            'params' => array(self::HAS_MANY, 'ScenarioParam', 'scenarioID'),
            'device' => array(self::BELONGS_TO, 'Device', 'deviceID'),
            
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'caption' => 'Название',
			'delay' => 'Задержка',
			'deviceID' => 'Устройство',
            'content' => 'Содержимое',
            'actived' => 'Активен',
		);
	}
    
    
    public function getParam($name){
        if(isset($this->_params[$name]))
            return $this->_params[$name];
        else
            return null;
    }
    
    public function setParam($name, $paramModel){
        $this->_params[$name] = $paramModel;
    }
    
    public function saveParam($name, $value){
        if(isset($this->_params[$name])){
            $paramModel = $this->_params[$name];
            $paramModel->value = $value;
            $paramModel->save();
        } else{
            $paramModel = new ScenarioParam;
            $paramModel->scenarioID = $this->id;
            $paramModel->name = $name;
            $paramModel->value = $value;
            $paramModel->save();
        }
    }
    
    /**
     * Удаление параметра
     * @param string $name имя параметра
     * @return boolean|null статус выполнения удаления
     */
    public function deleteParam($name){
        if(isset($this->_params[$name])){
            $paramModel = $this->_params[$name];
            if($paramModel->delete()){
                unset($this->_params[$name]);
                return true;
            } else{
                return false;
            }
        } else{
            return null;
        }
    }
    
    
    protected function afterFind(){
        parent::afterFind();
        
        foreach($this->params as $paramModel){
            $this->_params[$paramModel->name] = $paramModel;
        }
        
        
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
		$criteria->compare('caption',$this->caption,true);
		$criteria->compare('delay',$this->delay);
		$criteria->compare('deviceID',$this->deviceID);
		$criteria->compare('actived',$this->actived);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Scenario the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
