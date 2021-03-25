<?php

/**
 * This is the model class for table "expencess".
 *
 * The followings are the available columns in table 'expencess':
 * @property integer $id
 * @property integer $device_id
 * @property string $img_path
 * @property string $eff_date
 * @property string $total
 * @property string $remarks
 * @property string $created
 * @property integer $online
 *
 * The followings are the available model relations:
 * @property Device $device
 */
class Expencess extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'expencess';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('device_id, img_path, eff_date, total, created', 'required'),
			array('device_id, online', 'numerical', 'integerOnly'=>true),
			array('img_path', 'length', 'max'=>200),
			array('total', 'length', 'max'=>10),
			array('remarks', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, device_id, img_path, eff_date, total, remarks, created, online', 'safe', 'on'=>'search'),
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
			'device' => array(self::BELONGS_TO, 'Device', 'device_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'device_id' => 'Device',
			'img_path' => 'Img Path',
			'eff_date' => 'Eff Date',
			'total' => 'Total',
			'remarks' => 'Remarks',
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
		$criteria->compare('device_id',$this->device_id);
		$criteria->compare('img_path',$this->img_path,true);
		$criteria->compare('eff_date',$this->eff_date,true);
		$criteria->compare('total',$this->total,true);
		$criteria->compare('remarks',$this->remarks,true);
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
	 * @return Expencess the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
