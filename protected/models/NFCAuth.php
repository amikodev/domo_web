<?php

/**
 * This is the model class for table "{{NFCAuth}}".
 *
 * The followings are the available columns in table '{{NFCAuth}}':
 * @property integer $id
 * @property string $uuid
 * @property string $block4
 * @property string $fio
 */
class NFCAuth extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{NFCAuth}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('uuid, block4', 'required'),
			array('uuid', 'length', 'max'=>14),
			array('block4', 'length', 'max'=>32),
			array('scenarioID', 'numerical', 'integerOnly'=>true),
			array('fio', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, uuid, block4, fio', 'safe', 'on'=>'search'),
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
            'history' => array(self::HAS_MANY, 'NFCAuthHistory', 'naID'),
            'scenario' => array(self::BELONGS_TO, 'Scenario', 'scenarioID'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'uuid' => 'Uuid',
			'block4' => 'Block4',
			'fio' => 'Fio',
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
		$criteria->compare('uuid',$this->uuid,true);
		$criteria->compare('block4',$this->block4,true);
		$criteria->compare('fio',$this->fio,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return NFCAuth the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
