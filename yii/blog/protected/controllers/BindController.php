<?php
header('Content-Type: text/html;charset=utf8');
class BindController extends Controller{
    public function actionIndex()
    {
        $this->render('index');
    }
    public function actions(){
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
        );
//        if(CCaptcha::checkRequirements()){
//            Yii::app()->controller->widget('CCaptcha');
//            echo $from->textField($model,'verifyCode');
//        }
    }

//    public function accessRules(){
//        return array(
//            array('allow',
//                'actions'=>array('captcha'),
//                'users'=>array('*'),
//            ),
//        );
//    }



    //>>>>>>>>>>>>>>>>>>>>>>>>>>>>接收参数并验证开始<<<<<<<<<<<<<<<<<<<<<<<<<<<<
    public function actionCheckMobile(){    //    $result = json_decode($result,true);
        $client = new Client();
        $openid = $_POST['openid'];           //获取用户ID
        $openid = $this->arr2str($openid);     //将数组转换成字符串$openid = 'op7b7jnIU-TBdHv_hnLlZutAl6hQ';
        $mobile = $_POST['mobile'];           //获取用户输入内容  $mobile = 15902858339;
        $APPID = $_POST['appId'];             //获取微信appid  $APPID = 'wxe5bed39047a398ab';
        $APPSECRET = $_POST['appsecret'];     //获取微信appsecret $APPSECRET = '312eb4e031f30a0010cc8f731d799aac';
        if(!empty($_POST)){
            $TOKEN_URL = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$APPID}&secret={$APPSECRET}";
            $json = file_get_contents($TOKEN_URL);   //执行url地址
            $result = json_decode($json,true);       //将得到的数据转换成数据
            $access_token = $result['access_token'];  //出去得到access_token
            $user_list_url = "http://api.weixin.qq.com/cgi-bin/user/info?access_token={$access_token}&openid={$openid}&lang=zh_CN";
            $user_lists = file_get_contents($user_list_url);   //执行url地址
            $users = json_decode($user_lists,true);      //将得到的数据转换成数据
            $username = $users['nickname'];      //取出用户的昵称
            $address = $users['city'];           //取出用户的地址
            if(preg_match("/^13[0-9]{1}[0-9]{8}$|15[0-9]{1}[0-9]{8}$|18[0-9]{9}|16[0-9]$/",$mobile)){
                $criteria=new CDbCriteria;
                $criteria->select='openid,mobile';
                $criteria->addcondition(array('openid=:openId','mobile=:Mobile'),'or');
                $criteria->params=array(':openId'=>$openid,':Mobile'=>$mobile); //复制给  select mobile,openid from tbl_client where openid=openid or mobile=mobile
                $weChatUser=Client::model()->find($criteria);
                if(isset($weChatUser['mobile'])){
                    $client->updateAll(array('mobile'=>$mobile,'username'=>$username,'address'=>$address),'openid=:openId',array(':openId'=>$openid));
                }else{//alter table brand add column logo varchar(100) not null default '' comment 'LOGO地址' after name;
                    $client->mobile = $mobile;
                    $client->openid = $openid;
                    $client->username = $username;
                    $client->address = $address;
                    $client->time = time();
                    $client->save();
                }
                $content = "尊敬的:".$username."您好,您的手机号码为：".$mobile.",确认请回复【1】，如果有误，请重新点击【会员专区】->【手机绑定】";
            } else if(preg_match("/^[0-9]{8}$/",$mobile)){
                $criteria=new CDbCriteria;
                $criteria->select='openid,mobile';
                $criteria->addcondition(array('openid=:openId','mobile=:Mobile'),'or');
                $criteria->params=array(':openId'=>$openid,':Mobile'=>$mobile); //复制给  select mobile,openid from tbl_client where openid=openid or mobile=mobile
                $weChatUser=Client::model()->find($criteria);
                if(isset($weChatUser['mobile'])){
                    $client->updateAll(array('mobile'=>$mobile),'openid=:openId',array(':openId'=>$openid));
                }else{
                    $client->mobile = $mobile;
                    $client->openid = $openid;
                    $client->time = time();
                    $client->save();
                }
                $content = "尊敬的:".$username."您好,您的手机号码为：".$mobile.",确认请回复【1】，如果有误，请重新点击【会员专区】->【手机绑定】";
            }else{
                $content = "对不起，您输入的手机号码格式不正确。如需绑定请重新点击【会员专区】->【手机绑定】";
            }
            echo $content;
        }
    }
    //>>>>>>>>>>>>>>>>>>>>>>>>>>>验证用户是否已经存在开始<<<<<<<<<<<<<<<<<<<<<<<<<
    public function actionBind(){
        if(!empty($_POST)){
            $openid = $_POST['openid'];
            $criteria=new CDbCriteria;
            $criteria->select='openid,flag';   //select code from tbl_client
            $criteria->addCondition(array('openid=:openId','flag=1'));
            $criteria->params=array(':openId'=>$openid[0]); //复制给  select openid,flag from tbl_client where openid=openid and flag=flag
            $weChatUser=Client::model()->find($criteria); // $params is not needed
            if($weChatUser) {
                $result = '您已经是会员了!!!';
            }else{
                $result = '尊敬的用户，您好！欢迎加入【昊祥科技有限公司】会员俱乐部，请回复您的手机号码，完成身份绑定。';
            }
            echo $result;
        }
    }
    //>>>>>>>>>>>>>>>>>>>>>>>>>>>验证用户是否已经存在结束<<<<<<<<<<<<<<<<<<<<<<<<<



    //>>>>>>>>>>>>>>>>>>>>>>>>>>>>接收参数并验证开始<<<<<<<<<<<<<<<<<<<<<<<<<<<<
    public function actionVerifyCode() {
        if(!empty($_POST)){
            $code = $_POST['code'];   //接收用户请求参数->验证码
            $openid = $_POST['openid'];    //接收用户请求参数->id
            $criteria=new CDbCriteria;      //实例化
            $criteria->select='code,openid,time';   //select code,openid from tbl_client
            $criteria->addCondition(array('openid=:openId','code=:code','time>:Time'));   //where openid=id and code=验证码
            $criteria->params=array(':openId'=>$openid[0], 'code'=>$code,'Time'=>time()); //复制给
            $weChatUser=Client::model()->find($criteria);
            if(isset($weChatUser)) {
                $client =  new Client();
                $yes = '是';
                $client->updateAll(array('flag'=>$yes),'openid=:openId',array(':openId'=>$openid[0]));
                $result = '恭喜您已绑定成功!';
            }else{
                $result = '您输入不正确,请重新输入验证码,或者验证已超时.重新点击【会员专区】->【手机绑定】!';
            }
            echo $result;
        }
    }
    //>>>>>>>>>>>>>>>>>>>>>>>>>>>>接收参数并验证结束<<<<<<<<<<<<<<<<<<<<<<<<<<<<
