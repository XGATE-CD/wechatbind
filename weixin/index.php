<?php
header("Content-Type: text/html;charset=utf-8");
define("TOKEN", "dongshao");   //这个token是自己设置的
$wechatObj = new wechatCallbackapiTest();
//$wechatObj->valid();   //验证TOKEN,验证完之后就可以关闭,说明网站的url已经和公众号绑定
//微信用户信息
$wechatObj->responseMsg();

class wechatCallbackapiTest{
     public function headers(){
         $headers = array(
             'Content-Type: application/json',
             'Accept: application/json',
             'HTTP_X_FIREALS_USERNAME: ' . 'demo',
             'HTTP_X_FIREALS_PASSWORD: ' . 'demo',
         );
         return $headers;
     }
    //>>>>>>>>>>>>处理微信公众平台发送过来的信息<<<<<<<<<<<<<<<<<
    public function responseMsg(){
        include_once './test.php';
        //接收微信平台发送过来的信息
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
        // file_get_contents('php://input');//也可以使用这个来得到发送过来的信息,
        //将接收到的信息保存到当前目录下的request.text文件中
        file_put_contents('./response1.text',$postStr);
        //解析出XML信息
        if(!empty($postStr)){
            libxml_disable_entity_loader(true);
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $msgType = $postObj->MsgType;    //类型text,event,event
            $fromUsername = $postObj->FromUserName;  //微信号
            if($msgType == 'event'){
                switch ($postObj->Event){
                    case "subscribe":
                        $Content = "欢迎关注昊祥科技有限公司 ";
                        break;
                    case "CLICK":
                        switch ($postObj->EventKey){
                            case "bind":
                                $keyword = array(
                                    'openid' =>$fromUsername,
                                );

                             $Content =createApiCall('http://www.blog.nat123.net/index.php/bind/bind' , 'POST', $this->headers(),$keyword);
                                file_put_contents('./response2.text',$Content);
                                break;
                            default:
                                $Content = "点击菜单：".$postObj->EventKey;
                                break;
                        }
                        break;
                    case "unsubscribe":
                        $Content = "取消关注";
                        break;
                }
               $result = $this->transmitText($postObj,$Content);
            }elseif($msgType == 'text'){
               $result = $this->receiveText($postObj);
            }
        }
    }
    //>>>>>>>>>>>>接收用户输入的参数开始<<<<<<<<<<<<<<<<<
    private function receiveText($postObj){
            $keywords = trim($postObj->Content);
            $FromUserName = $postObj->FromUserName;
            //Check Mobile when the Keywords are 11 digits
//        include_once './test.php';
            if(strlen($keywords) == 11  && is_numeric($keywords)) {
                $keyword = array(
                    'mobile' => $keywords,
                    'openid' => $FromUserName
                );
                //http://www.blog.nat123.net/
                $content = createApiCall('http://www.blog.nat123.net/index.php/bind/checkMobile' , 'POST', $this->headers(), $keyword);
            } else if(strlen($keywords) == 8  && is_numeric($keywords)) {
                //Check Verification Code when the Keywords are 6 digits
                $keyword = array(
                    'mobile' => $keywords,    //接收的验证码
                    'openid' => $FromUserName
                );
                $content = createApiCall('http://www.blog.nat123.net/index.php/bind/checkMobile' , 'POST', $this->headers(), $keyword);
            } else if(strlen($keywords) == 6 && is_numeric($keywords)) {
                //Check Verification Code when the Keywords are 6 digits
                $keyword = array(
                    'code' => $keywords,    //接收的验证码
                    'openid' => $FromUserName
                );
                $content = createApiCall('http://www.blog.nat123.net/index.php/bind/verifyCode' , 'POST', $this->headers(), $keyword);
            }else if($keywords == 1  && is_numeric($keywords)){
                $keyword = array(
                    'code' => $keywords,   //发送的验证码
                    'openid' => $FromUserName
                );
                //$content = '尊敬的用户，您已确认需绑定的手机号码，我们将发送验证码至该手机号码，请您于5分钟内微信回复您收到的验证码，以确认绑定。';
                $content = createApiCall('http://www.blog.nat123.net/index.php/bind/sendCode' , 'POST', $this->headers(), $keyword);
                file_put_contents('./response3.text',$content);
            }else{
            $content = '您是输入有误,请重新输入!';
            }
        $result = $this->transmitText($postObj,$content);
}
    //>>>>>>>>>>>>接收用户输入的参数结束<<<<<<<<<<<<<<<<<

    private function transmitText($object, $content){
        $textTpl = "<xml>
        <ToUserName><![CDATA[%s]]></ToUserName>
        <FromUserName><![CDATA[%s]]></FromUserName>
        <CreateTime>%s</CreateTime>
        <MsgType><![CDATA[text]]></MsgType>
        <Content><![CDATA[%s]]></Content>
        </xml>";
        $result = sprintf($textTpl, $object->FromUserName, $object->ToUserName, time(), $content);
        echo $result;
    }











    //>>>>>>>>>>>验证TOKEN代码开始<<<<<<<<<<<<<<<<
    public function valid(){
        $echoStr = $_GET["echostr"];
        //valid signature , option
        if($this->checkSignature()){
            echo $echoStr;
            exit;
        }
    }
    private function checkSignature(){
        // you must define TOKEN by yourself
        if (!defined("TOKEN")){
            throw new Exception('TOKEN is not defined!');
        }
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
        $token = TOKEN;
        $tmpArr = array($token, $timestamp, $nonce);
        // use SORT_STRING rule
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );
        if( $tmpStr == $signature ){
            return true;
        }else{
            return false;
        }
    }
    //>>>>>>>>>>>验证TOKEN代码结束<<<<<<<<<<<<<<<<


}
