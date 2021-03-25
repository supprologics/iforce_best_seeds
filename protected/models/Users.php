<?php

/**
 * This is the model class for table "users".
 *
 * The followings are the available columns in table 'users':
 * @property integer $id
 * @property string $name
 * @property string $email
 * @property string $username
 * @property string $password
 * @property integer $ulevel
 * @property string $created
 * @property integer $online
 *
 * The followings are the available model relations:
 * @property Adj[] $adjs
 * @property Grn[] $grns
 * @property Ledger[] $ledgers
 * @property Ltn[] $ltns
 * @property Payment[] $payments
 * @property Po[] $pos
 * @property Sr[] $srs
 * @property Tn[] $tns
 * @property Useraccess[] $useraccesses
 * @property UsersDevices[] $usersDevices
 */
class Users extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'users';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name, username, password, created', 'required'),
			array('ulevel, online', 'numerical', 'integerOnly'=>true),
			array('name', 'length', 'max'=>60),
			array('email', 'length', 'max'=>150),
			array('username', 'length', 'max'=>45),
			array('password', 'length', 'max'=>32),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, name, email, username, password, ulevel, created, online', 'safe', 'on'=>'search'),
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
			'adjs' => array(self::HAS_MANY, 'Adj', 'users_id'),
			'grns' => array(self::HAS_MANY, 'Grn', 'users_id'),
			'ledgers' => array(self::HAS_MANY, 'Ledger', 'users_id'),
			'ltns' => array(self::HAS_MANY, 'Ltn', 'users_id'),
			'payments' => array(self::HAS_MANY, 'Payment', 'users_id'),
			'pos' => array(self::HAS_MANY, 'Po', 'users_id'),
			'srs' => array(self::HAS_MANY, 'Sr', 'users_id'),
			'tns' => array(self::HAS_MANY, 'Tn', 'users_id'),
			'useraccesses' => array(self::HAS_MANY, 'Useraccess', 'users_id'),
			'usersDevices' => array(self::HAS_MANY, 'UsersDevices', 'users_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'name' => 'Name',
			'email' => 'Email',
			'username' => 'Username',
			'password' => 'Password',
			'ulevel' => 'Ulevel',
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
		$criteria->compare('name',$this->name,true);
		$criteria->compare('email',$this->email,true);
		$criteria->compare('username',$this->username,true);
		$criteria->compare('password',$this->password,true);
		$criteria->compare('ulevel',$this->ulevel);
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
	 * @return Users the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
