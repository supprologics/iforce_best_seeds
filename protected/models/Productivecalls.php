<?php

/**
 * This is the model class for table "productivecalls".
 *
 * The followings are the available columns in table 'productivecalls':
 * @property integer $id
 * @property integer $device_id
 * @property integer $customers_id
 * @property integer $ptype
 * @property string $remarks
 * @property integer $bat_level
 * @property string $latitude
 * @property string $longitude
 * @property string $created
 * @property integer $online
 *
 * The followings are the available model relations:
 * @property Customers $customers
 * @property Device $device
 */
class Productivecalls extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'productivecalls';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('device_id, customers_id, created', 'required'),
			array('device_id, customers_id, ptype, bat_level, online', 'numerical', 'integerOnly'=>true),
			array('remarks', 'length', 'max'=>250),
			array('latitude, longitude', 'length', 'max'=>10),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, device_id, customers_id, ptype, remarks, bat_level, latitude, longitude, created, online', 'safe', 'on'=>'search'),
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
			'customers' => array(self::BELONGS_TO, 'Customers', 'customers_id'),
			'device' => array(self::BELONGS_TO, 'Device', 'device_id'),
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
			'customers_id' => 'Customers',
			'ptype' => 'Ptype',
			'remarks' => 'Remarks',
			'bat_level' => 'Bat Level',
			'latitude' => 'Latitude',
			'longitude' => 'Longitude',
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
		$criteria->compare('customers_id',$this->customers_id);
		$criteria->compare('ptype',$this->ptype);
		$criteria->compare('remarks',$this->remarks,true);
		$criteria->compare('bat_level',$this->bat_level);
		$criteria->compare('latitude',$this->latitude,true);
		$criteria->compare('longitude',$this->longitude,true);
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
	 * @return Productivecalls the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
