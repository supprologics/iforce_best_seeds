<?php

/**
 * This is the model class for table "po_items".
 *
 * The followings are the available columns in table 'po_items':
 * @property integer $id
 * @property integer $po_id
 * @property integer $items_id
 * @property string $qty
 * @property string $cost
 * @property string $selling
 * @property string $discount
 * @property string $total
 * @property string $notes
 *
 * The followings are the available model relations:
 * @property GrnItems[] $grnItems
 * @property Items $items
 * @property Po $po
 */
class PoItems extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'po_items';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('po_id, items_id, cost, selling', 'required'),
			array('po_id, items_id', 'numerical', 'integerOnly'=>true),
			array('qty, cost, selling, discount, total', 'length', 'max'=>10),
			array('notes', 'length', 'max'=>150),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, po_id, items_id, qty, cost, selling, discount, total, notes', 'safe', 'on'=>'search'),
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
			'grnItems' => array(self::HAS_MANY, 'GrnItems', 'po_items_id'),
			'items' => array(self::BELONGS_TO, 'Items', 'items_id'),
			'po' => array(self::BELONGS_TO, 'Po', 'po_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'po_id' => 'Po',
			'items_id' => 'Items',
			'qty' => 'Qty',
			'cost' => 'Cost',
			'selling' => 'Selling',
			'discount' => 'Discount',
			'total' => 'Total',
			'notes' => 'Notes',
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
		$criteria->compare('po_id',$this->po_id);
		$criteria->compare('items_id',$this->items_id);
		$criteria->compare('qty',$this->qty,true);
		$criteria->compare('cost',$this->cost,true);
		$criteria->compare('selling',$this->selling,true);
		$criteria->compare('discount',$this->discount,true);
		$criteria->compare('total',$this->total,true);
		$criteria->compare('notes',$this->notes,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return PoItems the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
