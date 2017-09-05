<?php
// +----------------------------------------------------------------------
// | nthxmai.com 谷创商城夺宝
// +----------------------------------------------------------------------
// | Copyright (c) 2016 http://www.nthxmai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Ant（594177881@qq.com）
// +----------------------------------------------------------------------


class uc_settingApiModule extends MainBaseApiModule
{

	/**
	 * 	 会员中心设置
	 *
	 * 	 输入:
	 *
	 *  输出:
	 * user_login_status:int   0表示未登录   1表示已登录  2表示临时登录
	 * login_info:string 未登录状态的提示信息，已登录时无此项
	 * page_title:string 页面标题
	 *
	 */
	public function index()
	{
		$root = array();
		$user_data = $GLOBALS['user_info'];
		$user_login_status = check_login();
		if($user_login_status!=LOGIN_STATUS_LOGINED){
			$root['user_login_status'] = $user_login_status;
		}else{

			$root['user_login_status'] = $user_login_status;

			$root['page_title'].="设置";
			$root['uid'] = $user_data['id']?$user_data['id']:0;
			$root['user_name'] = $user_data['user_name']?$user_data['user_name']:0;
			$root['user_money_format'] = format_price($user_data['money'])?format_price($user_data['money']):"";//用户金额
			$root['user_money_int'] = $user_data['money']?floatval($user_data['money']):0;//用户金额
			$root['user_score'] = intval($user_data['score']);
			$root['user_score_format'] = format_score($user_data['score']);
			$root['user_avatar'] = get_abs_img_root(get_muser_avatar($user_data['id'],"big"))?get_abs_img_root(get_muser_avatar($user_data['id'],"big")):"";
			$root['user_logo'] = $user_data['user_logo']?get_abs_img_root($user_data['user_logo']):$root['user_avatar'];
			$root['msg_count']=$user_data['msg_count'];
			$user_id = $user_data['id'];

		}

		return output($root);



	}

}
?>
