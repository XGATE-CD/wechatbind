<?php

class ApiController extends Controller
{
    // Members
    /**
     * Key which has to be in HTTP USERNAME and PASSWORD headers
     */
    Const APPLICATION_ID = 'ASCCPE';
    /**
     * Default response format
     * either 'json' or 'xml'
     */
    private $format = 'json';
    /**
     * @return array action filters
     */
    public function filters()
    {
        return array();
    }

    // Actions
    public function actionList()
    {
          //$this->_checkAuth();
        // Get the respective model instance
        switch($_GET['model'])
        {
            case 'posts':
                $models = Post::model()->findAll();
                break;
            default:
                // Model not implemented error
                $this->_sendResponse(501, sprintf(
                    'Error: Mode <b>list</b> is not implemented for model <b>%s</b>',
                    $_GET['model']) );
                Yii::app()->end();
        }
        // Did we get some results?
        if(empty($models)) {
            // No
            $this->_sendResponse(200,
                sprintf('No items where found for model <b>%s</b>', $_GET['model']) );
        } else {
            // Prepare response
            $rows = array();
            foreach($models as $model)
                $rows[] = $model->attributes;

            // Send the response
           $this->_sendResponse(200, CJSON::encode($rows));

        }
    }
    public function actionView()
    {
        header("PATH_INFO: demo", true);
        header('Content-Type: json/xml');
        print_r($_SERVER);
        exit();
        //$this->_checkAuth();
        if(!isset($_GET['id']))
            $this->_sendResponse(500, 'ID is missing' );

        if(!isset($_GET['model']))
            $this->_sendResponse(500, 'Model is missing' );

        $returnArr = array();
        $returnArr['id'] = $_GET['id'];
        $returnArr['model'] = $_GET['model'];

        return $this->_sendResponse(200, CJSON::encode($returnArr));

/*        $item = Item::model()->findByPk($_GET['id']);
        if(is_null($item)){
            $this->_sendResponse(404, 'No Item found');
        }else{
            $this->_sendResponse(200, CJSON::encode($item));
           $content = $this->checkMobile($item);
            return $content;
        }*/

    }
    public function actionCreate()
    {
        //$this->_checkAuth();
        $item = new Item;
        foreach($_POST as $var=>$value)
        {
            if($item->hasAttribute($var)){
                $item->$var = $value;
            }else{
                $this->_sendResponse(500, 'Parameter Error');
            }
        }
        if($item->save()){
            $this->_sendResponse(200, CJSON::encode($item));

        }else{
            $this->_sendResponse(500, 'Could not Create Item');
        }
    }
    public function actionUpdate()
    {
       // $this->_checkAuth();
    //获取 put 方法所带来的 json 数据
        $json = file_get_contents('php://input');
        $put_vars = CJSON::decode($json,true);

        $item = Item::model()->findByPk($_GET['id']);

        if(is_null($item))
            $this->_sendResponse(400, 'No Item found');

        foreach($put_vars as $var=>$value)
        {
            if($item->hasAttribute($var)){
                $item->$var = $value;
            }else{
                $this->_sendResponse(500, 'Parameter Error');
            }
        }

        if($item->save()){
            $this->_sendResponse(200, CJSON::encode($item));
        } else{
            $this->_sendResponse(500, 'Could not Update Item');
        }
    }
    public function actionDelete()
    {
        $this->_checkAuth();
        $item = Item::model()->findByPk($_GET['id']);
        if(is_null){
            $this->_sendResponse(400, 'No Item found');
        }
        if($item->delete()){
            $this->_sendResponse(200, 'Delete Success');
        }else{
            $this->_sendResponse(500, 'Could not Delete Item');
        }
    }

