<?php
class User extends CActiveRecord{//便于数据库的增删改查
    /* *
     * 后台用户模型
     */
    public $password1;
    public $password2;
    public  static function model($className=__CLASS__){
        return parent::model($className);
    }
    /*必不可缺方法2 返回表名 这里必须使用两个{}，这样可以不用加表前缀
     * */
    public  function  tableName(){
        return "{{admin}}";
    }
    public function attributeLabels(){
        return array(
            'password'=>'原始密码',
            'password1'=> '新密码',
            'password2'=> '确认密码'
        );
    }
    public function rules(){
        return array(
            array( 'password','required','message'=>'原始密码必填'),
            array( 'password','check_passwd'),
            array( 'password1','required','message'=>'新密码必填'),
            array( 'password2','required','message'=>'确认密码必填'),
            array( 'password2','compare','compareAttribute'=>'password1','message'=>'两次密码不相同'),
        );
    }
    public function check_passwd(){
        $userInfo = $this->find('username=:name',array(':name'=>yii::app()->user->name));
        if(md5($this->password) != $userInfo->password){
            $this->addError('password','原始密码不正确');
        }
    }
}