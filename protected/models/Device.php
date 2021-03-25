<?php

/**
 * This is the model class for table "device".
 *
 * The followings are the available columns in table 'device':
 * @property integer $id
 * @property integer $region_id
 * @property string $locations
 * @property string $map_area
 * @property string $code
 * @property string $name
 * @property integer $pin
 * @property string $mac_id
 * @property string $dtype
 * @property integer $po_date
 * @property double $target
 * @property integer $stock_lot
 * @property string $address_line1
 * @property string $address_line2
 * @property string $tel_no
 * @property string $tel_no_2
 * @property string $lat
 * @property string $lng
 * @property string $rep_name
 * @property string $created
 * @property integer $device_type
 * @property integer $online
 *
 * The followings are the available model relations:
 * @property Adj[] $adjs
 * @property Areas[] $areases
 * @property BuferStock[] $buferStocks
 * @property Region $region
 * @property Expencess[] $expencesses
 * @property Grn[] $grns
 * @property Invoice[] $invoices
 * @property InvoiceItems[] $invoiceItems
 * @property Ltn[] $ltns
 * @property Ltn[] $ltns1
 * @property Po[] $pos
 * @property Productivecalls[] $productivecalls
 * @property Schedule[] $schedules
 * @property SessionLogins[] $sessionLogins
 * @property Sr[] $srs
 * @property Stock[] $stocks
 * @property Tn[] $tns
 * @property UsersDevices[] $usersDevices
 */
class Device extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'device';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('region_id, locations, map_area, code, name, pin, stock_lot, address_line1, address_line2, tel_no, rep_name, created', 'required'),
			array('region_id, pin, po_date, stock_lot, device_type, online', 'numerical', 'integerOnly'=>true),
			array('target', 'numerical'),
			array('locations, name, address_line1, address_line2, rep_name', 'length', 'max'=>60),
			array('map_area', 'length', 'max'=>5),
			array('code', 'length', 'max'=>4),
			array('mac_id', 'length', 'max'=>32),
			array('dtype', 'length', 'max'=>3),
			array('tel_no, tel_no_2', 'length', 'max'=>15),
			array('lat, lng', 'length', 'max'=>10),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, region_id, locations, map_area, code, name, pin, mac_id, dtype, po_date, target, stock_lot, address_line1, address_line2, tel_no, tel_no_2, lat, lng, rep_name, created, device_type, online', 'safe', 'on'=>'search'),
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
			'adjs' => array(self::HAS_MANY, 'Adj', 'device_id'),
			'areases' => array(self::HAS_MANY, 'Areas', 'device_id'),
			'buferStocks' => array(self::HAS_MANY, 'BuferStock', 'device_id'),
			'region' => array(self::BELONGS_TO, 'Region', 'region_id'),
			'expencesses' => array(self::HAS_MANY, 'Expencess', 'device_id'),
			'grns' => array(self::HAS_MANY, 'Grn', 'device_id'),
			'invoices' => array(self::HAS_MANY, 'Invoice', 'device_id'),
			'invoiceItems' => array(self::HAS_MANY, 'InvoiceItems', 'device_id'),
			'ltns' => array(self::HAS_MANY, 'Ltn', 'device_from'),
			'ltns1' => array(self::HAS_MANY, 'Ltn', 'device_to'),
			'pos' => array(self::HAS_MANY, 'Po', 'device_id'),
			'productivecalls' => array(self::HAS_MANY, 'Productivecalls', 'device_id'),
			'schedules' => array(self::HAS_MANY, 'Schedule', 'device_id'),
			'sessionLogins' => array(self::HAS_MANY, 'SessionLogins', 'device_id'),
			'srs' => array(self::HAS_MANY, 'Sr', 'device_id'),
			'stocks' => array(self::HAS_MANY, 'Stock', 'device_id'),
			'tns' => array(self::HAS_MANY, 'Tn', 'device_id'),
			'usersDevices' => array(self::HAS_MANY, 'UsersDevices', 'device_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'region_id' => 'Region',
			'locations' => 'Locations',
			'map_area' => 'Map Area',
			'code' => 'Code',
			'name' => 'Name',
			'pin' => 'Pin',
			'mac_id' => 'Mac',
			'dtype' => 'Dtype',
			'po_date' => 'Po Date',
			'target' => 'Target',
			'stock_lot' => 'Stock Lot',
			'address_line1' => 'Address Line1',
			'address_line2' => 'Address Line2',
			'tel_no' => 'Tel No',
			'tel_no_2' => 'Tel No 2',
			'lat' => 'Lat',
			'lng' => 'Lng',
			'rep_name' => 'Rep Name',
			'created' => 'Created',
			'device_type' => 'Device Type',
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
		$criteria->compare('region_id',$this->region_id);
		$criteria->compare('locations',$this->locations,true);
		$criteria->compare('map_area',$this->map_area,true);
		$criteria->compare('code',$this->code,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('pin',$this->pin);
		$criteria->compare('mac_id',$this->mac_id,true);
		$criteria->compare('dtype',$this->dtype,true);
		$criteria->compare('po_date',$this->po_date);
		$criteria->compare('target',$this->target);
		$criteria->compare('stock_lot',$this->stock_lot);
		$criteria->compare('address_line1',$this->address_line1,true);
		$criteria->compare('address_line2',$this->address_line2,true);
		$criteria->compare('tel_no',$this->tel_no,true);
		$criteria->compare('tel_no_2',$this->tel_no_2,true);
		$criteria->compare('lat',$this->lat,true);
		$criteria->compare('lng',$this->lng,true);
		$criteria->compare('rep_name',$this->rep_name,true);
		$criteria->compare('created',$this->created,true);
		$criteria->compare('device_type',$this->device_type);
		$criteria->compare('online',$this->online);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Device the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
