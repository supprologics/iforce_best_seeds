<?php

/**
 * This is the model class for table "invoice".
 *
 * The followings are the available columns in table 'invoice':
 * @property integer $id
 * @property integer $customers_id
 * @property integer $device_id
 * @property integer $code
 * @property string $bill_bookcode
 * @property string $invoice_discount_total
 * @property string $invoice_net_total
 * @property string $invoice_return_total
 * @property string $invoice_total
 * @property string $invoice_other_discount
 * @property string $invoice_discount
 * @property integer $invoice_discount_type
 * @property string $eff_date
 * @property string $sync_time
 * @property integer $pay_type
 * @property integer $is_synced
 * @property integer $battery_level
 * @property string $latitude
 * @property string $longitude
 * @property string $created
 * @property integer $online
 *
 * The followings are the available model relations:
 * @property Customers $customers
 * @property Device $device
 * @property Ledger[] $ledgers
 * @property Payment[] $payments
 */
class Invoice extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'invoice';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('customers_id, device_id, code, bill_bookcode, eff_date, sync_time, created', 'required'),
			array('customers_id, device_id, code, invoice_discount_type, pay_type, is_synced, battery_level, online', 'numerical', 'integerOnly'=>true),
			array('bill_bookcode', 'length', 'max'=>45),
			array('invoice_discount_total, invoice_net_total, invoice_return_total, invoice_total, invoice_other_discount, invoice_discount, latitude, longitude', 'length', 'max'=>10),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, customers_id, device_id, code, bill_bookcode, invoice_discount_total, invoice_net_total, invoice_return_total, invoice_total, invoice_other_discount, invoice_discount, invoice_discount_type, eff_date, sync_time, pay_type, is_synced, battery_level, latitude, longitude, created, online', 'safe', 'on'=>'search'),
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
			'ledgers' => array(self::HAS_MANY, 'Ledger', 'invoice_id'),
			'payments' => array(self::HAS_MANY, 'Payment', 'invoice_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'customers_id' => 'Customers',
			'device_id' => 'Device',
			'code' => 'Code',
			'bill_bookcode' => 'Bill Bookcode',
			'invoice_discount_total' => 'Invoice Discount Total',
			'invoice_net_total' => 'Invoice Net Total',
			'invoice_return_total' => 'Invoice Return Total',
			'invoice_total' => 'Invoice Total',
			'invoice_other_discount' => 'Invoice Other Discount',
			'invoice_discount' => 'Invoice Discount',
			'invoice_discount_type' => 'Invoice Discount Type',
			'eff_date' => 'Eff Date',
			'sync_time' => 'Sync Time',
			'pay_type' => 'Pay Type',
			'is_synced' => 'Is Synced',
			'battery_level' => 'Battery Level',
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
		$criteria->compare('customers_id',$this->customers_id);
		$criteria->compare('device_id',$this->device_id);
		$criteria->compare('code',$this->code);
		$criteria->compare('bill_bookcode',$this->bill_bookcode,true);
		$criteria->compare('invoice_discount_total',$this->invoice_discount_total,true);
		$criteria->compare('invoice_net_total',$this->invoice_net_total,true);
		$criteria->compare('invoice_return_total',$this->invoice_return_total,true);
		$criteria->compare('invoice_total',$this->invoice_total,true);
		$criteria->compare('invoice_other_discount',$this->invoice_other_discount,true);
		$criteria->compare('invoice_discount',$this->invoice_discount,true);
		$criteria->compare('invoice_discount_type',$this->invoice_discount_type);
		$criteria->compare('eff_date',$this->eff_date,true);
		$criteria->compare('sync_time',$this->sync_time,true);
		$criteria->compare('pay_type',$this->pay_type);
		$criteria->compare('is_synced',$this->is_synced);
		$criteria->compare('battery_level',$this->battery_level);
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
	 * @return Invoice the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
