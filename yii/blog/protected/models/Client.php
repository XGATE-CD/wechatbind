<?php

/**
 * This is the model class for table "{{client}}".
 *
 * The followings are the available columns in table '{{client}}':
 * @property string $id
 * @property string $username
 * @property string $mobile
 * @property string $code
 * @property string $openid
 * @property string $address
 * @property string $time
 * @property string $flag
 */
class Client extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Client the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{client}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('username, address', 'length', 'max'=>100),
			array('mobile', 'length', 'max'=>20),
			array('code', 'length', 'max'=>7),
			array('openid', 'length', 'max'=>50),
			array('time', 'length', 'max'=>10),
			array('flag', 'length', 'max'=>3),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, username, mobile, code, openid, address, time, flag', 'safe', 'on'=>'search'),
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
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'username' => 'Username',
			'mobile' => 'Mobile',
			'code' => 'Code',
			'openid' => 'Openid',
			'address' => 'Address',
			'time' => 'Time',
			'flag' => 'Flag',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('username',$this->username,true);
		$criteria->compare('mobile',$this->mobile,true);
		$criteria->compare('code',$this->code,true);
		$criteria->compare('openid',$this->openid,true);
		$criteria->compare('address',$this->address,true);
		$criteria->compare('time',$this->time,true);
		$criteria->compare('flag',$this->flag,true);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}
}