<?php
class LoginController extends Controller{
    public function actionIndex(){
//      $userinfo = User::model()->find('username=:name',array(':name'=>'admin'));
//        print_r($userinfo);
        $loginForm = new LoginForm();
        //print_r($_POST);exit;

        if(isset($_POST['LoginForm']))
        {
            $loginForm->attributes=$_POST['LoginForm'];
            if($loginForm->validate()&& $loginForm->login()){
                yii::app()->session['logintime'] = time();
                $this->redirect(array('default/index'));
            }

        }

        $this->render('index',array('loginForm'=>$loginForm));
    }
    public function actions(){
        return array(
            'captcha'=>array(
                'class'=>'system.web.widgets.captcha.CCaptchaAction',
                'width'=>80,
                'height'=>25,
                'minLength'=>4,
                'maxLength'=>4
            ),
        );
    }
    public function  actionOut(){
        Yii::app()->user->logout();
        $this->redirect(array('index'));
    }

}