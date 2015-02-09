<?php
header("Content-Type: text/html;charset=utf-8");
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
         // $this->_checkAuth();
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
    //    $this->_checkAuth();
        if(!isset($_GET['id']))
            $this->_sendResponse(500, 'ID is missing' );

        if(!isset($_GET['model']))
            $this->_sendResponse(500, 'Model is missing' );
        $returnArr = array();
        $returnArr['id'] = $_GET['id'];
        $returnArr['model'] = $_GET['model'];
        if(is_null( $returnArr)){
            return  $this->_sendResponse(404, 'No Item found');
        }else{
        return $this->_sendResponse(200, CJSON::encode($returnArr));
        }

    }
    public function actionCreate()
    {
       $model = $_GET['model'];
       $item = new $model;
      //  print_r($item) ;exit;
        foreach($_POST as $var=>$value){
            return $this->_sendResponse(200, CJSON::encode($item));
        }
    //        if($item->hasAttribute($var)){
    //              $item->$var = $value;
    //              }else{
    //                  $this->_sendResponse(500, 'Parameter Error');
    //              }
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
    $json = file_get_contents('php://input');//file_get_contents()函数把整个文件读入一个字符串中
    $put_vars = CJSON::decode($json,true);
    $item =$_GET['model']::model()->findByPk($_GET['id']);
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
    //$this->_checkAuth();
    $item = $_GET['model']::model()->findByPk($_GET['id']);
    if(is_null($item)){
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




  /*  //>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>手机验证开始<<<<<<<<<<<<<<<<<<<<<<<<<<<<
    public function CheckMobile($data){
       // Yii::log('Start to checkMobile with mobile:' . $data, 'error', 'debugInfo');
        if(preg_match("/^13[0-9]{1}[0-9]{8}$|15[0189]{1}[0-9]{8}$|18[0-9]{9}$/",$data)){
            $content = "您的手机号码为：".$data.",确认请回复【1】。如果有误，请重新点击【会员专区】之【手机绑定】";
        }else{
            $content = "对不起，您输入的手机号码格式不正确。如需绑定请重新点击【会员专区】之【手机绑定】";
        }
        return $content;
    }
    //>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>手机号码验证结束<<<<<<<<<<<<<<<<<<<<<<<<<<<<

    //>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>验证码开始<<<<<<<<<<<<<<<<<<<<<<<<<<<<
    public function compareCode($keyword){
        echo 'compareCode';
    }
    //>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>验证码结束<<<<<<<<<<<<<<<<<<<<<<<<<<<<
    public function sendCode($data){
        echo "sendCode";

    }
    //>>>>>>>>>>>>>>>>>>>>>>>>>>>>接收参数并验证开始<<<<<<<<<<<<<<<<<<<<<<<<<<<<
   public function actionTest(){
       if(!empty($_POST)){
             $mobile = $_POST['mobile'];
           if(strlen($mobile) == 11){
               $result = $this->CheckMobile($mobile);
               if($result){
                   $user = new Client();
                   $user->mobile = $mobile;
                   $user->save();
               }else{
                   return false;
               }
           }else if($mobile == 1){
               $result = "尊敬的用户，您已确认需绑定的手机号码，我们将发送验证码至该手机号码，请您于5分钟内微信回复您收到的验证码，以确认绑定。";
               if($result){
                   $user = new User();
                   $user->mobile = $mobile;
                   $user->save();
               }else{
                   return false;
               }
           }
           echo $result;
       }
    }*/
    //>>>>>>>>>>>>>>>>>>>>>>>>>>>>接收参数并验证结束<<<<<<<<<<<<<<<<<<<<<<<<<<<<


























}



/*ublic function actionTest(){
    Yii::log('Go into action test', 'error', 'debugInfo');
    if(!empty($_POST)){
        Yii::log('Post is not empty', 'error', 'debugInfo');
        $mobile = $_POST['mobile'];
        if(strlen($mobile) == 11){
            Yii::log('Start to check mobile with: ' . $mobile, 'error', 'debugInfo');
            $result = $this->CheckMobile($mobile);
            if($result){
                Yii::log('Start to add a new WcUser with mobile:' . $mobile, 'error', 'debugInfo');
                $user = new WcUser();
                $user->mobile = $mobile;
                if($user->save())
                    Yii::log('Successful to add a new WcUser with mobile:' . $mobile, 'error', 'debugInfo');
                else
                    Yii::log('Fail to add a new WcUser with mobile:' . $mobile, 'error', 'debugInfo');
            }else{
                return false;
            }

        }else if($mobile == 1){
            $result = "尊敬的用户，您已确认需绑定的手机号码，我们将发送验证码至该手机号码，请您于5分钟内微信回复您收到的验证码，以确认绑定。";
            $this->code();
        }
        echo $result;
    }
}*/



