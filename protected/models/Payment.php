<?php

/**
 * This is the model class for table "payment".
 *
 * The followings are the available columns in table 'payment':
 * @property integer $id
 * @property integer $customers_id
 * @property integer $invoice_id
 * @property string $code
 * @property string $eff_date
 * @property string $remarks
 * @property string $pay_type
 * @property string $amount
 * @property string $cheque_no
 * @property string $bank_name
 * @property string $branch_name
 * @property string $pd_date
 * @property string $created
 * @property integer $online
 * @property integer $users_id
 *
 * The followings are the available model relations:
 * @property Customers $customers
 * @property Invoice $invoice
 * @property Users $users
 */
class Payment extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'payment';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('customers_id, code, eff_date, created', 'required'),
			array('customers_id, invoice_id, online, users_id', 'numerical', 'integerOnly'=>true),
			array('code, pay_type', 'length', 'max'=>45),
			array('amount', 'length', 'max'=>10),
			array('cheque_no', 'length', 'max'=>60),
			array('bank_name, branch_name', 'length', 'max'=>150),
			array('remarks, pd_date', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, customers_id, invoice_id, code, eff_date, remarks, pay_type, amount, cheque_no, bank_name, branch_name, pd_date, created, online, users_id', 'safe', 'on'=>'search'),
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
			'customers' => array(self::BELONGS_TO, 'Customers', 'customers_id'),
			'invoice' => array(self::BELONGS_TO, 'Invoice', 'invoice_id'),
			'users' => array(self::BELONGS_TO, 'Users', 'users_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'customers_id' => 'Customers',
			'invoice_id' => 'Invoice',
			'code' => 'Code',
			'eff_date' => 'Eff Date',
			'remarks' => 'Remarks',
			'pay_type' => 'Pay Type',
			'amount' => 'Amount',
			'cheque_no' => 'Cheque No',
			'bank_name' => 'Bank Name',
			'branch_name' => 'Branch Name',
			'pd_date' => 'Pd Date',
			'created' => 'Created',
			'online' => 'Online',
			'users_id' => 'Users',
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
		$criteria->compare('customers_id',$this->customers_id);
		$criteria->compare('invoice_id',$this->invoice_id);
		$criteria->compare('code',$this->code,true);
		$criteria->compare('eff_date',$this->eff_date,true);
		$criteria->compare('remarks',$this->remarks,true);
		$criteria->compare('pay_type',$this->pay_type,true);
		$criteria->compare('amount',$this->amount,true);
		$criteria->compare('cheque_no',$this->cheque_no,true);
		$criteria->compare('bank_name',$this->bank_name,true);
		$criteria->compare('branch_name',$this->branch_name,true);
		$criteria->compare('pd_date',$this->pd_date,true);
		$criteria->compare('created',$this->created,true);
		$criteria->compare('online',$this->online);
		$criteria->compare('users_id',$this->users_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Payment the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
