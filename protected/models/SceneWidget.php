<?php

/**
 * This is the model class for table "{{SceneWidget}}".
 *
 * The followings are the available columns in table '{{SceneWidget}}':
 * @property integer $id
 * @property integer $sceneID
 * @property string $caption
 * @property string $params
 */
class SceneWidget extends CActiveRecord
{
    
    const TYPE_TEXT = 1;
    const TYPE_DEVICEVALUE = 2;
    const TYPE_CHECKBOX = 3;
    const TYPE_VIDEO = 4;
    const TYPE_DEVICEMULTIVALUE = 5;
    
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{SceneWidget}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('sceneID, caption, type', 'required'),
			array('sceneID, type', 'numerical', 'integerOnly'=>true),
			array('caption', 'length', 'max'=>255),
			array('params', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, sceneID, caption, params', 'safe', 'on'=>'search'),
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
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'sceneID' => 'Сцена',
			'caption' => 'Название',
			'params' => 'Параметры',
            'type' => 'Тип',
		);
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
		$criteria->compare('sceneID',$this->sceneID);
		$criteria->compare('caption',$this->caption,true);
		$criteria->compare('params',$this->params,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

    
    public static function getType($ind=null){
        $types = array(
            self::TYPE_TEXT => "Текст",
            self::TYPE_DEVICEVALUE => "Значение устройства",
            self::TYPE_CHECKBOX => "Переключатель",
            self::TYPE_VIDEO => "Видео",
            self::TYPE_DEVICEMULTIVALUE => "Устройство с несколькими значениями",
        );
        
        if($ind === null){
            return $types;
        } else{
            return $types[$ind];
        }        
    }
    
	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return SceneWidget the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
