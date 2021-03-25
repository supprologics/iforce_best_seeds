<?php

/**
 * This is the model class for table "tn".
 *
 * The followings are the available columns in table 'tn':
 * @property integer $id
 * @property integer $device_id
 * @property string $code
 * @property string $bill_bookcode
 * @property string $eff_date
 * @property string $remarks
 * @property string $reamrks_approval
 * @property string $created
 * @property integer $online
 * @property integer $users_id
 *
 * The followings are the available model relations:
 * @property Device $device
 * @property Users $users
 * @property TnItems[] $tnItems
 */
class Tn extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'tn';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('device_id, code, bill_bookcode, eff_date, created, users_id', 'required'),
			array('device_id, online, users_id', 'numerical', 'integerOnly'=>true),
			array('code, bill_bookcode', 'length', 'max'=>45),
			array('remarks, reamrks_approval', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, device_id, code, bill_bookcode, eff_date, remarks, reamrks_approval, created, online, users_id', 'safe', 'on'=>'search'),
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
			'users' => array(self::BELONGS_TO, 'Users', 'users_id'),
			'tnItems' => array(self::HAS_MANY, 'TnItems', 'tn_id'),
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
			'code' => 'Code',
			'bill_bookcode' => 'Bill Bookcode',
			'eff_date' => 'Eff Date',
			'remarks' => 'Remarks',
			'reamrks_approval' => 'Reamrks Approval',
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
		$criteria->compare('device_id',$this->device_id);
		$criteria->compare('code',$this->code,true);
		$criteria->compare('bill_bookcode',$this->bill_bookcode,true);
		$criteria->compare('eff_date',$this->eff_date,true);
		$criteria->compare('remarks',$this->remarks,true);
		$criteria->compare('reamrks_approval',$this->reamrks_approval,true);
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
	 * @return Tn the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
