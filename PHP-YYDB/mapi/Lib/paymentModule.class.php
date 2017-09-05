<?php
// +----------------------------------------------------------------------
// | nthxmai.com 谷创商城夺宝
// +----------------------------------------------------------------------
// | Copyright (c) 2016 http://www.nthxmai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Ant（594177881@qq.com）
// +----------------------------------------------------------------------


class paymentApiModule extends MainBaseApiModule
{
	
	/**
	 * 订单支付页，包含检测状态，获取支付代码与消费券
	 * 
	 * 输入:
	 * id: int 订单ID
	 * 
	 * 输出:
	 * status:int 状态 0:失败 1:成功
	 * info: string 失败的原因
	 * 以下参数为成功时返回
	 * pay_status: int 支付状态 0:未支付 1:已支付 
	 * order_id: int 订单ID
	 * order_sn: string 订单号
	 * 
	 * pay_info: string 显示的信息
	 * 
	 * 当pay_status 为1时
	 * couponlist: array 消费券列表
	 * Array
	 * (
	 * 		Array(
	 * 			"password" => string 验证码
	 * 			"qrcode"  => string 二维码地址
	 * 		)
	 * )
	 * 
	 * 当pay_status 为0时
	 * payment_code: Array() 相关支付接口返回的支付数据
	 */
	public function done()
	{
		global_run();
		$root = array();
		$order_id = intval($GLOBALS['request']['id']);
		
		$order_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_order where id = ".$order_id);
		if(empty($order_info))
		{
			return output(array(),0,"订单不存在");
		}
		
		$root['order_sn'] = $order_info['order_sn'];
		$root['order_id'] = $order_id;
		$root['is_app']   = $GLOBALS['is_app'] ? 1:0;
		if($order_info['pay_status']==2)
		{
			if($order_info['type']==0||$order_info['type']==2)
			{
				$refund_item = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."deal_order_item where order_id = ".$order_info['id']." and refund_status = 2");
				if($refund_item)
				{
					$root['pay_status'] = 1;
					if(count($refund_item)>1)
						$root['pay_info'] = $refund_item[0]['name'].'等已失效，已退款';
					else
						$root['pay_info'] = $refund_item[0]['name'].'已失效，已退款';
				}
				else
				{
					$root['pay_status'] = 1;					
					$root['pay_info'] = '订单已经收款';
				}
				return output($root);
			}
			else
			{
				$root['pay_status'] = 1;
				$root['pay_type'] = $order_info['type'];//判断会员充值通宝
				$root['pay_info'] = round($order_info['pay_amount'],2)." 元 充值成功";
				return output($root);
			}
		}
		else
		{
			require_once APP_ROOT_PATH."system/model/cart.php";
			
			
			$payment = $order_info['payment_id'];
			if($order_info['type']==1)
			{
				$pay_price = $order_info['total_price'];
			}
			else {
				$goods_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."deal_order_item where order_id = ".$order_info['id']);
				
				$data = count_buy_total($payment,0,0,0,1,'','','',$goods_list,$order_info['account_money'],$order_info['ecv_money'],0,'');
				
				$pay_price = $data['pay_price'];
			}
			$payment_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment where id = ".$order_info['payment_id']);
			if(empty($payment_info))
			{
				return output(array(),0,"支付方式不存在");
			}
			if($pay_price<=0&&($payment_info['class_name']!='Account'&&$payment_info['class_name']!='Voucher'&&$payment_info['class_name']!='Account_Points'))
			{
				return output(array(),0,"无效的支付方式");
			}
			
			global $is_app;
			/*if(!$is_app)*/
			if(!$is_app&&payment_info['online_pay'])
			{
				if ( $payment_info['online_pay'] !=2 && $payment_info['online_pay'] !=4 && $payment_info['online_pay'] !=5 && $payment_info['online_pay'] !=6 && $payment_info['online_pay'] != 7 )
				{
					return output(array(),0,"该支付方式不支持wap支付");
				}
			}
			else
			{
				if ($payment_info['online_pay']!=3&&$payment_info['online_pay']!=4&&$payment_info['online_pay']!=5&&$payment_info['online_pay']!=6)
				{
					return output(array(),0,"该支付方式不支持手机支付");
				}
			}
			
			$payment_notice_id = make_payment_notice($pay_price,$order_id,$order_info['payment_id']);
			require_once APP_ROOT_PATH."system/payment/".$payment_info['class_name']."_payment.php";
			$payment_class = $payment_info['class_name']."_payment";

			  $payment_object = new $payment_class();
			  $payment_code = $payment_object->get_payment_code($payment_notice_id);
			  $root['payment_code'] = $payment_code;
			
			$root['pay_status']   = 0;
			$root['pay_info']     = '支付失败';
			return output($root);
		}		
	}
	
	public function order_share(){
	    global_run();
	    $root = array();
	    $order_id = intval($GLOBALS['request']['id']);

	    //检查用户,用户密码
	    $user = $GLOBALS['user_info'];
	    $user_id  = intval($user['id']);
	    
	    $user_login_status = check_login();
	    if($user_login_status!=LOGIN_STATUS_LOGINED){
	        $root['user_login_status'] = $user_login_status;
	    }
	    else
	    {
	        $root['user_login_status'] = $user_login_status;
	        require_once APP_ROOT_PATH.'system/model/topic.php';
	        order_share($order_id);
	    }
	    return output($root);
	    
	}
	
	
}
?>

