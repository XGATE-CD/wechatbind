<?php
require_once dirname(__FILE__) . '/protected/components/globals.php';
// change the following paths if necessary
//定义一个yii类的路径
$yii=dirname(__FILE__).'/../yii/framework/yii.php';
//定义了应用的配置文件路径
$config=dirname(__FILE__).'/protected/config/main.php';

// remove the following line when in production mode
 defined('YII_DEBUG') or define('YII_DEBUG',true);

require_once($yii);
//Yii::createWebApplication($config)->run();
$app = Yii::createWebApplication($config);
//dump($app);
//dump(yii::getPathOfAlias('application'));
//$runtime = yii::getPathOfAlias('application.runtime');//运行结果：'D:\\WWW\\blog\\protected\\runtime'
//$runtime = yii::getPathOfAlias('ext');//运行结果：'D:\\WWW\\blog\\protected\\extensions'
//$runtime = yii::getPathOfAlias('webroot');//运行结果：'D:/WWW/blog'
//dump($runtime);
$app->run();
