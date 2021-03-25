<?php

/**
 * This is the model class for table "adj_items".
 *
 * The followings are the available columns in table 'adj_items':
 * @property integer $id
 * @property integer $adj_id
 * @property integer $items_id
 * @property string $qty
 * @property string $qty_ns
 * @property string $selling
 * @property string $cost
 * @property string $variance
 * @property string $variance_ns
 * @property string $batch_no
 * @property string $expire_date
 *
 * The followings are the available model relations:
 * @property Adj $adj
 * @property Items $items
 */
class AdjItems extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'adj_items';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('adj_id, items_id', 'required'),
			array('adj_id, items_id', 'numerical', 'integerOnly'=>true),
			array('qty, qty_ns, selling, cost, variance, variance_ns', 'length', 'max'=>10),
			array('batch_no', 'length', 'max'=>60),
			array('expire_date', 'length', 'max'=>15),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, adj_id, items_id, qty, qty_ns, selling, cost, variance, variance_ns, batch_no, expire_date', 'safe', 'on'=>'search'),
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
			'adj' => array(self::BELONGS_TO, 'Adj', 'adj_id'),
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
			'adj_id' => 'Adj',
			'items_id' => 'Items',
			'qty' => 'Qty',
			'qty_ns' => 'Qty Ns',
			'selling' => 'Selling',
			'cost' => 'Cost',
			'variance' => 'Variance',
			'variance_ns' => 'Variance Ns',
			'batch_no' => 'Batch No',
			'expire_date' => 'Expire Date',
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
		$criteria->compare('adj_id',$this->adj_id);
		$criteria->compare('items_id',$this->items_id);
		$criteria->compare('qty',$this->qty,true);
		$criteria->compare('qty_ns',$this->qty_ns,true);
		$criteria->compare('selling',$this->selling,true);
		$criteria->compare('cost',$this->cost,true);
		$criteria->compare('variance',$this->variance,true);
		$criteria->compare('variance_ns',$this->variance_ns,true);
		$criteria->compare('batch_no',$this->batch_no,true);
		$criteria->compare('expire_date',$this->expire_date,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return AdjItems the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
