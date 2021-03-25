<?php

/**
 * This is the model class for table "grn_items".
 *
 * The followings are the available columns in table 'grn_items':
 * @property integer $id
 * @property integer $grn_id
 * @property integer $items_id
 * @property integer $po_items_id
 * @property string $qty
 * @property string $cost
 * @property string $selling
 * @property string $discount
 * @property string $total
 * @property string $notes
 * @property string $batch_no
 * @property string $expire_date
 * @property string $sub_location
 *
 * The followings are the available model relations:
 * @property Grn $grn
 * @property Items $items
 * @property PoItems $poItems
 */
class GrnItems extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'grn_items';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('grn_id, items_id, po_items_id, cost, selling', 'required'),
			array('grn_id, items_id, po_items_id', 'numerical', 'integerOnly'=>true),
			array('qty, cost, selling, discount, total', 'length', 'max'=>10),
			array('notes', 'length', 'max'=>150),
			array('batch_no', 'length', 'max'=>60),
			array('expire_date', 'length', 'max'=>15),
			array('sub_location', 'length', 'max'=>45),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, grn_id, items_id, po_items_id, qty, cost, selling, discount, total, notes, batch_no, expire_date, sub_location', 'safe', 'on'=>'search'),
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
			'grn' => array(self::BELONGS_TO, 'Grn', 'grn_id'),
			'items' => array(self::BELONGS_TO, 'Items', 'items_id'),
			'poItems' => array(self::BELONGS_TO, 'PoItems', 'po_items_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'grn_id' => 'Grn',
			'items_id' => 'Items',
			'po_items_id' => 'Po Items',
			'qty' => 'Qty',
			'cost' => 'Cost',
			'selling' => 'Selling',
			'discount' => 'Discount',
			'total' => 'Total',
			'notes' => 'Notes',
			'batch_no' => 'Batch No',
			'expire_date' => 'Expire Date',
			'sub_location' => 'Sub Location',
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
		$criteria->compare('grn_id',$this->grn_id);
		$criteria->compare('items_id',$this->items_id);
		$criteria->compare('po_items_id',$this->po_items_id);
		$criteria->compare('qty',$this->qty,true);
		$criteria->compare('cost',$this->cost,true);
		$criteria->compare('selling',$this->selling,true);
		$criteria->compare('discount',$this->discount,true);
		$criteria->compare('total',$this->total,true);
		$criteria->compare('notes',$this->notes,true);
		$criteria->compare('batch_no',$this->batch_no,true);
		$criteria->compare('expire_date',$this->expire_date,true);
		$criteria->compare('sub_location',$this->sub_location,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return GrnItems the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