    private function _sendResponse($status = 200, $body = '', $content_type = 'text/html')
    {
        // set the status
        $status_header = 'HTTP/1.1 ' . $status . ' ' . $this->_getStatusCodeMessage($status);
        header($status_header);
        // and the content type
        header('Content-type: ' . $content_type);

        // pages with body are easy
        if($body != '')
        {
            // send the body
            echo $body;
        }
        // we need to create the body if none is passed
        else
        {
            // create some body messages
            $message = '';

            // this is purely optional, but makes the pages a little nicer to read
            // for your users.  Since you won't likely send a lot of different status codes,
            // this also shouldn't be too ponderous to maintain
            switch($status)
            {
                case 401:
                    $message = 'You must be authorized to view this page.';
                    break;
                case 404:
                    $message = 'The requested URL ' . $_SERVER['REQUEST_URI'] . ' was not found.';
                    break;
                case 500:
                    $message = 'The server encountered an error processing your request.';
                    break;
                case 501:
                    $message = 'The requested method is not implemented.';
                    break;
            }

            // servers don't always have a signature turned on
            // (this is an apache directive "ServerSignature On")
            $signature = ($_SERVER['SERVER_SIGNATURE'] == '') ? $_SERVER['SERVER_SOFTWARE'] . ' Server at ' . $_SERVER['SERVER_NAME'] . ' Port ' . $_SERVER['SERVER_PORT'] : $_SERVER['SERVER_SIGNATURE'];

            // this should be templated in a real-world solution
            $body = '
                            <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
                            <html>
                            <head>
                                <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
                                <title>' . $status . ' ' . $this->_getStatusCodeMessage($status) . '</title>
                            </head>
                            <body>
                                <h1>' . $this->_getStatusCodeMessage($status) . '</h1>
                                <p>' . $message . '</p>
                                <hr />
                                <address>' . $signature . '</address>
                            </body>
                            </html>';
            echo $body;
        }
        Yii::app()->end();
    }
    private function _checkAuth()
    {
        // Check if we have the USERNAME and PASSWORD HTTP headers set?
        if(!(isset($_SERVER['HTTP_X_USERNAME']) and isset($_SERVER['HTTP_X_PASSWORD']))) {
            // Error: Unauthorized
            //     $this->_sendResponse(401, 'Not find any header...');
            $this->_sendResponse(401, 'Not find any header');
        }
        $username = $_SERVER['HTTP_X_USERNAME'];
        $password = $_SERVER['HTTP_X_PASSWORD'];
        // Find the user
        $user=User::model()->find('LOWER(username)=?',array(strtolower($username)));
        if($user===null) {
            // Error: Unauthorized
            $this->_sendResponse(401, 'Hello no it ...Error: User Name is invalid');
        } else if(!$user->validatePassword($password)) {
            // Error: Unauthorized
            $this->_sendResponse(401, 'Error: User Password is invalid');
        }
    }
    private function _getStatusCodeMessage($status)
    {
        // these could be stored in a .ini file and loaded
        // via parse_ini_file()... however, this will suffice
        // for an example
        $codes = Array(
            200 => 'OK',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
        );
        return (isset($codes[$status])) ? $codes[$status] : '';
    }

