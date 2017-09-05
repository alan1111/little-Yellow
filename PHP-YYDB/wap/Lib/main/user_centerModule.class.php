<?php
// +----------------------------------------------------------------------
// | nthxmai.com 谷创商城夺宝
// +----------------------------------------------------------------------
// | Copyright (c) 2016 http://www.nthxmai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Ant（594177881@qq.com）
// +----------------------------------------------------------------------

class user_centerModule extends MainBaseModule
{

	public function index()
	{
		global_run();
		init_app_page();

		$param=array();
		$data = call_api_core("user_center","index",$param);
		if($data['user_login_status']!=LOGIN_STATUS_LOGINED){
			app_redirect(wap_url("index","user#login"));
		}

		$GLOBALS['tmpl']->assign("data",$data);
		$GLOBALS['tmpl']->display("user_center.html");
	}
	
	public function qrcode(){
	    global_run();
	    init_app_page();
	    $data['page_title'] ="渠道二维码";
	    $data['user_id'] = intval($_REQUEST['data_id']);
	    
	    $user_id = $data['user_id'];
	    include_once APP_ROOT_PATH."system/model/weixin_jssdk.php";
	    $img_url = getQrCode($user_id);
	    
	    $GLOBALS['tmpl']->assign("img_url",$img_url);
	    $GLOBALS['tmpl']->assign("data",$data);
        $GLOBALS['tmpl']->display("qrcode.html");
	}
	
	 
	
	


}
?>
