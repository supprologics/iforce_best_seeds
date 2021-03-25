<?php

/**
 * This is the model class for table "ltn".
 *
 * The followings are the available columns in table 'ltn':
 * @property integer $id
 * @property string $code
 * @property string $bill_bookcode
 * @property integer $device_from
 * @property integer $device_to
 * @property string $eff_date
 * @property string $remarks
 * @property string $created
 * @property integer $online
 * @property string $ltn_type
 * @property integer $users_id
 *
 * The followings are the available model relations:
 * @property Device $deviceFrom
 * @property Device $deviceTo
 * @property Users $users
 * @property LtnItems[] $ltnItems
 */
class Ltn extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'ltn';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('code, device_from, device_to, eff_date, created, users_id', 'required'),
			array('device_from, device_to, online, users_id', 'numerical', 'integerOnly'=>true),
			array('code', 'length', 'max'=>60),
			array('bill_bookcode', 'length', 'max'=>45),
			array('ltn_type', 'length', 'max'=>2),
			array('remarks', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, code, bill_bookcode, device_from, device_to, eff_date, remarks, created, online, ltn_type, users_id', 'safe', 'on'=>'search'),
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
			'deviceFrom' => array(self::BELONGS_TO, 'Device', 'device_from'),
			'deviceTo' => array(self::BELONGS_TO, 'Device', 'device_to'),
			'users' => array(self::BELONGS_TO, 'Users', 'users_id'),
			'ltnItems' => array(self::HAS_MANY, 'LtnItems', 'ltn_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'code' => 'Code',
			'bill_bookcode' => 'Bill Bookcode',
			'device_from' => 'Device From',
			'device_to' => 'Device To',
			'eff_date' => 'Eff Date',
			'remarks' => 'Remarks',
			'created' => 'Created',
			'online' => 'Online',
			'ltn_type' => 'Ltn Type',
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
		$criteria->compare('code',$this->code,true);
		$criteria->compare('bill_bookcode',$this->bill_bookcode,true);
		$criteria->compare('device_from',$this->device_from);
		$criteria->compare('device_to',$this->device_to);
		$criteria->compare('eff_date',$this->eff_date,true);
		$criteria->compare('remarks',$this->remarks,true);
		$criteria->compare('created',$this->created,true);
		$criteria->compare('online',$this->online);
		$criteria->compare('ltn_type',$this->ltn_type,true);
		$criteria->compare('users_id',$this->users_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Ltn the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