    /**
     * actionIndex
     *
     * @access public
     * @return void
     */
    //----------生成验证码---------- //
    public function code()
    {
        $strarr = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz1234567890';
        $l = strlen($strarr); //得到字串的长度;
        //循环随机抽取六位前面定义的字母和数字;
        for($i=1;$i<=6;$i++)
        {
            $num=rand(0,$l-1);
            //每次随机抽取一位数字;从第一个字到该字串最大长度,
            //减1是因为截取字符是从0开始起算;这样34字符任意都有可能排在其中;
            $authnum .= $strarr[$num];
            //将通过数字得来的字符连起来一共是六位;
        }
        return $authnum;

    }
    //----------手机验证---------- //
    private function checkMobile($phone){
        if(preg_match("/^13[0-9]{1}[0-9]{8}$|15[0189]{1}[0-9]{8}$|18[0-9]{9}$/",$phone)){
           return true;// $content = "您的手机号码为：".$phone.",确认请回复【1】。如果有误，请重新点击【会员专区】之【手机绑定】";
        }else{
          return false;// $content = "对不起，您输入的手机号码格式不正确。如需绑定请重新点击【会员专区】之【手机绑定】";
        }
    }
    //----------查询数据库---------- //
//    public function dbSelect($table,$field,$where)
//    {
//        $result = mysql_query("select $field from $table where $where");
//        while($row = mysql_fetch_array($result))
//        {
//            return $row;
//        }
//
//    }
//    //----------写入数据库---------- //
//    public function dbInsert($table,$data)
//    {
//        //遍历$data生成$key_str和$value_str
//        $key_str = "";      // xxx,yyy,zzz
//        $value_str = "";    // 'xxx','yyy','zzz'
//
//        foreach($data as $k => $v)
//        {
//            $key_str .= $k.","; //xxx,yyy,zzz,
//            $value_str .= "'".$v."',"; //'xxx','yyy','zzz',
//        }
//
//        //去掉$key_str, $value_str 最后的一个逗号
//        $key_str = substr($key_str,0,-1);   // xxx,yyy,zzz,  转换为  xxx,yyy,zzz
//        $value_str = substr($value_str,0,-1); //'xxx','yyy','zzz', 转换为 'xxx','yyy','zzz'
//
//        $sql = "insert into {$table}({$key_str}) values({$value_str})";
//
//        mysql_query($sql);
//        return mysql_insert_id();
//    }
//    //----------发送验证码---------- //
//    public function sendCode($fromusername){
//        $code=$this->code();
//        $arr = array();
//        $arr['openid'] = $fromusername;
//        $arr['time'] = date('h:i:s',time());//当前时间
//        $arr['code']=$code;
//        $this->dbInsert('user',$arr);
//
//    }
//
//    public function Confirmation($word,$fromusername){
//        switch($word){
//            case 1:
//               return true;// $content = "尊敬的用户，您已确认需绑定的手机号码，我们将发送验证码至该手机号码，请您于5分钟内微信回复您收到的验证码，以确认绑定。";
//                //$this->sendCode($fromusername);
//                break;
//        }
//        return $content;
//    }
//    //----------验证验证码---------- //
//    public function compareCode($code){
//        $data=$this->dbSelect('user','code','openid=$object->FromUserName');
//        $curtime = date('h:i:s',time());//当前时间
//        $lifetime =5;//有效时间5分钟
//        $exptime = strtotime($data['time'])+5*60;//过期时间
//        if(strcasecmp($code,$data['code'])==0 ){
//            if($exptime>$curtime){
//                return  true;//$content="您的验证码输入正确，您的手机成功绑定本公众平台";
//            }else{
//                return  false;//$content="验证码已超时，请您重新获取验证码！重新获取验证码请输入..(输入关键字)";
//            }
//        }else{
//                return  false;//$content = "验证码错误。";
//        }
//
//    }

}
//	function checklogin( $user, $password )
//	{
//    	if ( emptyempty( $user ) || emptyempty( $password ) )
//        	{
//        	return 0;
//	}
//	$ch = curl_init( );
//	curl_setopt( $ch, CURLOPT_REFERER, "http://158497182.sinaapp.com" );
//	curl_setopt( $ch, CURLOPT_HEADER, true );
//	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
//	curl_setopt( $ch, CURLOPT_USERAGENT, USERAGENT );
//	curl_setopt( $ch, CURLOPT_COOKIEJAR, COOKIEJAR );
//	curl_setopt( $ch, CURLOPT_TIMEOUT, TIMEOUT );
//    curl_setopt( $ch, CURLOPT_URL, "http://mail.sina.com.cn/cgi-bin/login.cgi" );
//    curl_setopt( $ch, CURLOPT_POST, true );
//	curl_setopt( $ch, CURLOPT_POSTFIELDS, "&logintype=uid&u=".urlencode( $user )."&psw=".$password );
//	$contents = curl_exec( $ch );
//	curl_close( $ch );
//	if ( !preg_match( "/Location: (.*)\\/cgi\\/index\\.php\\?check_time=(.*)\n/", $contents, $matches ) )
//       	{
//       return 0;
//	}else{
//        	return 1;
//}
//}
//
//	define( "USERAGENT", $_SERVER['HTTP_USER_AGENT'] );
//	define( "COOKIEJAR", tempnam( "/tmp", "cookie" ) );
//	define( "TIMEOUT", 500 );
//
//	echo checklogin("zhangying215","xtaj227");


?>