<?php

/**
 * This is the model class for table "sr".
 *
 * The followings are the available columns in table 'sr':
 * @property integer $id
 * @property integer $suppliers_id
 * @property integer $device_id
 * @property string $sr_type
 * @property string $code
 * @property string $eff_date
 * @property string $remarks
 * @property string $created
 * @property integer $online
 * @property integer $users_id
 *
 * The followings are the available model relations:
 * @property Device $device
 * @property Suppliers $suppliers
 * @property Users $users
 * @property SrItems[] $srItems
 */
class Sr extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'sr';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('suppliers_id, device_id, code, eff_date, created, users_id', 'required'),
			array('suppliers_id, device_id, online, users_id', 'numerical', 'integerOnly'=>true),
			array('sr_type', 'length', 'max'=>3),
			array('code', 'length', 'max'=>45),
			array('remarks', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, suppliers_id, device_id, sr_type, code, eff_date, remarks, created, online, users_id', 'safe', 'on'=>'search'),
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
			'suppliers' => array(self::BELONGS_TO, 'Suppliers', 'suppliers_id'),
			'users' => array(self::BELONGS_TO, 'Users', 'users_id'),
			'srItems' => array(self::HAS_MANY, 'SrItems', 'sr_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'suppliers_id' => 'Suppliers',
			'device_id' => 'Device',
			'sr_type' => 'Sr Type',
			'code' => 'Code',
			'eff_date' => 'Eff Date',
			'remarks' => 'Remarks',
			'created' => 'Created',
			'online' => 'Online',
			'users_id' => 'Users',
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
		$criteria->compare('suppliers_id',$this->suppliers_id);
		$criteria->compare('device_id',$this->device_id);
		$criteria->compare('sr_type',$this->sr_type,true);
		$criteria->compare('code',$this->code,true);
		$criteria->compare('eff_date',$this->eff_date,true);
		$criteria->compare('remarks',$this->remarks,true);
		$criteria->compare('created',$this->created,true);
		$criteria->compare('online',$this->online);
		$criteria->compare('users_id',$this->users_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Sr the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
