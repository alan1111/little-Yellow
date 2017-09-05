<?php
// +----------------------------------------------------------------------
// | nthxmai.com 谷创商城夺宝
// +----------------------------------------------------------------------
// | Copyright (c) 2016 http://www.nthxmai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Ant（594177881@qq.com）
// +----------------------------------------------------------------------

/**
 * api 登录的接口标准
 */
interface api_login{
	
	function get_api_url();  //用于获取同步登录的api访问url
	
	function callback();     //用于处理 api 访问回调的callback
	
	function get_title();    //后台列表调用的显示信息
	
	function create_user();  //创建用户
	
	function unset_api();    //解除API 绑定
	
	function send_message($data);   //同步微博信息
}
?>