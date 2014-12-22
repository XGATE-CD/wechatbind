<?php

class WeixinModule extends CWebModule
{
    public $token = 'coyqiuhaowechat1234';
    public $weixin_debug = true;
	public function init()
	{
		// this method is called when the module is being created
		// you may place code here to customize the module or the application

		// import the module-level models and components
		$this->setImport(array(
			'weixin.models.*',
			'weixin.components.*',
            'weixin.message.base.*',
            'weixin.message.utils.*',
            'weixin.message.request.*',
            'weixin.message.response.*',
		));
	}

	public function beforeControllerAction($controller, $action)
	{
		if(parent::beforeControllerAction($controller, $action))
		{
			// this method is called before any module controller action is performed
			// you may place customized code here
			return true;
		}
		else
			return false;
	}
}