////Yii::log('1Start to checkMobile with mobile:' . var_dump($weChatUser), 'error', 'debugInfo');
    //>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>验证码开始<<<<<<<<<<<<<<<<<<<<<<<<<<<<
    //>>>>>>>>>>>>>>>>>>>>生成并发送验证码开始<<<<<<<<<<<<<<<<<<<<<<<<<
   public function actionSendCode(){
        $authnum = '';
        $strarr = '0123456789';
        $openid = $_POST['openid'];
        $code = $_POST['code'];
        $openid = $this->arr2str($openid);
        $len = strlen($strarr); //得到字串的长度;
        //循环随机抽取六位前面定义的字母和数字;
        for($i=1;$i<=6;$i++){           //每次随机抽取一位数字;从第一个字到该字串最大长度,
            $num=rand(0,$len-1);            //减1是因为截取字符是从0开始起算;这样34字符任意都有可能排在其中;
            $authnum .= $strarr[$num];    //将通过数字得来的字符连起来一共是六位;
        }
        if($code==1){
            $criteria=new CDbCriteria;      //实例化
            $criteria->select='openid,mobile,flag';   //select code,openid from tbl_client
            $criteria->addCondition(array('openid=:openId'));   //where openid=id and code=验证码
            $criteria->params=array(':openId'=>$openid); //复制给
            $weChatUser=Client::model()->find($criteria);    //执行sql
           if($weChatUser){
                $mobile = $weChatUser->attributes['mobile'];    //从查询出来的数据中获取用户的手机号码
                        $flag = $weChatUser->attributes['flag'];    //从查询出来的数据中获取用户的手机号码
                        if($flag=='否' && !empty($mobile)){
                                $client = new Client();
                                $client->updateAll(array('code'=>$authnum,'time'=>time()+300),'openid=:openId',array(':openId'=>$openid));
                        //>>>>>>>>>>>>>>>>>>发送短信代码<<<<<<<<<<<<<<<<<
                        $username = "coyxgatechina";    //短信平台端账号
                        $password = "coycn9239";   //短信平台端密码
                        $message =urlencode("您的微信身份绑定验证码为:".$authnum."请您于5分钟内在微信中回复您收到的验证码，以确认绑定。") ;//内容解码
                        if(strlen($mobile) == 11) {
                            $mobile = '86'.$mobile;
                            $url = "http://smsc.xgate.com.hk/smshub/sendsms?UserId={$username}&UserPassword={$password}&MessageType=TEXT&MessageLanguage=UTF8&MessageReceiver={$mobile}&MessageBody={$message}";
                        } else if(strlen($mobile) == 8) {
                            $mobile = '852'.$mobile;
                            $url = "http://smsc.xgate.com.hk/smshub/sendsms?UserId={$username}&UserPassword={$password}&MessageType=TEXT&MessageLanguage=UTF8&MessageReceiver={$mobile}&MessageBody={$message}";
                        }
                        file_get_contents($url);
                   //>>>>>>>>>>>>>>>>>>发送短信代码<<<<<<<<<<<<<<<<<
                         $content = '尊敬的用户，您已确认需绑定的手机号码,我们将发送验证码至该手机号码，请您于5分钟内微信回复您收到的验证码，以确认绑定。';
                     }else{
                            $content = '您已经是会员了!';
                        }
            }
            }else{
                $content = '请您重新点击【会员专区】->【手机绑定】';
            }
            echo $content;
  }
        //>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>验证码结束<<<<<<<<<<<<<<<<<<<<<<<<<<<<
        /**
         * 字符串到数组
         */
        function str2arr($str,$delimiter=","){
            return explode($delimiter,$str);
        }
        /**
         * 数组到字符串
         */
        function arr2str($arr,$delimiter=","){
            return implode($delimiter,$arr);
        }





}