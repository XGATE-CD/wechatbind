<?php
class UserController extends controller{
    public function actionPasswd(){
        $userModel = User::model();
        if(isset($_POST['User'])){
            $userModel->attributes = $_POST['User'];
            $userModel->validate();
        }
        //print_r($_POST);
        $this->render('index',array('userModel'=>$userModel));
    }
}