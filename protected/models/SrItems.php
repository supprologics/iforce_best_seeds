<?php

/**
 * This is the model class for table "sr_items".
 *
 * The followings are the available columns in table 'sr_items':
 * @property integer $id
 * @property integer $sr_id
 * @property integer $items_id
 * @property string $qty
 * @property string $qty_ns
 * @property string $cost
 * @property string $selling
 * @property string $discount
 * @property string $total
 * @property string $remarks
 * @property string $batch_no
 * @property string $expire_date
 * @property string $sub_location
 *
 * The followings are the available model relations:
 * @property Items $items
 * @property Sr $sr
 */
class SrItems extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'sr_items';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('sr_id, items_id, cost', 'required'),
			array('sr_id, items_id', 'numerical', 'integerOnly'=>true),
			array('qty, qty_ns, cost, selling, discount, total', 'length', 'max'=>10),
			array('remarks', 'length', 'max'=>150),
			array('batch_no', 'length', 'max'=>60),
			array('expire_date', 'length', 'max'=>15),
			array('sub_location', 'length', 'max'=>45),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, sr_id, items_id, qty, qty_ns, cost, selling, discount, total, remarks, batch_no, expire_date, sub_location', 'safe', 'on'=>'search'),
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
			'items' => array(self::BELONGS_TO, 'Items', 'items_id'),
			'sr' => array(self::BELONGS_TO, 'Sr', 'sr_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'sr_id' => 'Sr',
			'items_id' => 'Items',
			'qty' => 'Qty',
			'qty_ns' => 'Qty Ns',
			'cost' => 'Cost',
			'selling' => 'Selling',
			'discount' => 'Discount',
			'total' => 'Total',
			'remarks' => 'Remarks',
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
		$criteria->compare('sr_id',$this->sr_id);
		$criteria->compare('items_id',$this->items_id);
		$criteria->compare('qty',$this->qty,true);
		$criteria->compare('qty_ns',$this->qty_ns,true);
		$criteria->compare('cost',$this->cost,true);
		$criteria->compare('selling',$this->selling,true);
		$criteria->compare('discount',$this->discount,true);
		$criteria->compare('total',$this->total,true);
		$criteria->compare('remarks',$this->remarks,true);
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
	 * @return SrItems the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
