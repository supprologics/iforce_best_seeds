<?php

/**
 * This is the model class for table "invoice_items".
 *
 * The followings are the available columns in table 'invoice_items':
 * @property integer $id
 * @property integer $invoice_item_id
 * @property integer $invoice_code
 * @property integer $customers_id
 * @property integer $items_id
 * @property string $item_name
 * @property integer $qty_selable
 * @property integer $qty_nonselable
 * @property string $mrp
 * @property double $dist_val
 * @property string $discount
 * @property integer $discount_type
 * @property string $discount_amount
 * @property integer $is_manual_dis
 * @property string $total
 * @property integer $device_id
 * @property string $eff_date
 * @property integer $item_type
 *
 * The followings are the available model relations:
 * @property Customers $customers
 * @property Device $device
 * @property Items $items
 */
class InvoiceItems extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'invoice_items';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('invoice_item_id, invoice_code, customers_id, items_id, device_id, eff_date', 'required'),
			array('invoice_item_id, invoice_code, customers_id, items_id, qty_selable, qty_nonselable, discount_type, is_manual_dis, device_id, item_type', 'numerical', 'integerOnly'=>true),
			array('dist_val', 'numerical'),
			array('item_name', 'length', 'max'=>250),
			array('mrp, discount, discount_amount, total', 'length', 'max'=>10),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, invoice_item_id, invoice_code, customers_id, items_id, item_name, qty_selable, qty_nonselable, mrp, dist_val, discount, discount_type, discount_amount, is_manual_dis, total, device_id, eff_date, item_type', 'safe', 'on'=>'search'),
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
			'items' => array(self::BELONGS_TO, 'Items', 'items_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'invoice_item_id' => 'Invoice Item',
			'invoice_code' => 'Invoice Code',
			'customers_id' => 'Customers',
			'items_id' => 'Items',
			'item_name' => 'Item Name',
			'qty_selable' => 'Qty Selable',
			'qty_nonselable' => 'Qty Nonselable',
			'mrp' => 'Mrp',
			'dist_val' => 'Dist Val',
			'discount' => 'Discount',
			'discount_type' => 'Discount Type',
			'discount_amount' => 'Discount Amount',
			'is_manual_dis' => 'Is Manual Dis',
			'total' => 'Total',
			'device_id' => 'Device',
			'eff_date' => 'Eff Date',
			'item_type' => 'Item Type',
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
		$criteria->compare('invoice_item_id',$this->invoice_item_id);
		$criteria->compare('invoice_code',$this->invoice_code);
		$criteria->compare('customers_id',$this->customers_id);
		$criteria->compare('items_id',$this->items_id);
		$criteria->compare('item_name',$this->item_name,true);
		$criteria->compare('qty_selable',$this->qty_selable);
		$criteria->compare('qty_nonselable',$this->qty_nonselable);
		$criteria->compare('mrp',$this->mrp,true);
		$criteria->compare('dist_val',$this->dist_val);
		$criteria->compare('discount',$this->discount,true);
		$criteria->compare('discount_type',$this->discount_type);
		$criteria->compare('discount_amount',$this->discount_amount,true);
		$criteria->compare('is_manual_dis',$this->is_manual_dis);
		$criteria->compare('total',$this->total,true);
		$criteria->compare('device_id',$this->device_id);
		$criteria->compare('eff_date',$this->eff_date,true);
		$criteria->compare('item_type',$this->item_type);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return InvoiceItems the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
