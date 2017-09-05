<?php
// +----------------------------------------------------------------------
// | Fanwe 谷创商城系统
// +----------------------------------------------------------------------
// | Copyright (c) 2016 http://www.nthxmai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 甘味人生(526130@qq.com)
// +----------------------------------------------------------------------

$payment_lang = array(
	'name'	=>	'京东支付(WAP版本)',
	'merchantNum'	=>	'商户号',
	'desKey'	=>	'商户DES密钥',
	'md5Key'	=>	'商户MD5密钥',
	'VALID_ERROR'	=>	'支付验证失败',
	'PAY_FAILED'	=>	'支付失败',
	'GO_TO_PAY'	=>	'前往京东在线支付',
);
$config = array(
	'merchantNum'	=>	array(
		'INPUT_TYPE'	=>	'0',
	), //商户编号
	'desKey'	=>	array(
		'INPUT_TYPE'	=>	'0'
	), //商户DES密钥
	'md5Key'	=>	array(
		'INPUT_TYPE'	=>	'0'
	) //商户MD5密钥

);
/* 模块的基本信息 */
if (isset($read_modules) && $read_modules == true)
{
    if(ACTION_NAME=='install')
	{
		//更新字段
		$GLOBALS['db']->query("ALTER TABLE `".DB_PREFIX."user`  ADD COLUMN `wjdpay_token`  varchar(255) NOT NULL",'SILENT');
	}
    $module['class_name']    = 'Wjdpay';

    /* 名称 */
    $module['name']    = $payment_lang['name'];

   /* 支付方式：1：在线支付；0：线下支付;2:手机wap;3:手机sdk */
    $module['online_pay'] = '2';
    
    /* 配送 */
    $module['config'] = $config;
    
    $module['lang'] = $payment_lang;
    $module['reg_url'] = '';
    return $module;
}

require_once(APP_ROOT_PATH.'system/payment/wjdpay/config/config.php');//京东支付配置文件

// 京东支付模型
require_once(APP_ROOT_PATH.'system/libs/payment.php');

class Wjdpay_payment implements payment {

