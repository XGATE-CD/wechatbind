<?php
function dump($target,$bool=true){
    static $i = 0;
    if($i==0){
        header('content-type:text/html;charset=utf-8');
    }
    CVarDumper::dump($target,10,true);
    $i++;
    if($bool){
        exit;
    }else{
        echo '<br />';
    }
}

//打印并高亮函数
function p($target,$bool=true){
    static $i = 0;
    if($i==0){
        header('content-type:text/html;charset=utf-8');
    }
    echo '<pre>';
    print_r ($target);
    $i++;
    if($bool){
        exit;
    }else{
        echo '<br />';
    }
}

function fire($target,$name=null){
    if(defined('FB_DEBUG')){
        //Yii::import('application.extensions.debug.*');
        FB::warn($target,$name);
    }
}
class runtime{
    var $StartTime = 0;
    Var $StopTime = 0;
}
?>