<?php

/**
 * This is the model class for table "{{SceneDevice}}".
 *
 * The followings are the available columns in table '{{SceneDevice}}':
 * @property integer $id
 * @property integer $sceneID
 * @property integer $deviceID
 * @property integer $x
 * @property integer $y
 */
class SceneDevice extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{SceneDevice}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('sceneID, deviceID', 'required'),
			array('sceneID, deviceID, x, y, angle, width, height', 'numerical', 'integerOnly'=>true),
			//array('scale', 'numerical'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, sceneID, deviceID, x, y, angle, width, height', 'safe', 'on'=>'search'),
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
			'sceneID' => 'Сцена',
			'deviceID' => 'Устройство',
			'x' => 'X',
			'y' => 'Y',
            'angle' => 'Угол поворота',
            //'scale' => 'Масштаб',
            'width' => 'Ширина',
            'height' => 'Высота',
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
		$criteria->compare('deviceID',$this->deviceID);
		//$criteria->compare('x',$this->x);
		//$criteria->compare('y',$this->y);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return SceneDevice the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
