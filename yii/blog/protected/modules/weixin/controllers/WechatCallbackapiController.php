<?php
/**
 * Created by PhpStorm.
 * User: hongvi
 * Date: 14-8-10
 * Time: 上午3:18
 */
class WechatCallbackapiController extends CController
{

    //入口
    public function actionIndex()
    {
        $headers = array(
            'Content-Type: application/json',
            'Accept: application/json',
            'HTTP_X_FIREALS_USERNAME: ' . 'demo',
            'HTTP_X_FIREALS_PASSWORD: ' . 'demo',
        );
        $weChatAuthData = array();
        $weChatAuthData['appid'] = 'wxeca2e361be1de14c';
        $weChatAuthData['secret'] = '7af4cee381df0be26495e5260d77400c';
        $weChatAuthUrl = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $weChatAuthData['appid'] .
            '&secret=' . $weChatAuthData['secret'];
    // var_dump($weChatAuthUrl);
        $weChatAuthResponse = $this->createApiCall($weChatAuthUrl, 'GET', $headers);
        $weChatAuthResponse = json_decode($weChatAuthResponse, true);
       //print_r($weChatAuthResponse).'<br>'.'<br>'.'<br>';
        if( isset($weChatAuthResponse['access_token']) ){
            $weChatSendMsgUrl = 'https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=' . $weChatAuthResponse['access_token'];
           // print_r($weChatAuthResponse['access_token']);
          //var_dump($weChatSendMsgUrl);exit;
        }

//        $response = $this->createApiCall('http://158497182', 'get', $headers, $data);//http://sl.shadela.com/index.php?r=api/login
//        $content = json_decode($response, true);
        if($this->checkSignature()){

            if(isset($_GET['echostr'])){
                echo $_GET['echostr'];
            }else{
                $this->responseMsg();
            }
        }
    }

    private function checkSignature()
    {
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
        $token = TOKEN;
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );

        if( $tmpStr == $signature ){
            return true;
        }else{
            return false;
        }
    }

    public function responseMsg()
    {
        //get post data, May be due to the different environments
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];

        //extract post data
        if (!empty($postStr)){

            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            define('FROM_USER_NAME', $postObj->FromUserName);
            define('TO_USER_NAME', $postObj->ToUserName);
            $msg_type = $postObj->MsgType;

            switch($msg_type)
            {
                case 'text':
                    TextMessage::handle($postObj);
                    break;
                case 'event':
                    EventMessage::handle($postObj);
                    break;
                case 'image':
                    ImageMessage::handle($postObj);
                    break;
                case 'voice':
                    VoiceMessage::handle($postObj);
                    break;
                case 'video':
                    VideoMessage::handle($postObj);
                    break;
                case 'location':
                    LocationMessage::handle($postObj);
                    break;
                case 'link':
                    LinkMessage::handle($postObj);
                    break;
                default:
                    echo '';
                    exit;
            }

        }else {
            echo '';
            exit;
        }
    }
    public function createApiCall($url, $method, $headers, $data = array())
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