<?php

/**
 * This is the model class for table "items".
 *
 * The followings are the available columns in table 'items':
 * @property integer $id
 * @property integer $suppliers_id
 * @property integer $brands_id
 * @property string $item_type
 * @property string $code
 * @property string $item_name
 * @property string $des
 * @property string $short_des
 * @property string $cost
 * @property string $mrp
 * @property integer $re_order
 * @property string $discount
 * @property integer $discount_type
 * @property string $discount_amount
 * @property string $created
 * @property integer $is_stock
 * @property integer $online
 * @property integer $sub_category_id
 * @property integer $volume
 * @property string $unit_type
 *
 * The followings are the available model relations:
 * @property AdjItems[] $adjItems
 * @property Bom[] $boms
 * @property BuferStock[] $buferStocks
 * @property Costing[] $costings
 * @property Costing[] $costings1
 * @property GrnItems[] $grnItems
 * @property InvoiceItems[] $invoiceItems
 * @property Brands $brands
 * @property Suppliers $suppliers
 * @property LtnItems[] $ltnItems
 * @property PoItems[] $poItems
 * @property Shelf[] $shelves
 * @property SrItems[] $srItems
 * @property Stock[] $stocks
 * @property TnItems[] $tnItems
 * @property SubCategory $sub_categories
 */
class Items extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'items';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('suppliers_id, brands_id, item_type, code, item_name, created', 'required'),
			array('suppliers_id, brands_id, re_order, discount_type, is_stock, online, sub_category_id, volume', 'numerical', 'integerOnly'=>true),
			array('item_type', 'length', 'max'=>2),
			array('code', 'length', 'max'=>60),
			array('item_name, des', 'length', 'max'=>250),
			array('short_des, unit_type', 'length', 'max'=>50),
			array('cost, mrp, discount, discount_amount', 'length', 'max'=>10),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, suppliers_id, brands_id, item_type, code, item_name, des, short_des, cost, mrp, re_order, discount, discount_type, discount_amount, created, is_stock, online, sub_category_id', 'safe', 'on'=>'search'),
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
			'adjItems' => array(self::HAS_MANY, 'AdjItems', 'items_id'),
			'boms' => array(self::HAS_MANY, 'Bom', 'items_id'),
			'buferStocks' => array(self::HAS_MANY, 'BuferStock', 'items_id'),
			'costings' => array(self::HAS_MANY, 'Costing', 'items_id'),
			'costings1' => array(self::HAS_MANY, 'Costing', 'rm_id'),
			'grnItems' => array(self::HAS_MANY, 'GrnItems', 'items_id'),
			'invoiceItems' => array(self::HAS_MANY, 'InvoiceItems', 'items_id'),
			'brands' => array(self::BELONGS_TO, 'Brands', 'brands_id'),
			'suppliers' => array(self::BELONGS_TO, 'Suppliers', 'suppliers_id'),
			'ltnItems' => array(self::HAS_MANY, 'LtnItems', 'items_id'),
			'poItems' => array(self::HAS_MANY, 'PoItems', 'items_id'),
			'shelves' => array(self::HAS_MANY, 'Shelf', 'items_id'),
			'srItems' => array(self::HAS_MANY, 'SrItems', 'items_id'),
			'stocks' => array(self::HAS_MANY, 'Stock', 'items_id'),
			'tnItems' => array(self::HAS_MANY, 'TnItems', 'items_id'),
			'subCategory' => array(self::BELONGS_TO, 'SubCategory', 'sub_category_id'),
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
			'brands_id' => 'Brands',
			'item_type' => 'Item Type',
			'code' => 'Code',
			'item_name' => 'Item Name',
			'des' => 'Des',
			'short_des' => 'Short Des',
			'cost' => 'Cost',
			'mrp' => 'Mrp',
			're_order' => 'Re Order',
			'discount' => 'Discount',
			'discount_type' => 'Discount Type',
			'discount_amount' => 'Discount Amount',
			'created' => 'Created',
			'is_stock' => 'Is Stock',
			'online' => 'Online',
			'sub_category_id' => 'Sub Category',
			'volume' => 'Volume',
			'unit_type' => 'Measure Unit Type',
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
		$criteria->compare('brands_id',$this->brands_id);
		$criteria->compare('item_type',$this->item_type,true);
		$criteria->compare('code',$this->code,true);
		$criteria->compare('item_name',$this->item_name,true);
		$criteria->compare('des',$this->des,true);
		$criteria->compare('short_des',$this->short_des,true);
		$criteria->compare('cost',$this->cost,true);
		$criteria->compare('mrp',$this->mrp,true);
		$criteria->compare('re_order',$this->re_order);
		$criteria->compare('discount',$this->discount,true);
		$criteria->compare('discount_type',$this->discount_type);
		$criteria->compare('discount_amount',$this->discount_amount,true);
		$criteria->compare('created',$this->created,true);
		$criteria->compare('is_stock',$this->is_stock);
		$criteria->compare('online',$this->online);
		$criteria->compare('sub_category_id',$this->sub_category_id);
		$criteria->compare('volume',$this->volume);
		$criteria->compare('unit_type',$this->unit_type);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Items the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
