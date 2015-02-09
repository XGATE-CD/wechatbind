<?php

class SiteController extends Controller
{
	public $layout='column1';

	/**
	 * Declares class-based actions.
	 */
	public function actions()
	{
		return array(
			// captcha action renders the CAPTCHA image displayed on the contact page
			'captcha'=>array(
				'class'=>'CCaptchaAction',
//				'backColor'=>0xFFFFFF,
                'maxLength'=>6,
                'minLength'=>'6',
			),
			// page action renders "static" pages stored under 'protected/views/site/pages'
			// They can be accessed via: index.php?r=site/page&view=FileName
			'page'=>array(
				'class'=>'CViewAction',
			),
		);
	}

    public function accessRules(){
        return array(
            array('allow',
                'actions'=>array('captcha'),
                'users'=>array('*'),
            ),
        );
    }
//    public function actionLogin(){
//        $model=new LoginForm;
//        if(isset($_POST['ajax']) && $_POST['ajax']==='login-form'){
//            echo CActiveForm::validate($model);
//            Yii::app()->end();
//        }
//
//        if(isset($_POST['LoginForm'])){
//            $model->attributes=$_POST['LoginForm'];
//            // validate user input and redirect to the previous page if valid
//            if($model->validate() &&
//                $model->validateVerifyCode($this->createAction('captcha')->getVerifyCode()) &&
//                $model->login()){
//                $this->redirect(CController::createUrl('default/index'));
//            }
//
//        }
//        $this->render('login',array('model'=>$model));
//    }
	/**
	 * This is the action to handle external exceptions.
	 */
	public function actionError()
	{
	    if($error=Yii::app()->errorHandler->error)
	    {
	    	if(Yii::app()->request->isAjaxRequest)
	    		echo $error['message'];
	    	else
	        	$this->render('error', $error);
	    }
	}

	/**
	 * Displays the contact page
	 */
	public function actionContact()
	{
		$model=new ContactForm;
		if(isset($_POST['ContactForm']))
		{
			$model->attributes=$_POST['ContactForm'];
			if($model->validate())
			{
				$headers="From: {$model->email}\r\nReply-To: {$model->email}";
				mail(Yii::app()->params['adminEmail'],$model->subject,$model->body,$headers);
				Yii::app()->user->setFlash('contact','Thank you for contacting us. We will respond to you as soon as possible.');
				$this->refresh();
			}
		}
		$this->render('contact',array('model'=>$model));
	}

	/**
	 * Displays the login page
	 */
	public function actionLogin()
	{
		if (!defined('CRYPT_BLOWFISH')||!CRYPT_BLOWFISH)
			throw new CHttpException(500,"This application requires that PHP was compiled with Blowfish support for crypt().");

		$model=new LoginForm;

		// if it is ajax validation request
		if(isset($_POST['ajax']) && $_POST['ajax']==='login-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}

		// collect user input data
		if(isset($_POST['LoginForm']))
		{
			$model->attributes=$_POST['LoginForm'];
			// validate user input and redirect to the previous page if valid
			if($model->validate() && $model->login())
				$this->redirect(Yii::app()->user->returnUrl);
		}
		// display the login form
		$this->render('login',array('model'=>$model));
	}

	/**
	 * Logs out the current user and redirect to homepage.
	 */
	public function actionLogout()
	{
		Yii::app()->user->logout();
		$this->redirect(Yii::app()->homeUrl);
	}

    public function actionTest()
    {
        $headers = array(
            'Content-Type: application/json',
            'Accept: application/json',
//            'HTTP_X_USERNAME:' . 'demo',//_FIREALS
//            'HTTP_X_PASSWORD:' . 'demo',
        );
        $data = array();
        $data['touser'] = 'o2Rmrt9B49oJbITFEpNxTaMUVUSw';
        $data['msgtype'] = 'text';
        $data['text']['content'] = 'From SIna hello';

        //  echo 'The link is: ' . 'http://sl.shadela.com/index.php?r=api/login' . '<br>';
       // $response = $this->createApiCall('https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=_c8I-97j0_EZ1BQE5AU5ktTWw4vAXIkN6DRZ4Y9uQLpNj8KzjquE1ivJjxFTZIZE0drENHKeeMl_wph7_HiDXEJJXK_cNHyP5GDDEGhowYQ', 'POST', $headers, $data);//http://sl.shadela.com/index.php?r=api/login
        $response = $this->createApiCall('http://127.0.0.1/wechatbind/blog/index.php/api/user/1', 'get', $headers, 1111111111111);//http://sl.shadela.com/index.php?r=api/login
       // $response = json_decode($response, true);

        //var_dump($response);
    //    print_r($response);

    }

    protected  function createApiCall($url, $method, $headers, $data = array())
    {
        if ($method == 'PUT')
        {
            $headers[] = 'X-HTTP-Method-Override: PUT';
        }

        $handle = curl_init();
        curl_setopt($handle, CURLOPT_URL, $url);//这是你想用PHP取回的URL地址。你也可以在用curl_init()函数初始化时设置这个选项。
        curl_setopt($handle, CURLOPT_HTTPHEADER, $headers);//
        curl_setopt($handle, CURLOPT_HEADER, true);//如果你想把一个头包含在输出中，设置这个选项为一个非零值。
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);//输出内容为字符串
        curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);

        switch($method)
        {
            case 'GET':
                break;
            case 'POST':
                curl_setopt($handle, CURLOPT_POST, true);//允许接收post数据
                curl_setopt($handle, CURLOPT_POSTFIELDS, http_build_query($data));// 传递一个作为HTTP “POST”操作的所有数据的字符串。
                break;
            case 'PUT':
                curl_setopt($handle, CURLOPT_CUSTOMREQUEST, 'PUT');//当进行HTTP请求时，传递一个字符被GET或HEAD使用。为进行DELETE或其它操作是有益的，更Pass a string to be used instead of GET or HEAD when doing an HTTP request. This is useful for doing  or another, more obscure, HTTP request.
                curl_setopt($handle, CURLOPT_POSTFIELDS, http_build_query($data));
                break;
            case 'DELETE':
                curl_setopt($handle, CURLOPT_CUSTOMREQUEST, 'DELETE');
                break;
        }
        $response = curl_exec($handle);

        if ( $response === FALSE ) {
            echo "cURL Error: " . curl_error ( $handle ) ;
            exit();
        } else {
            $info = curl_getinfo ( $handle ) ;
            print_r($info);
        }
        return $response;
    }

}
