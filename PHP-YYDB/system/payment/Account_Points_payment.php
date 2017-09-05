<?php
// +----------------------------------------------------------------------
// | nthxmai.com 谷创商城夺宝
// +----------------------------------------------------------------------
// | Copyright (c) 2016 http://www.nthxmai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Ant（594177881@qq.com）
// +----------------------------------------------------------------------

$payment_lang = array(
	'name'	=>	'通宝支付',
	'account_credit'	=>	'帐户通宝',
	'use_user_money' =>	'使用通宝支付',
	'use_all_money'	=>	'全额支付',
	'USER_ORDER_PAID'	=>	'%s订单付款,付款单号%s'
);
/* 模块的基本信息 */
if (isset($read_modules) && $read_modules == true)
{
    $module['class_name']    = 'Account_Points';

    /* 名称 */
    $module['name']    = $payment_lang['name'];


    /* 支付方式：1：在线支付；0：线下支付  2:仅wap支付 3:仅app支付 4:兼容wap和app 6:wap,app,pc */
    $module['online_pay'] = '6';

    /* 配送 */
    $module['config'] = $config;
    
    $module['lang'] = $payment_lang;
    $module['reg_url'] = '';
    return $module;
}

// 通宝支付模型
require_once(APP_ROOT_PATH.'system/libs/payment.php');
class Account_Points_payment implements payment {
	public function get_payment_code($payment_notice_id)
	{		
		$rs = payment_paid($payment_notice_id);
		if($rs)
		{
			$payment_notice = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment_notice where id = ".$payment_notice_id);
			$order_sn = $GLOBALS['db']->getOne("select order_sn from ".DB_PREFIX."deal_order where id = ".$payment_notice['order_id']);
			require_once APP_ROOT_PATH."system/model/user.php";
			$msg = sprintf('%s订单付款,付款单号%s',$order_sn,$payment_notice['notice_sn']);			
			modify_account(array('money'=>"-".$payment_notice['money'],'score'=>0),$payment_notice['user_id'],$msg);
		}
	}
	
	/**
	 * 直接处理付款单
	 * @param unknown_type $payment_notice
	 */
	public function response($payment_notice)
	{
		return false;	
	}
	
	public function notify($request)
	{
		return false;
	}
	
	public function get_display_code()
	{
		/*$user_curpoints =es_session::get("user_curpoints");

		//zhu edit 20170717获取账户通宝的方法
		$user_id = intval($GLOBALS['user_info']['id']);
		$user_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where id = ".$user_id." and is_effect = 1 and is_delete = 0");
		if($user_info&&$user_curpoints>0)
		{						
			$html = "<p>帐户通宝：<b>".$user_curpoints."</b>，使用通宝支付".					
					" <input type='text' style='width: 50px; margin-bottom:-5px;' value='' name='account_points' class='ui-textbox' id='account_points'>，".
					"<label class='ui-checkbox' rel='common_cbo'><input type='checkbox' checked='checked' id='check-all-money' name='all_account_points'>通宝支付</label></p>";
			return $html;
		}
		else
		{
			return '';
		}*/

                          //通宝的验证
 		$ResultInfo=get_remotedata('ida/account/getuserpoints',null);

		if($ResultInfo["status"])
		{
                             $user_curpointsStatus=0;
		    $user_curpoints=$ResultInfo['data'];
		}
		else{
		    $user_curpointsStatus=-1;
		    $user_curpoints="通宝获取失败,请稍后再试!" ;
		}

		/*return "通宝支付";*/
		$payment_item = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment where class_name='Account_Points'");
		if($payment_item)
		{
			$html = "<label class='ui-radiobox payment_rdo payment_rdo_hover' rel='payment_rdo' checked='true'><input type='radio' name='payment' value='".$payment_item['id']."'    checked='true'/>";

			if($payment_item['logo']!='')
			{
				$html .= "<img src='".APP_ROOT.$payment_item['logo']."' />";
			}
			else
			{
				$html .= $payment_item['name'];
			}
			$html.="</label>";
			$html.="<div class='clear'></div>";
			$html.="<div class='account_payment'>";
			$html.="<input  type='hidden' id='hdcurpointsStatus' value='".$user_curpointsStatus."'>";
			$html.="<input  type='hidden' id='hdcurpoints' value='".$user_curpoints."'>";
			$html.= "<p>帐户通宝：<b>".$user_curpoints."</b>，使用通宝支付。".					
					" <input type='password' style='width: 100px; margin-bottom:-5px;' placeholder='请输入支付密码' value='' name='paypassword' class='ui-textbox' id='paypassword'></p>";
			$html.="</div> ";
			return $html;
		}
		else
		{
			return '';
		}
	}
}
?>