	public function get_payment_code($payment_notice_id)
	{

		$payment_notice = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment_notice where id = ".$payment_notice_id);
		$order_sn = $GLOBALS['db']->getOne("select order_sn from ".DB_PREFIX."deal_order where id = ".$payment_notice['order_id']);
		$money = round($payment_notice['money'],2);
		$payment_info = $GLOBALS['db']->getRow("select id,config,logo,name,class_name from ".DB_PREFIX."payment where id=".intval($payment_notice['payment_id']));
		$payment_info['config'] = unserialize($payment_info['config']);
		
		
		
		$sql = "select name ".
				"from ".DB_PREFIX."deal_order_item ".
				"where order_id =". intval($payment_notice['order_id']). " limit 1";
		$title_name = $GLOBALS['db']->getOne($sql);
		if(empty($title_name))
		{
			$title_name = "充值".round($payment_notice['money'],2)."元";
		}
		
		$pay = array();
		$pay['pay_info'] = $title_name;
		$pay['pay_action'] = SITE_DOMAIN.APP_ROOT."/cgi/payment/wjdpay/redirect.php?notice_id=".$payment_notice_id."&from=".$GLOBALS['request']['from'];
		$pay['payment_name'] = "京东支付";
		$pay['pay_money'] = $money;
		$pay['class_name'] = "Wjdpay";
		return $pay;
	}
	
	
	public function get_redirect_url($payment_notice_id)
	{
		require_once(APP_ROOT_PATH.'system/payment/wjdpay/common/SignUtil.php');
		require_once(APP_ROOT_PATH.'system/payment/wjdpay/common/DesUtils.php');
		require_once(APP_ROOT_PATH.'system/payment/wjdpay/common/ConfigUtil.php');
		
		$payment_notice = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment_notice where id = ".$payment_notice_id);
		$order_sn = $GLOBALS['db']->getOne("select order_sn from ".DB_PREFIX."deal_order where id = ".$payment_notice['order_id']);
		$money = intval(round($payment_notice['money'],2)*100);
		$payment_info = $GLOBALS['db']->getRow("select id,config,logo from ".DB_PREFIX."payment where id=".intval($payment_notice['payment_id']));
		$payment_info['config'] = unserialize($payment_info['config']);
		$user=$GLOBALS['db']->getRow("select wjdpay_token from ".DB_PREFIX."user where id=".intval($payment_notice['user_id']));
		
		$order_info = $GLOBALS['db']->getRow("select order_sn,bank_id from ".DB_PREFIX."deal_order where id = ".$payment_notice['order_id']);
		
		$sql = "select name ".
				"from ".DB_PREFIX."deal_order_item ".
				"where order_id =". intval($payment_notice['order_id']);
		$title_name = $GLOBALS['db']->getOne($sql);
		
		$param = array();

		
		$param["merchantRemark"] =app_conf("SHOP_TITLE");//商户备注 string(64)
		$param["tradeNum"] = $payment_notice['notice_sn'];//交易流水号 String（30）
		$param["tradeName"] = $title_name;//交易名称String（256）
		$param["tradeDescription"] = '京东支付';//交易描述String(1024)
		$param["tradeTime"] = to_date($payment_notice['create_time']);//交易时间 年-月-日 时:分:秒
		$param["tradeAmount"] = $money;//交易金额  int
		$param["currency"] = 'CNY';//货币种类
		$param["notifyUrl"] = get_domain().APP_ROOT.'/callback/payment/wjdpay_web/WebAsynNotificationCtrl.php';//异步通知页面地址String(200)
		$param["successCallbackUrl"] = get_domain().APP_ROOT.'/callback/payment/wjdpay_web/wjdpay_response.php';
		$param["failCallbackUrl"] = get_domain().APP_ROOT.'/callback/payment/wjdpay_web/wjdpay_response.php';//支付成功跳转路径String(200)
		
		$param["token"] = $user['wjdpay_token'];//用户交易令牌 识别用户信息,支付成功后会调用successCallbackUrl返回给商户
		//注:商户可以记录这个token值，当用户再次支付的时候传入该token，用户无需再次输入银行卡信息 ，直接输入短息验证码进行支付

		$param["version"] = '2.0';//版本
		$param["merchantNum"] = $payment_info['config']["merchantNum"];//商户号
		
		/*
		$param["merchantRemark"] ="生产环境-测试商户号";
		$param["tradeNum"] = "222945311461645604529";
		$param["tradeName"] = "交易名称";
		$param["tradeDescription"] = "交易描述";
		$param["tradeTime"] = "2016-04-26 04:40:04";
		$param["tradeAmount"] = 1;
		$param["currency"] = 'CNY';
		$param["notifyUrl"] = "http://localhost/mclient-php/wangyin/wepay/join/demo/api/WebAsynNotificationCtrl.php";
		$param["successCallbackUrl"] = "http://www.jd.com";
		$param["failCallbackUrl"] = "http://www.baidu.com";
		
		$param["token"] = "";
		$param["version"] = '1.0';
		$param["merchantNum"] = "22294531";
		*/
		$sign = SignUtil::sign($param);
		$param["merchantSign"] = $sign;
		$param['serverPayUrl'] = ConfigUtil::get_val_by_key("serverPayUrl");
		
		if($param["version"]=="2.0"){
					
			$des_arr=array("merchantRemark","tradeNum","tradeName","tradeDescription","tradeTime","tradeAmount","currency","notifyUrl","successCallbackUrl","failCallbackUrl");			
			$desUtils=new DesUtils();
			//$key=$payment_info['config']["desKey"];//商户号
			
			$key=ConfigUtil::get_val_by_key("desKey");
			foreach($param as $k=>$v){
				if(in_array($k,$des_arr)){
					$param[$k]=$desUtils->encrypt($v,$key);
				}	
			}
		}
		
		
		$payLinks ='<form method="post" action="'.$param['serverPayUrl'].'" id="payForm">';
		$payLinks .='<input type="hidden" name="version" value="'.$param['version'].'"/>';
		$payLinks .='<input type="hidden" name="token" value="'.$param['token'].'"/>';
		$payLinks .='<input type="hidden" name="merchantSign" value="'.$param['merchantSign'].'"/>';
		$payLinks .='<input type="hidden" name="merchantNum" value="'.$param['merchantNum'].'"/>';
		$payLinks .='<input type="hidden" name="merchantRemark" value="'.$param['merchantRemark'].'"/>';
		$payLinks .='<input type="hidden" name="tradeNum" value="'.$param['tradeNum'].'"/>';
		$payLinks .='<input type="hidden" name="tradeName" value="'.$param['tradeName'].'"/>';
		$payLinks .='<input type="hidden" name="tradeDescription" value="'.$param['tradeDescription'].'"/>';
		$payLinks .='<input type="hidden" name="tradeTime" value="'.$param['tradeTime'].'"/>';
		$payLinks .='<input type="hidden" name="tradeAmount" value="'.$param['tradeAmount'].'"/>';
		$payLinks .='<input type="hidden" name="currency" value="'.$param['currency'].'"/>';
		$payLinks .='<input type="hidden" name="notifyUrl" value="'.$param['notifyUrl'].'"/>';
		
		$payLinks .='<input type="hidden" name="successCallbackUrl" value="'.$param['successCallbackUrl'].'"/>';
		$payLinks .='<input type="hidden" name="failCallbackUrl" value="'.$param['failCallbackUrl'].'"/>';
		$payLinks .='</form>';
		$payLinks.='<script type="text/javascript">document.getElementById("payForm").submit();</script>';

		return $payLinks;
		
	
	
	}
	public function response($request)
	{
		
	}
	
	public function notify($request)
	{
		
	}
	
	public function get_display_code()
	{
		return "京东支付";
	}
	
	
}

?>