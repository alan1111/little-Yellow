<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2016 http://www.fanwebbs.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 微柚（5773389@qq.com）
// +----------------------------------------------------------------------

class paymentModule extends MainBaseModule
{
	//订单支付页
	public function pay()
	{
		global_run();
		init_app_page();

		$id = intval($_REQUEST['id']);
		$payment_notice = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment_notice where id = ".$id);

		if($payment_notice)
		{
			if($payment_notice['is_paid'] == 0)
			{
				$payment_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment where id = ".$payment_notice['payment_id']);
				if(empty($payment_info))
				{
					app_redirect(url("index"));
				}
				$order = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_order where id = ".$payment_notice['order_id']." and is_delete = 0");
				if(empty($order))
				{
					app_redirect(url("index"));
				}
				if($order['pay_status']==2)
				{
					app_redirect(url("index","payment#done",array("id"=>$order['id'])));
				}
				require_once APP_ROOT_PATH."system/payment/".$payment_info['class_name']."_payment.php";
				$payment_class = $payment_info['class_name']."_payment";
				$payment_object = new $payment_class();
				$payment_code = $payment_object->get_payment_code($payment_notice['id']);
				$GLOBALS['tmpl']->assign("page_title",$GLOBALS['lang']['PAY_NOW']);
				$GLOBALS['tmpl']->assign("payment_code",$payment_code);
	
				$GLOBALS['tmpl']->assign("order",$order);
				$GLOBALS['tmpl']->assign("payment_notice",$payment_notice);
				if(intval($_REQUEST['check'])==1)
				{
					showErr($GLOBALS['lang']['PAYMENT_NOT_PAID_RENOTICE'],0,url("index","payment#pay",array("id"=>$id)));
				}
				$GLOBALS['tmpl']->display("payment_pay.html");
			}
			else
			{
				$order = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_order where id = ".$payment_notice['order_id']);
			
				if($order['pay_status']==2)
				{
					app_redirect(url("index","payment#done",array("id"=>$order['id'])));
				}
				else
					showSuccess($GLOBALS['lang']['NOTICE_PAY_SUCCESS'],0,url("index"),1);
			}
		}
		else
		{
			showErr($GLOBALS['lang']['NOTICE_SN_NOT_EXIST'],0,url("index"),1);
		}
	}
	
	
	public function tip()
	{
		$payment_notice = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment_notice where id = ".intval($_REQUEST['id']));
		$GLOBALS['tmpl']->assign("payment_notice",$payment_notice);
		$GLOBALS['tmpl']->display("payment_tip.html");
	}
	
	
	public function response()
	{
		//支付跳转返回页
		if($GLOBALS['pay_req']['class_name'])
			$_REQUEST['class_name'] = $GLOBALS['pay_req']['class_name'];
			
		$class_name = strim($_REQUEST['class_name']);
		$payment_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment where class_name = '".$class_name."'");
		if($payment_info)
		{
			require_once APP_ROOT_PATH."system/payment/".$payment_info['class_name']."_payment.php";
			$payment_class = $payment_info['class_name']."_payment";
			$payment_object = new $payment_class();
			adddeepslashes($_REQUEST);
			$payment_code = $payment_object->response($_REQUEST);
		}
		else
		{
			showErr($GLOBALS['lang']['PAYMENT_NOT_EXIST']);
		}
	}
	
	public function notify()
	{
		//支付跳转返回页
		if($GLOBALS['pay_req']['class_name'])
			$_REQUEST['class_name'] = $GLOBALS['pay_req']['class_name'];
			
		$class_name = strim($_REQUEST['class_name']);
		$payment_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment where class_name = '".$class_name."'");
		if($payment_info)
		{
			require_once APP_ROOT_PATH."system/payment/".$payment_info['class_name']."_payment.php";
			$payment_class = $payment_info['class_name']."_payment";
			$payment_object = new $payment_class();
			adddeepslashes($_REQUEST);
			$payment_code = $payment_object->notify($_REQUEST);
		}
		else
		{
			showErr($GLOBALS['lang']['PAYMENT_NOT_EXIST']);
		}
	}
	
	
	
