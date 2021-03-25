<?php

/**
 * This is the model class for table "useraccess".
 *
 * The followings are the available columns in table 'useraccess':
 * @property integer $id
 * @property integer $users_id
 * @property integer $access_id
 * @property integer $update_
 * @property integer $create_
 * @property integer $view_
 * @property integer $delete_
 * @property integer $approve_
 *
 * The followings are the available model relations:
 * @property Access $access
 * @property Users $users
 */
class Useraccess extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'useraccess';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('users_id, access_id', 'required'),
			array('users_id, access_id, update_, create_, view_, delete_, approve_', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, users_id, access_id, update_, create_, view_, delete_, approve_', 'safe', 'on'=>'search'),
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
			'access' => array(self::BELONGS_TO, 'Access', 'access_id'),
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
			'users_id' => 'Users',
			'access_id' => 'Access',
			'update_' => 'Update',
			'create_' => 'Create',
			'view_' => 'View',
			'delete_' => 'Delete',
			'approve_' => 'Approve',
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
		$criteria->compare('users_id',$this->users_id);
		$criteria->compare('access_id',$this->access_id);
		$criteria->compare('update_',$this->update_);
		$criteria->compare('create_',$this->create_);
		$criteria->compare('view_',$this->view_);
		$criteria->compare('delete_',$this->delete_);
		$criteria->compare('approve_',$this->approve_);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Useraccess the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
