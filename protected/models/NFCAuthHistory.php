<?php

/**
 * This is the model class for table "{{NFCAuthHistory}}".
 *
 * The followings are the available columns in table '{{NFCAuthHistory}}':
 * @property integer $id
 * @property integer $naID
 * @property string $uuid
 * @property string $block4
 * @property string $dateoperation
 * @property integer $state
 */
class NFCAuthHistory extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{NFCAuthHistory}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('uuid, block4, dateoperation', 'required'),
			array('naID, state', 'numerical', 'integerOnly'=>true),
			array('uuid', 'length', 'max'=>14),
			array('block4', 'length', 'max'=>32),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, naID, uuid, block4, dateoperation, state', 'safe', 'on'=>'search'),
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
            'auth' => array(self::BELONGS_TO, 'NFCAuth', 'naID'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'naID' => 'Na',
			'uuid' => 'Uuid',
			'block4' => 'Block4',
			'dateoperation' => 'Dateoperation',
			'state' => 'State',
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
		$criteria->compare('naID',$this->naID);
		$criteria->compare('uuid',$this->uuid,true);
		$criteria->compare('block4',$this->block4,true);
		$criteria->compare('dateoperation',$this->dateoperation,true);
		$criteria->compare('state',$this->state);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return NFCAuthHistory the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
