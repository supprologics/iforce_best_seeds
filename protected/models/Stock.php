<?php

/**
 * This is the model class for table "stock".
 *
 * The followings are the available columns in table 'stock':
 * @property integer $id
 * @property integer $suppliers_id
 * @property integer $device_id
 * @property integer $brands_id
 * @property integer $items_id
 * @property string $qty
 * @property string $qty_ns
 * @property integer $stock_lot
 * @property string $cost
 * @property string $selling
 * @property string $discount
 * @property string $total
 * @property string $remarks
 * @property string $batch_no
 * @property string $expire_date
 * @property string $sub_location
 * @property string $tbl_name
 * @property integer $p_id
 * @property integer $f_id
 * @property string $created
 * @property integer $online
 *
 * The followings are the available model relations:
 * @property Brands $brands
 * @property Device $device
 * @property Items $items
 * @property Suppliers $suppliers
 */
class Stock extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'stock';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('suppliers_id, device_id, brands_id, items_id, cost, selling, tbl_name, p_id, f_id, created', 'required'),
			array('suppliers_id, device_id, brands_id, items_id, stock_lot, p_id, f_id, online', 'numerical', 'integerOnly'=>true),
			array('qty, qty_ns, cost, selling, discount, total', 'length', 'max'=>10),
			array('remarks', 'length', 'max'=>150),
			array('batch_no', 'length', 'max'=>60),
			array('expire_date', 'length', 'max'=>15),
			array('sub_location, tbl_name', 'length', 'max'=>45),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, suppliers_id, device_id, brands_id, items_id, qty, qty_ns, stock_lot, cost, selling, discount, total, remarks, batch_no, expire_date, sub_location, tbl_name, p_id, f_id, created, online', 'safe', 'on'=>'search'),
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
			'brands' => array(self::BELONGS_TO, 'Brands', 'brands_id'),
			'device' => array(self::BELONGS_TO, 'Device', 'device_id'),
			'items' => array(self::BELONGS_TO, 'Items', 'items_id'),
			'suppliers' => array(self::BELONGS_TO, 'Suppliers', 'suppliers_id'),
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
			'brands_id' => 'Brands',
			'items_id' => 'Items',
			'qty' => 'Qty',
			'qty_ns' => 'Qty Ns',
			'stock_lot' => 'Stock Lot',
			'cost' => 'Cost',
			'selling' => 'Selling',
			'discount' => 'Discount',
			'total' => 'Total',
			'remarks' => 'Remarks',
			'batch_no' => 'Batch No',
			'expire_date' => 'Expire Date',
			'sub_location' => 'Sub Location',
			'tbl_name' => 'Tbl Name',
			'p_id' => 'P',
			'f_id' => 'F',
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
		$criteria->compare('suppliers_id',$this->suppliers_id);
		$criteria->compare('device_id',$this->device_id);
		$criteria->compare('brands_id',$this->brands_id);
		$criteria->compare('items_id',$this->items_id);
		$criteria->compare('qty',$this->qty,true);
		$criteria->compare('qty_ns',$this->qty_ns,true);
		$criteria->compare('stock_lot',$this->stock_lot);
		$criteria->compare('cost',$this->cost,true);
		$criteria->compare('selling',$this->selling,true);
		$criteria->compare('discount',$this->discount,true);
		$criteria->compare('total',$this->total,true);
		$criteria->compare('remarks',$this->remarks,true);
		$criteria->compare('batch_no',$this->batch_no,true);
		$criteria->compare('expire_date',$this->expire_date,true);
		$criteria->compare('sub_location',$this->sub_location,true);
		$criteria->compare('tbl_name',$this->tbl_name,true);
		$criteria->compare('p_id',$this->p_id);
		$criteria->compare('f_id',$this->f_id);
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
	 * @return Stock the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
