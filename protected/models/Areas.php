<?php

/**
 * This is the model class for table "areas".
 *
 * The followings are the available columns in table 'areas':
 * @property integer $id
 * @property integer $device_id
 * @property string $name
 * @property string $last_date
 * @property integer $is_dir
 * @property string $created
 * @property integer $online
 *
 * The followings are the available model relations:
 * @property Device $device
 * @property Customers[] $customers
 * @property Schedule[] $schedules
 * @property SessionLogins[] $sessionLogins
 */
class Areas extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'areas';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('device_id, name, created', 'required'),
			array('device_id, is_dir, online', 'numerical', 'integerOnly'=>true),
			array('name', 'length', 'max'=>150),
			array('last_date', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, device_id, name, last_date, is_dir, created, online', 'safe', 'on'=>'search'),
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
			'device' => array(self::BELONGS_TO, 'Device', 'device_id'),
			'customers' => array(self::HAS_MANY, 'Customers', 'areas_id'),
			'schedules' => array(self::HAS_MANY, 'Schedule', 'areas_id'),
			'sessionLogins' => array(self::HAS_MANY, 'SessionLogins', 'areas_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'device_id' => 'Device',
			'name' => 'Name',
			'last_date' => 'Last Date',
			'is_dir' => 'Is Dir',
			'created' => 'Created',
			'online' => 'Online',
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
		$criteria->compare('device_id',$this->device_id);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('last_date',$this->last_date,true);
		$criteria->compare('is_dir',$this->is_dir);
		$criteria->compare('created',$this->created,true);
		$criteria->compare('online',$this->online);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Areas the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
