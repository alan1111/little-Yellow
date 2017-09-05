问题录入系统：http://www.nthxmai.com:808/Home/Login  
一元夺宝本地后台：http://127.0.0.1:81/PHP-YYDB/m.php?m=yydb&a=m.php&m=Index&a=index
一元夺宝网址：http://yyg.nthxmai.com/
谷创电子商务首页：http://www.nthxmai.com
<?php

	//页面跳转 到 网站URL根目录
	app_redirect(APP_ROOT."/");

	//验证码
	$session_verify = es_session::get('verify');

	//ajax返回
	$data['status'] = false;
	$data['info']	=	"图片验证码错误";
	$data['field'] = "verify_code";
	ajax_return($data);

	//删除验证码session值
	es_session::delete("verify");

	//{insert name="load_user_tip"}   这是引入当前目录下的insert目录下的load_user_tip.htm
	<li class="user_tip" id="head_user_tip">{insert name="load_user_tip"}</li>

	define('COMMON_PATH',   APP_PATH.'/Common/'); // 项目公共目录


	//定义项目名称和路径
	define('APP_NAME', 'admin');
	define('APP_PATH', './admin');

?>