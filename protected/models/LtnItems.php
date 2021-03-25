<?php

/**
 * This is the model class for table "ltn_items".
 *
 * The followings are the available columns in table 'ltn_items':
 * @property integer $id
 * @property integer $ltn_id
 * @property integer $items_id
 * @property string $cost
 * @property string $selling
 * @property string $batch_no
 * @property string $expire_date
 * @property string $qty
 * @property string $qty_ns
 *
 * The followings are the available model relations:
 * @property Items $items
 * @property Ltn $ltn
 */
class LtnItems extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'ltn_items';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('ltn_id, items_id, cost, selling', 'required'),
			array('ltn_id, items_id', 'numerical', 'integerOnly'=>true),
			array('cost, selling, qty, qty_ns', 'length', 'max'=>10),
			array('batch_no', 'length', 'max'=>60),
			array('expire_date', 'length', 'max'=>15),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, ltn_id, items_id, cost, selling, batch_no, expire_date, qty, qty_ns', 'safe', 'on'=>'search'),
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
			'ltn' => array(self::BELONGS_TO, 'Ltn', 'ltn_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'ltn_id' => 'Ltn',
			'items_id' => 'Items',
			'cost' => 'Cost',
			'selling' => 'Selling',
			'batch_no' => 'Batch No',
			'expire_date' => 'Expire Date',
			'qty' => 'Qty',
			'qty_ns' => 'Qty Ns',
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
		$criteria->compare('ltn_id',$this->ltn_id);
		$criteria->compare('items_id',$this->items_id);
		$criteria->compare('cost',$this->cost,true);
		$criteria->compare('selling',$this->selling,true);
		$criteria->compare('batch_no',$this->batch_no,true);
		$criteria->compare('expire_date',$this->expire_date,true);
		$criteria->compare('qty',$this->qty,true);
		$criteria->compare('qty_ns',$this->qty_ns,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return LtnItems the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
