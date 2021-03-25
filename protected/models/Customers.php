<?php

/**
 * This is the model class for table "customers".
 *
 * The followings are the available columns in table 'customers':
 * @property integer $id
 * @property integer $customer_types_id
 * @property integer $areas_id
 * @property string $code
 * @property string $name
 * @property string $nic
 * @property string $address_no
 * @property string $street
 * @property string $brn
 * @property string $seed_act
 * @property string $contact_name
 * @property string $mobile
 * @property string $landline
 * @property string $latitude
 * @property string $longitude
 * @property integer $is_synced
 * @property string $created
 * @property string $synced
 * @property integer $is_approved
 * @property string $credit_limit
 * @property double $discount
 * @property integer $online
 *
 * The followings are the available model relations:
 * @property Areas $areas
 * @property CustomerTypes $customerTypes
 * @property Invoice[] $invoices
 * @property InvoiceItems[] $invoiceItems
 * @property Ledger[] $ledgers
 * @property Payment[] $payments
 * @property Productivecalls[] $productivecalls
 * @property Shelf[] $shelves
 */
class Customers extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'customers';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('customer_types_id, areas_id, code, name, created, synced', 'required'),
			array('customer_types_id, areas_id, is_synced, is_approved, online', 'numerical', 'integerOnly'=>true),
			array('discount', 'numerical'),
			array('code, brn, seed_act', 'length', 'max'=>60),
			array('name', 'length', 'max'=>150),
			array('nic', 'length', 'max'=>15),
			array('street, contact_name, mobile, landline', 'length', 'max'=>45),
			array('latitude, longitude, credit_limit', 'length', 'max'=>10),
			array('address_no', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, customer_types_id, areas_id, code, name, nic, address_no, street, brn, seed_act, contact_name, mobile, landline, latitude, longitude, is_synced, created, synced, is_approved, credit_limit, discount, online', 'safe', 'on'=>'search'),
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
			'areas' => array(self::BELONGS_TO, 'Areas', 'areas_id'),
			'customerTypes' => array(self::BELONGS_TO, 'CustomerTypes', 'customer_types_id'),
			'invoices' => array(self::HAS_MANY, 'Invoice', 'customers_id'),
			'invoiceItems' => array(self::HAS_MANY, 'InvoiceItems', 'customers_id'),
			'ledgers' => array(self::HAS_MANY, 'Ledger', 'customers_id'),
			'payments' => array(self::HAS_MANY, 'Payment', 'customers_id'),
			'productivecalls' => array(self::HAS_MANY, 'Productivecalls', 'customers_id'),
			'shelves' => array(self::HAS_MANY, 'Shelf', 'customers_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'customer_types_id' => 'Customer Types',
			'areas_id' => 'Areas',
			'code' => 'Code',
			'name' => 'Name',
			'nic' => 'Nic',
			'address_no' => 'Address No',
			'street' => 'Street',
			'brn' => 'Brn',
			'seed_act' => 'Seed Act',
			'contact_name' => 'Contact Name',
			'mobile' => 'Mobile',
			'landline' => 'Landline',
			'latitude' => 'Latitude',
			'longitude' => 'Longitude',
			'is_synced' => 'Is Synced',
			'created' => 'Created',
			'synced' => 'Synced',
			'is_approved' => 'Is Approved',
			'credit_limit' => 'Credit Limit',
			'discount' => 'Discount',
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
		$criteria->compare('customer_types_id',$this->customer_types_id);
		$criteria->compare('areas_id',$this->areas_id);
		$criteria->compare('code',$this->code,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('nic',$this->nic,true);
		$criteria->compare('address_no',$this->address_no,true);
		$criteria->compare('street',$this->street,true);
		$criteria->compare('brn',$this->brn,true);
		$criteria->compare('seed_act',$this->seed_act,true);
		$criteria->compare('contact_name',$this->contact_name,true);
		$criteria->compare('mobile',$this->mobile,true);
		$criteria->compare('landline',$this->landline,true);
		$criteria->compare('latitude',$this->latitude,true);
		$criteria->compare('longitude',$this->longitude,true);
		$criteria->compare('is_synced',$this->is_synced);
		$criteria->compare('created',$this->created,true);
		$criteria->compare('synced',$this->synced,true);
		$criteria->compare('is_approved',$this->is_approved);
		$criteria->compare('credit_limit',$this->credit_limit,true);
		$criteria->compare('discount',$this->discount);
		$criteria->compare('online',$this->online);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Customers the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