	public function done()
	{
		global_run();
	    init_app_page();
	    $order_id = intval($_REQUEST['id']);
	    
	    $order_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_order where id = ".$order_id);
	    if(empty($order_info))
	    {
	        showErr("订单不存在",0,url("index"));
	    }

	    //判断支付状态
	    if($order_info['pay_status']==2){
	        
	        if($order_info['type']==2 || $order_info['type']=='0'){ //商品订单和夺宝订单
	            $order_item = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."deal_order_item where order_id = ".$order_info['id']);
	            $duobao_item_ids = array();
	            foreach ($order_item as $k=>$v){
	                $duobao_item_ids[] = $v['duobao_item_id'];
	                $order_item_ids[] = $v['id'];
	            }
	            //查询夺宝号
	            $duobao_item_log_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."duobao_item_log where order_item_id in (".implode(",", $order_item_ids).") and duobao_item_id in (".implode(",", $duobao_item_ids).") and user_id = ".$order_info['user_id']);
	            foreach ($order_item as $k=>$v){
	                $temp_arr = array();
	                foreach ($duobao_item_log_list as $sub_k=>$sub_v){
	                    if($v['duobao_item_id']==$sub_v['duobao_item_id']){
	                        $temp_arr[] = $sub_v['lottery_sn'];
	                    }
	                }
	                $create_time = $v['create_time'];
	                $data_arr = explode(".", $create_time);
	                $date_str = to_date(intval($data_arr[0]),"H:i:s");
	                $full_date_str = to_date(intval($data_arr[0]));
	                $mmtime = trim($data_arr[1]);
	                
	                $res = intval(str_replace(":", "", $date_str).$mmtime);
	                $fair_sn_local=$res;
	                
	                $order_item[$k]['create_time_format'] = $full_date_str.".".$mmtime;
	                $order_item[$k]['lottery_sn_list'] = $temp_arr;
	                $total_number+=intval($v['number']);
	            }
	            $GLOBALS['tmpl']->assign("total_number",$total_number);
	            $GLOBALS['tmpl']->assign("order_item",$order_item);
	        
	       }elseif($order_info['type']==1){//充值订单
	           $GLOBALS['tmpl']->assign("info",round($order_info['pay_amount'],2)." 元 充值成功");
	       }
	    }

	    $GLOBALS['tmpl']->assign("order_info",$order_info);
		$GLOBALS['tmpl']->assign("page_title",$GLOBALS['lang']['PAY_SUCCESS']);
		$GLOBALS['tmpl']->display("payment_done.html");
	}
	
	public function incharge_done()
	{
		global_run();
		init_app_page();
		$GLOBALS['tmpl']->assign("no_nav",true); //无分类下拉
		$order_id = intval($_REQUEST['id']);
		$order_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_order where id = ".$order_id);
		//$order_deals = $GLOBALS['db']->getAll("select d.* from ".DB_PREFIX."deal as d where id in (select distinct deal_id from ".DB_PREFIX."deal_order_item where order_id = ".$order_id.")");
		$GLOBALS['tmpl']->assign("order_info",$order_info);
		//$GLOBALS['tmpl']->assign("order_deals",$order_deals);
		
		if($order_info['user_id']==$GLOBALS['user_info']['id'])
		{
			showSuccess(round($order_info['pay_amount'],2)." 元 充值成功",0,url("index","uc_money"));
		}
		else
		{
			showSuccess(round($order_info['pay_amount'],2)." 元 充值成功",0);
		}
	
		$GLOBALS['tmpl']->assign("page_title",$GLOBALS['lang']['PAY_SUCCESS']);
		$GLOBALS['tmpl']->display("payment_done.html");
	}
}
?>