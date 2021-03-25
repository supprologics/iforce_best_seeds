<?php

/**
 * This is the model class for table "costing".
 *
 * The followings are the available columns in table 'costing':
 * @property integer $id
 * @property integer $items_id
 * @property integer $rm_id
 * @property double $qty
 * @property integer $is_ceil
 *
 * The followings are the available model relations:
 * @property Items $items
 * @property Items $rm
 */
class Costing extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'costing';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('items_id, rm_id', 'required'),
			array('items_id, rm_id, is_ceil', 'numerical', 'integerOnly'=>true),
			array('qty', 'numerical'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, items_id, rm_id, qty, is_ceil', 'safe', 'on'=>'search'),
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
			'rm' => array(self::BELONGS_TO, 'Items', 'rm_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'items_id' => 'Items',
			'rm_id' => 'Rm',
			'qty' => 'Qty',
			'is_ceil' => 'Is Ceil',
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
		$criteria->compare('items_id',$this->items_id);
		$criteria->compare('rm_id',$this->rm_id);
		$criteria->compare('qty',$this->qty);
		$criteria->compare('is_ceil',$this->is_ceil);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Costing the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
