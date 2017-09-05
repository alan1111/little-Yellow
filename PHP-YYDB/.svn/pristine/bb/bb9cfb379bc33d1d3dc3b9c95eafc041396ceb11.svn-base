<?php
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2016 http://www.fanwebbs.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 微柚（5773389@qq.com）
// +----------------------------------------------------------------------
/*ini_set("display_errors", true);
    error_reporting(E_ALL);*/
class cartModule extends MainBaseModule
{    
    
	public function index()
	{
		global_run();

		init_app_page();
		require_once APP_ROOT_PATH."system/model/duobao.php";
		$GLOBALS['tmpl']->assign("drop_nav","no_drop"); //首页下拉菜单不输出

		require_once APP_ROOT_PATH."system/model/cart.php";
		$cart_result = load_cart_list(true,1);

		//检测购物车中的商品是否过期
		$duobao_ids = array_keys($cart_result['cart_list']);
		$id_str=implode(",", $duobao_ids);
		if($id_str!=''){
			$duobao_items = $GLOBALS['db']->getAll("select dc.id,di.name,di.progress,di.current_buy,di.max_buy from ".DB_PREFIX."deal_cart as dc left join ".DB_PREFIX."duobao_item as di on di.id = dc.duobao_item_id where dc.id in(".$id_str.")");
			foreach($duobao_items as $k=>$v){
				if($v['progress']==100 && ($v['max_buy']==$v['current_buy'])){
					$expire_ids[] = $v['id'];
				}
			}
			$GLOBALS['db']->query("delete from ".DB_PREFIX."deal_cart where id in(".  implode(",", $expire_ids).") and session_id = '".es_session::id()."' and user_id = ".intval($GLOBALS['user_info']['id']));
		}

		//把购物车中用户当时设为无效的购物记录设为有效
		$GLOBALS['db']->query("update ".DB_PREFIX."deal_cart set is_effect=1 where session_id = '" . es_session::id () . "' and user_id = " . $user_info['id'] );


		$is_whole=1;
		$cart_result = load_cart_list(true,1);


		$cart_data=array();

		foreach($cart_result['cart_list'] as $k=>$v){
			$cart_data[$k]['id']=$v['id'];
			$cart_data[$k]['unit_price']=$v['unit_price'];
			$cart_data[$k]['total_price']=$v['total_price'];
			$cart_data[$k]['number']=$v['number'];
			$cart_data[$k]['min_buy']=$v['min_buy'];
			$cart_data[$k]['type']=1;
			$cart_data[$k]['residue_count']=$v['residue_count'];
			$cart_data[$k]['user_max_buy']=$v['user_max_buy'];

			$order_number = $GLOBALS['db']->getOne("select sum(number) from ".DB_PREFIX."deal_order_item where duobao_item_id = ".$v['duobao_item_id']." and user_id = ".intval($GLOBALS['user_info']['id'])." and pay_status = 2 and refund_status = 0 ");
			$cart_data[$k]['order_number']=intval($order_number);
		}


		$cart_result['total_data']['total_price'] = round($cart_result['total_data']['total_price'],2);

		if(check_save_login()!=LOGIN_STATUS_LOGINED)
		{
			app_redirect(url("index","user#login"));
		}

		$page_title= "购物车";

		$GLOBALS['tmpl']->assign("jsondata",json_encode($cart_data));
		$GLOBALS['tmpl']->assign("page_title",$page_title);
		$GLOBALS['tmpl']->assign("cart_result",$cart_result);
		$user_money=$GLOBALS['user_info']['money'];
		$GLOBALS['tmpl']->assign("user_money",$user_money);

		$is_cart_agreement=$GLOBALS['user_info']['is_cart_agreement'];
		if($is_cart_agreement==0){
			//服务协议
			$agreement = $GLOBALS['db']->getRow("SELECT *
                    FROM
                        ".DB_PREFIX."agreement
                    WHERE
                        is_effect = 1 AND
                        agreement_cate ='agreement'
                        ORDER BY sort DESC ");
			$GLOBALS['tmpl']->assign("agreement",$agreement);
		}


		$sql_recomend = "SELECT DuobaoItem.id,DuobaoItem.name AS duobaoitem_name, DuobaoItem.icon,DuobaoItem.max_buy
                FROM
                	".DB_PREFIX."duobao_item as DuobaoItem
                    LEFT JOIN ".DB_PREFIX."duobao as DUOBAO ON DuobaoItem.duobao_id = DUOBAO.id
		        WHERE
		            DuobaoItem.is_effect = 1 AND
		            DuobaoItem.progress < 100 AND
                    DUOBAO.is_recomend=1";   //未开奖的
		$recomend_list=$GLOBALS['db']->getAll($sql_recomend." order by rand() limit 5");
		$GLOBALS['tmpl']->assign("is_whole",$is_whole);


		$GLOBALS['tmpl']->assign("is_cart_agreement",$is_cart_agreement);
		$GLOBALS['tmpl']->assign("recomend_list",$recomend_list);  //新品推荐列表

		$GLOBALS['tmpl']->display("cart.html");
	}



	/**
	 * 购物车的提交页
	 */
	public function check()
	{
		//assign_form_verify();
	    global_run();
	    init_app_page();
	    if((check_save_login()!=LOGIN_STATUS_LOGINED&&$GLOBALS['user_info']['money']>0)||check_save_login()==LOGIN_STATUS_NOLOGIN)
	    {
	        app_redirect(url("index","user#login"));
	    }
		//通宝的验证
 		$ResultInfo=get_remotedata('ida/account/getuserpoints',null);

		if($ResultInfo["status"])
		{
                                 	$points_amount=$ResultInfo['data'];
                                	$GLOBALS['tmpl']->assign('points_amount',$points_amount);
		}
		else{
			showErr("通宝获取失败,请稍后再试!");
		     /* return output ( $root, 0, "通宝获取失败,请稍后再试!" );*/
		}	
	    $GLOBALS['db']->query("update ".DB_PREFIX."deal_cart set is_effect=0 where session_id = '" . es_session::id () . "' and user_id = " . $GLOBALS['user_info']['id'] );
		foreach($_REQUEST['selected'] as $k=>$v){
			$min_buy=$GLOBALS ['db']->getOne ( "select b.min_buy from " . DB_PREFIX . "deal_cart as a left join " . DB_PREFIX . "duobao_item as b on a.duobao_item_id = b.id where a.id = " . $v );
	    	$GLOBALS['db']->query("update ".DB_PREFIX."deal_cart set is_effect=1 , number=".$_REQUEST['num'][$v]." , total_price=unit_price * ".$_REQUEST['num'][$v]." , return_total_score=return_score * ".$_REQUEST['num'][$v]/$min_buy." where session_id = '" . es_session::id () . "' and user_id = " . $GLOBALS['user_info']['id']." and id=".$v );

	    }

	    //获取购物车内容
	    require_once APP_ROOT_PATH."system/model/cart.php";
	    $cart_result = load_cart_list();
	    $cart_list = $cart_result['cart_list'];

	    //购物车总金额
	    $total_price = $cart_result['total_data']['total_price'];

	    //判断购物车商品状态
	    if(!$cart_list)
	    {
	        app_redirect(url("index"));
	    }

	    foreach($cart_list as $k=>$v)
	    {
	        $id = intval($v['id']);
	        $number = intval($v['number']);
	        //调用验证函数
	        $data = check_cart($id, $number);
	        if(!$data['status'])
	        {
	            showErr($data['info']);
	        }
	        $cart_list[$k]['origin_price'] = round($v['origin_price'],2);
	        $cart_list[$k]['unit_price'] = round($v['unit_price'],2);
	        $cart_list[$k]['total_price'] = round($v['total_price'],2);

	    }


	    //输出支付方式
	    $payment_list = load_auto_cache("cache_payment");

	    $icon_paylist = array(); //用图标展示的支付方式
		$disp_paylist = array(); //特殊的支付方式(Voucher,Account,Otherpay)
		$bank_paylist = array(); //网银直连

		$wx_payment = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment where class_name = 'Wwxjspay'");
		if($wx_payment)
		{
			$wx_payment['config'] = unserialize($wx_payment['config']);
			if($wx_payment['config']['scan']==1)
			{
				$directory = APP_ROOT_PATH."system/payment/";
				$file = $directory. '/' .$wx_payment['class_name']."_payment.php";
				if(file_exists($file))
				{
					require_once($file);
					$payment_class = $wx_payment['class_name']."_payment";
					$payment_object = new $payment_class();
					$wx_payment['display_code'] = $payment_object->get_web_display_code();
					$disp_paylist[] = $wx_payment;
				}
			}
		}

		foreach($payment_list as $k=>$v)
		{
			if($v['class_name']=="Voucher"||$v['class_name']=="Account"||$v['class_name']=="Otherpay")
			{
				if($v['class_name']=="Account")
				{
					$directory = APP_ROOT_PATH."system/payment/";
					$file = $directory. '/' .$v['class_name']."_payment.php";
					if(file_exists($file))
					{
						require_once($file);
						$payment_class = $v['class_name']."_payment";
						$payment_object = new $payment_class();
						$v['display_code'] = $payment_object->get_display_code();
					}
				}
				if($v['class_name']=="Voucher")
				{
					$directory = APP_ROOT_PATH."system/payment/";
					$file = $directory. '/' .$v['class_name']."_payment.php";
					if(file_exists($file))
					{
						require_once($file);
						$payment_class = $v['class_name']."_payment";
						$payment_object = new $payment_class();
						$v['display_code'] = $payment_object->get_display_code();
					}

				}

				$disp_paylist[] = $v;
			}
			else
			{
				if($v['is_bank']==1)
				$bank_paylist[] = $v;
				else
				$icon_paylist[] = $v;
			}
		}



		//支付方式显示
		$GLOBALS['tmpl']->assign("icon_paylist",$icon_paylist);
		$GLOBALS['tmpl']->assign("disp_paylist",$disp_paylist);
		$GLOBALS['tmpl']->assign("bank_paylist",$bank_paylist);

		//用户信息显示
		$GLOBALS['tmpl']->assign("user_info",$GLOBALS['user_info']);

		//输出购物车内容
		$GLOBALS['tmpl']->assign("cart_result",$cart_result);
		$GLOBALS['tmpl']->assign("cart_list",$cart_list);
		$GLOBALS['tmpl']->assign('total_price',$total_price);

		//支付方式显示
		if($total_price > 0)
		    $GLOBALS['tmpl']->assign("show_payment",true);

		//关于短信发送的条件
		$GLOBALS['tmpl']->assign("sms_lesstime",load_sms_lesstime());
		$GLOBALS['tmpl']->assign("sms_ipcount",load_sms_ipcount());

		//购物车检测页
		$GLOBALS['tmpl']->display("cart_check.html");

	}

	public function order()
	{
		global_run();
		init_app_page();
		if(check_save_login()!=LOGIN_STATUS_LOGINED)
		{
			app_redirect(url("index","user#login"));
		}


		$id = intval($_REQUEST['id']);
		$order_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_order where id = ".$id." and is_delete = 0 and pay_status <> 2 and order_status <> 1 and user_id =".intval($GLOBALS['user_info']['id']));

// 		echo "select * from ".DB_PREFIX."deal_order where id = ".$id." and is_delete = 0 and pay_status <> 2 and order_status <> 1 and user_id =".intval($GLOBALS['user_info']['id']);exit;

		if(!$order_info)
		{
			app_redirect(url("index"));
		}
		if($order_info['type']==1)
		{
			app_redirect(url("index","uc_money#incharge"));
		}
		$GLOBALS['tmpl']->assign('order_info',$order_info);

	    //获取购物车内容
	    $cart_list = $GLOBALS['db']->getAll("select doi.*,di.origin_price from ".DB_PREFIX."deal_order_item as doi left join ".DB_PREFIX."duobao_item as di on di.id = doi.duobao_item_id  where doi.order_id = ".$order_info['id']);

		if(!$cart_list)
		{
			app_redirect(url("index"));
		}
		else
		{
			foreach($cart_list as $k=>$v)
			{
				$cart_list[$k]['unit_price'] = round($v['unit_price']);
				$cart_list[$k]['total_price'] = round($v['total_price']);
				$cart_list[$k]['origin_price'] = round($v['origin_price']);
				$total_price +=$v['total_price'];
			}
		}

		//购物车总金额
		$total_price = round($total_price,2);

	    //输出支付方式
	    $payment_list = load_auto_cache("cache_payment");

	    $icon_paylist = array(); //用图标展示的支付方式
		$disp_paylist = array(); //特殊的支付方式(Voucher,Account,Otherpay)
		$bank_paylist = array(); //网银直连

		$wx_payment = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."payment where class_name = 'Wwxjspay'");
		if($wx_payment)
		{
			$wx_payment['config'] = unserialize($wx_payment['config']);
			if($wx_payment['config']['scan']==1)
			{
				$directory = APP_ROOT_PATH."system/payment/";
				$file = $directory. '/' .$wx_payment['class_name']."_payment.php";
				if(file_exists($file))
				{
					require_once($file);
					$payment_class = $wx_payment['class_name']."_payment";
					$payment_object = new $payment_class();
					$wx_payment['display_code'] = $payment_object->get_web_display_code();
					$disp_paylist[] = $wx_payment;
				}
			}
		}

		foreach($payment_list as $k=>$v)
		{
			if($v['class_name']=="Voucher"||$v['class_name']=="Account"||$v['class_name']=="Otherpay")
			{
				if($v['class_name']=="Account")
				{
					$directory = APP_ROOT_PATH."system/payment/";
					$file = $directory. '/' .$v['class_name']."_payment.php";
					if(file_exists($file))
					{
						require_once($file);
						$payment_class = $v['class_name']."_payment";
						$payment_object = new $payment_class();
						$v['display_code'] = $payment_object->get_display_code();
					}
				}
				if($v['class_name']=="Voucher")
				{
					$directory = APP_ROOT_PATH."system/payment/";
					$file = $directory. '/' .$v['class_name']."_payment.php";
					if(file_exists($file))
					{
						require_once($file);
						$payment_class = $v['class_name']."_payment";
						$payment_object = new $payment_class();
						$v['display_code'] = $payment_object->get_display_code();
					}

				}

				$disp_paylist[] = $v;
			}
			else
			{
				if($v['is_bank']==1)
				$bank_paylist[] = $v;
				else
				$icon_paylist[] = $v;
			}
		}



		//支付方式显示
		$GLOBALS['tmpl']->assign("icon_paylist",$icon_paylist);
		$GLOBALS['tmpl']->assign("disp_paylist",$disp_paylist);
		$GLOBALS['tmpl']->assign("bank_paylist",$bank_paylist);

		//用户信息显示
		$GLOBALS['tmpl']->assign("user_info",$GLOBALS['user_info']);

		//输出购物车内容
		$GLOBALS['tmpl']->assign("order_info",$order_info);
		$GLOBALS['tmpl']->assign("cart_list",$cart_list);
		$GLOBALS['tmpl']->assign('total_price',$total_price);

		//支付方式显示
		if($total_price > 0)
		    $GLOBALS['tmpl']->assign("show_payment",true);

		//关于短信发送的条件
		$GLOBALS['tmpl']->assign("sms_lesstime",load_sms_lesstime());
		$GLOBALS['tmpl']->assign("sms_ipcount",load_sms_ipcount());



		$GLOBALS['tmpl']->display("cart_check.html");
	}

	public function done(){
		//check_form_verify();
	    global_run();
	    require_once APP_ROOT_PATH . "system/model/cart.php";
	    require_once APP_ROOT_PATH . "system/model/deal_order.php";
	    $ajax = 1;
	    $payment = intval ( $_REQUEST['payment'] );
	    $account_money = floatval($_REQUEST['account_money']);
		$all_account_money = intval($_REQUEST['all_account_money']);
		$all_account_points = intval ( $GLOBALS ['request'] ['all_account_points'] );

	    $ecvsn = $_REQUEST['ecvsn'] ? strim ( $_REQUEST['ecvsn'] ) : '';
	    $ecvpassword = $_REQUEST['ecvpassword'] ? strim ( $_REQUEST['ecvpassword'] ) : '';
	    $memo = strim ( $_REQUEST['content'] );
	    $paypassword = $_REQUEST['paypassword'] ? strim ( $_REQUEST['paypassword'] ) : '';

	    $cart_result = load_cart_list();
	    $goods_list = $cart_result['cart_list'];

	    require_once APP_ROOT_PATH . "system/model/duobao.php";
	    foreach($goods_list as $item)
	    {
	    	$res = duobao::check_duobao_number($item['duobao_item_id'], 0);
	    	if($res['status']==0)
	    		showErr($res['info'],$ajax);
	    }

	    if (! $goods_list) {
	        showErr($GLOBALS['lang']['CART_EMPTY_TIP'],$ajax,url("index"));
	    }

	    // 验证购物车
	    if((check_save_login()!=LOGIN_STATUS_LOGINED&&$GLOBALS['user_info']['money']>0)||check_save_login()==LOGIN_STATUS_NOLOGIN)
		{
			showErr($GLOBALS['lang']['PLEASE_LOGIN_FIRST'],$ajax,url("index","user#login"));
		}

		//zhu edit 20170721 远程验证 begin

	             $ResultInfo=get_remotedata('ida/account/GetUserPoints',null);

		if(!$ResultInfo["status"])
		{
		       showErr($ResultInfo['data'],$ajax);
		}
		else
		{
	                   $user_curpoints =$ResultInfo['data'];
		}

                          if (strlen($paypassword)<=0) {
			showErr( "支付密码未输入, 请确认!",$ajax );
		}
		
	             $ResultInfo=get_remotedata('ida/account/CheckPayPwd','payp='.$paypassword);

		if(!$ResultInfo["status"])
		{
                               showErr($ResultInfo['data'],$ajax);
		}
                          //zhu edit 20170721 远程验证 end
                          
                     
	    // 开始验证订单接交信息
	    $data = count_buy_total ( $payment, 0, $all_account_money,0,$all_account_points,$paypassword, $ecvsn, $ecvpassword, $goods_list, 0,0, 0, '' );


	    if(round($data['pay_price'],4)>0&&!$data['payment_info'])
	    {
	        showErr($GLOBALS['lang']['PLEASE_SELECT_PAYMENT'],$ajax);
	    }
                   
                 if (round ( $data ['pay_price'], 4 ) > $user_curpoints) {
	         showErr("当前账户通宝数不足，请确认！" ,$ajax);
	    }

	    // 结束验证订单接交信息

	    $user_id = $GLOBALS ['user_info'] ['id'];

	    // 获取用户地址
// 	    $consignee_info = $GLOBALS ['db']->getRow ( "select * from " . DB_PREFIX . "user_consignee where is_default=1 and user_id=" . $user_id );
// 	    $region_conf = load_auto_cache ( "delivery_region" );

// 	    $region_lv1 = intval ( $consignee_info ['region_lv1'] );
// 	    $region_lv2 = intval ( $consignee_info ['region_lv2'] );
// 	    $region_lv3 = intval ( $consignee_info ['region_lv3'] );
// 	    $region_lv4 = intval ( $consignee_info ['region_lv4'] );
// 	    $region_info = $region_conf [$region_lv1] ['name'] . " " . $region_conf [$region_lv2] ['name'] . " " . $region_conf [$region_lv3] ['name'] . " " . $region_conf [$region_lv4] ['name'];

	    // 开始生成订单
	    $now = NOW_TIME;
	    $order ['type'] = 2; // 一元购订单
	    $order ['user_id'] = $user_id;
	    $order ['create_time'] = $now;
	    $order ['update_time'] = $now;
	    $order ['total_price'] = $data ['pay_total_price']; // 应付总额 商品价 - 会员折扣 + 运费
	    // + 支付手续费
	    $order ['pay_amount'] = $data ['pay_price']; // 现金应付的金额，（待定:结单后送的积分）
	    $order ['pay_status'] = 0; // 新单都为零， 等下面的流程同步订单状态
	    $order ['delivery_status'] = $data ['is_delivery'] == 0 ? 5 : 0;
	    $order ['order_status'] = 0; // 新单都为零， 等下面的流程同步订单状态
	    $order ['return_total_score'] = $data ['return_total_score']; // 结单后送的积分
	    $order ['memo'] = $memo;
	    // 地址待定
// 	    $order ['region_info'] = $region_info;
// 	    $order ['address'] = strim ( $consignee_info ['address'] );
// 	    $order ['mobile'] = strim ( $consignee_info ['mobile'] );
// 	    $order ['consignee'] = strim ( $consignee_info ['consignee'] );
// 	    $order ['zip'] = strim ( $consignee_info ['zip'] );

	    $order ['ecv_money'] = 0;
	    $order ['account_money'] = 0;
	    $order ['points_money'] = $data['points_money'];
	    $order ['ecv_sn'] = '';
	    // $order['delivery_id'] = $data['delivery_info']['id'];

	    $order ['payment_id'] = $data ['payment_info'] ['id'];
	    $order ['payment_fee'] = $data ['payment_fee'];
	    $order ['bank_id'] = "";

	    // 更新来路
	    $order ['referer'] = $GLOBALS ['referer'];
	    $user_info = es_session::get ( "user_info" );
	    $order ['user_name'] = $user_info ['user_name'];

	    $order ['duobao_ip'] = CLIENT_IP;
	    require_once APP_ROOT_PATH . "system/extend/ip.php";
	    $ip = new iplocate ();
	    $area = $ip->getaddress ( CLIENT_IP );
	    $order ['duobao_area'] = $area ['area1'];

	    $order['create_date_ymd'] = to_date(NOW_TIME,"Y-m-d");
	    $order['create_date_ym'] = to_date(NOW_TIME,"Y-m");
	    $order['create_date_y'] = to_date(NOW_TIME,"Y");
	    $order['create_date_m'] = to_date(NOW_TIME,"m");
	    $order['create_date_d'] = to_date(NOW_TIME,"d");

	    /** 更新会员手机号
	     $coupon_mobile = htmlspecialchars(addslashes(trim($_REQUEST['coupon_mobile'])));
	     if($coupon_mobile!='')
	         $GLOBALS['db']->query("update ".DB_PREFIX."user set mobile = '".$coupon_mobile."' where id = ".intval($user_info['id']));
	         */
	    /*
	     if($user_info['mobile']=="")
	     {
	         $user_mobile = strim($_REQUEST['user_mobile']);

	         if($user_mobile=="")
	         {
	             $data = array();
	             $data['status'] = false;
	             $data['info']	=  "请输入手机号";
	             $data['jump']  = "";
	             ajax_return($data);
	         }

	         if(!check_mobile($user_mobile))
	         {
	             $data = array();
	             $data['status'] = false;
	             $data['info']	=  "手机号格式不正确";
	             $data['jump']  = "";
	             ajax_return($data);
	         }

	         if(app_conf("SMS_ON")==1)
	         {
	             $mobile_data = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."sms_mobile_verify where mobile_phone = '".$user_mobile."'");
	             $sms_verify = strim($_POST['sms_verify']);
	             if(empty($mobile_data)||$mobile_data['code']!=$sms_verify)
	             {
	                 $data = array();
	                 $data['status'] = false;
	                 $data['info']	=  "手机验证码错误";
	                 $data['jump']  = "";
	                 ajax_return($data);
	             }
	         }

	         $GLOBALS['db']->query("update ".DB_PREFIX."user set mobile = '".$user_mobile."' where id = ".$user_info['id'],"SILENT");
	         if($GLOBALS['db']->affected_rows()>0)
	         {
	             $GLOBALS['db']->query("delete from ".DB_PREFIX."sms_mobile_verify where mobile_phone = '".$user_mobile."'"); //删除验证码
	         }
	         else
	         {
	             $data = array();
	             $data['status'] = false;
	             $data['info']	=  "手机号已被注册";
	             $data['jump']  = "";
	             ajax_return($data);
	         }
	     }

*/
	    do {
	        $order ['order_sn'] = to_date ( NOW_TIME, "Ymdhis" ) . rand ( 10, 99 );
	        $GLOBALS ['db']->autoExecute ( DB_PREFIX . "deal_order", $order, 'INSERT', '', 'SILENT' );
	        $order_id = intval ( $GLOBALS ['db']->insert_id () );
	    } while ( $order_id == 0 );

	    // 生成订单商品
	    foreach ( $goods_list as $k => $v ) {
	        $deal_info = load_auto_cache ( "deal", array (
	            "id" => $v ['deal_id']
	        ) );
	        $goods_item = array ();

	        $goods_item ['deal_id'] = $v ['deal_id'];
	        $goods_item ['duobao_id'] = $v ['duobao_id'];
	        $goods_item ['duobao_item_id'] = $v ['duobao_item_id'];
	        $goods_item ['number'] = $v ['number'];
	        $goods_item ['unit_price'] = $v ['unit_price'];
	        $goods_item ['total_price'] = $v ['total_price'];
	        $goods_item ['name'] = $v ['name'];
	        $goods_item ['delivery_status'] = 0;
	        $goods_item ['return_score'] = $v ['return_score'];
	        $goods_item ['return_total_score'] = $v ['return_total_score'];
	        $goods_item ['verify_code'] = $v ['verify_code'];
	        $goods_item ['order_sn'] = $order ['order_sn'];
	        $goods_item ['order_id'] = $order_id;
	        $goods_item ['is_arrival'] = 0;
	        $goods_item ['deal_icon'] = $v ['deal_icon'];
	        $goods_item ['user_id'] = $user_id;
	        $goods_item ['duobao_ip'] = $order ['duobao_ip'];
	        $goods_item ['duobao_area'] = $order ['duobao_area'];
	        $goods_item ['type'] = $order ['type'];
	        $goods_item['create_time'] = NOW_TIME;
	        $goods_item['create_date_ymd'] = to_date(NOW_TIME,"Y-m-d");
	        $goods_item['create_date_ym'] = to_date(NOW_TIME,"Y-m");
	        $goods_item['create_date_y'] = to_date(NOW_TIME,"Y");
	        $goods_item['create_date_m'] = to_date(NOW_TIME,"m");
	        $goods_item['create_date_d'] = to_date(NOW_TIME,"d");

	        $GLOBALS ['db']->autoExecute ( DB_PREFIX . "deal_order_item", $goods_item, 'INSERT', '', 'SILENT' );
	    }

	    // 开始更新订单表的deal_ids
	    $GLOBALS ['db']->query ( "delete from " . DB_PREFIX . "deal_cart where session_id = '" . es_session::id () . "'" );
	    load_cart_list ( true );


	    if($GLOBALS ['user_info']['is_cart_agreement']==0){
	    	$GLOBALS ['db']->query ( "update " . DB_PREFIX . "user set is_cart_agreement=1  where id=".$GLOBALS ['user_info']['id'] );
	    }

	    // 生成order_id 后
	     $ResultInfo=get_remotedata('ida/order/orderdone','payp='.$paypassword.'&oc='.$order ['order_sn'].'&num='.$order ['total_price']);

	     if($ResultInfo["STATUS"]=='NO')
	     {
                               showErr($ResultInfo['DATA'],$ajax);
	     }

	    // 1. 代金券支付
	    $ecv_data = $data ['ecv_data'];
	    if ($ecv_data) {
	        $ecv_payment_id = $GLOBALS ['db']->getOne ( "select id from " . DB_PREFIX . "payment where class_name = 'Voucher'" );
	        if ($ecv_data ['money'] > $order ['total_price'])
	            $ecv_data ['money'] = $order ['total_price'];
	        $payment_notice_id = make_payment_notice ( $ecv_data ['money'], $order_id, $ecv_payment_id, "", $ecv_data ['id'] );
	        require_once APP_ROOT_PATH . "system/payment/Voucher_payment.php";
	        $voucher_payment = new Voucher_payment ();
	        $voucher_payment->direct_pay ( $ecv_data ['sn'], $ecv_data ['password'], $payment_notice_id );
	    }

	    // 2. 余额支付
	    $account_money = $data ['account_money'];
	    if (floatval ( $account_money ) > 0) {
	        $account_payment_id = $GLOBALS ['db']->getOne ( "select id from " . DB_PREFIX . "payment where class_name = 'Account'" );
	        $payment_notice_id = make_payment_notice ( $account_money, $order_id, $account_payment_id );
	        require_once APP_ROOT_PATH . "system/payment/Account_payment.php";
	        $account_payment = new Account_payment ();
	        $account_payment->get_payment_code ( $payment_notice_id );
	    }
                  // 3. 通宝支付
		$points_money = $data ['points_money'];
		if (floatval ( $points_money ) > 0) {
			$account_payment_id = $GLOBALS ['db']->getOne ( "select id from " . DB_PREFIX . "payment where class_name = 'Account_Points'" );
			$payment_notice_id = make_payment_notice ( $points_money, $order_id, $account_payment_id );
			require_once APP_ROOT_PATH . "system/payment/Account_Points_payment.php";
			$account_points_payment = new Account_Points_payment ();
			$account_points_payment->get_payment_code ( $payment_notice_id );
		}

	    //3. 相应的支付接口
	    $payment_info = $data['payment_info'];
	    if($payment_info&&$data['pay_price']>0)
	    {
	        $payment_notice_id = make_payment_notice($data['pay_price'],$order_id,$payment_info['id']);
	        //创建支付接口的付款单
	    }

	    $rs = order_paid($order_id);

	    if($rs)
	    {
	        $data = array();
	        $data['info'] = "";
	        $data['jump'] = url("index","payment#done",array("id"=>$order_id));
	        ajax_return($data); //支付成功

	    }
	    else
	    {
	        $data = array();
	        $data['info'] = "";
	        $data['jump'] = url("index","payment#pay",array("id"=>$payment_notice_id));
	        ajax_return($data);
	    }
	}


	//确认提交订单
	public function order_done(){
	    global_run();
	    require_once APP_ROOT_PATH . "system/model/cart.php";
	    require_once APP_ROOT_PATH . "system/model/deal_order.php";
		$ajax = 1;
	    $payment = intval ( $_REQUEST['payment'] );
	    $account_money = floatval($_REQUEST['account_money']);
		$all_account_money = intval($_REQUEST['all_account_money']);
		$all_account_points = intval ( $GLOBALS ['request'] ['all_account_points'] );

	    $paypassword =  $_REQUEST['paypassword'] ? strim ( $_REQUEST['paypassword'] ) : '';
		$ecvsn = $_REQUEST['ecvsn'] ? strim ( $_REQUEST['ecvsn'] ) : '';
		$ecvpassword = $_REQUEST['ecvpassword'] ? strim ( $_REQUEST['ecvpassword'] ) : '';
		$memo = strim ( $_REQUEST['content'] );

		$id = intval($_REQUEST['id']);

		$order = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_order where id = ".$id." and is_delete = 0 and pay_status <> 2 and order_status <> 1 and user_id =".intval($GLOBALS['user_info']['id']));
// 		echo "select * from ".DB_PREFIX."deal_order where id = ".$id." and is_delete = 0 and pay_status <> 2 and order_status <> 1 and user_id =".intval($GLOBALS['user_info']['id']);exit;

		if(!$order)
		{
			showErr($GLOBALS['lang']['INVALID_ORDER_DATA'],$ajax);
		}

		$goods_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."deal_order_item where order_id = ".$order['id']);

		require_once APP_ROOT_PATH . "system/model/duobao.php";

		foreach($goods_list as $item)
		{
			$res = duobao::check_duobao_number_2($item, 0);
			if($res['status']==0)
				showErr($res['info'],$ajax);
		}

		// 结束验证订单接交信息

		$user_id = $GLOBALS['user_info']['id'];


	    //开始验证订单接交信息
	    $data = count_buy_total($payment,0,$all_account_money,$all_account_points,$paypassword,$ecvsn,$ecvpassword,$goods_list,$order['account_money'],$order['ecv_money'],0,'');


	    if(round($data['pay_price'],4)>0&&!$data['payment_info'])
	    {
	        showErr($GLOBALS['lang']['PLEASE_SELECT_PAYMENT'],$ajax);
	    }
	    //结束验证订单接交信息

	    //开始修正订单
	    // 开始生成订单
	    $order['total_price'] = $data['pay_total_price'];  //应付总额  商品价 - 会员折扣 + 运费 + 支付手续费
	    $order ['payment_id'] = $data ['payment_info'] ['id'];
	    $order ['payment_fee'] = $data ['payment_fee'];

        //更新订单数据
	    $GLOBALS['db']->autoExecute(DB_PREFIX."deal_order",$order,'UPDATE','id='.$order['id'],'SILENT');

	    //1. 余额支付
	    $account_money = $data['account_money'];
	    if(floatval($account_money) > 0)
	    {
	        $account_payment_id = $GLOBALS['db']->getOne("select id from ".DB_PREFIX."payment where class_name = 'Account'");
	        $payment_notice_id = make_payment_notice($account_money,$order['id'],$account_payment_id);
	        require_once APP_ROOT_PATH."system/payment/Account_payment.php";
	        $account_payment = new Account_payment();
	        $account_payment->get_payment_code($payment_notice_id);
	    }

	    //3. 相应的支付接口
	    $payment_info = $data['payment_info'];
	    if($payment_info&&$data['pay_price']>0)
	    {
	        $payment_notice_id = make_payment_notice($data['pay_price'],$order['id'],$payment_info['id']);
	        //创建支付接口的付款单
	    }

	    $rs = order_paid($order['id']);
	    if($rs)
	    {
	        $data = array();
	        $data['info'] = "";
	        $data['jump'] = url("index","payment#done",array("id"=>$order['id']));
	        ajax_return($data); //支付成功

	    }
	    else
	    {
	        $data = array();
	        $data['info'] = "";
	        $data['jump'] = url("index","payment#pay",array("id"=>$payment_notice_id));
	        ajax_return($data);
	    }
	}


	public function cart_duobao_new()
	{

		$sql_recomend = "SELECT DuobaoItem.id,DuobaoItem.name AS duobaoitem_name, DuobaoItem.icon,DuobaoItem.max_buy
                FROM
                	".DB_PREFIX."duobao_item as DuobaoItem
                    LEFT JOIN ".DB_PREFIX."duobao as DUOBAO ON DuobaoItem.duobao_id = DUOBAO.id
		        WHERE
		            DuobaoItem.is_effect = 1 AND
		            DuobaoItem.progress < 100 AND
                    DUOBAO.is_recomend=1";   //未开奖的
		$recomend_list=$GLOBALS['db']->getAll($sql_recomend." order by rand() limit 5");

		$GLOBALS['tmpl']->assign("recomend_list",$recomend_list);  //新品推荐列表

		$html=$GLOBALS['tmpl']->fetch("inc/duobao_new.html");
		$result['status']=1;
		$result['html']=$html;
		ajax_return($result);

	}
	//删除指定的购物车项
	public function del_cart()
	{
		global_run();
		//过滤
		foreach (explode(',',strim($_REQUEST['id'])) as $value) {
			if(intval($value)){
				$id_arr[]=intval($value);
			}
		}

		if($id_arr)
		{
			$id = implode(",",$id_arr);
			$sql = "delete from ".DB_PREFIX."deal_cart  where session_id = '".es_session::id()."' and id in (".$id.") and user_id=".intval($GLOBALS['user_info']['id']);
		}

		$GLOBALS['db']->query($sql);
		$op_result = $GLOBALS['db']->affected_rows();



		require_once APP_ROOT_PATH."system/model/cart.php";

		if($op_result>0)
		{
			$result_other = load_cart_list(true,0);
			$result = load_cart_list(true,1);

			$result['total_data']=$result_other['total_data'];
			$is_whole=0;
			$whole=0;


			foreach($result['cart_list'] as $k=>$v){
				$whole+=$result['cart_list'][$k]['is_effect'];
			}
			if($whole==intval(count($result['cart_list']))){
				$is_whole=1;
			}
			$GLOBALS['tmpl']->assign("cart_result",$result);
			$user_money=$GLOBALS['user_info']['money'];
		    $GLOBALS['tmpl']->assign("is_whole",$is_whole);
		    $GLOBALS['tmpl']->assign("user_money",$user_money);


		    $cart_data=array();

		    foreach($result['cart_list'] as $k=>$v){

		    	$cart_data[$k]['id']=$v['id'];
		    	$cart_data[$k]['unit_price']=$v['unit_price'];
		    	$cart_data[$k]['total_price']=$v['total_price'];
		    	$cart_data[$k]['number']=$v['number'];
		    	$cart_data[$k]['min_buy']=$v['min_buy'];
		    	$cart_data[$k]['type']=1;
		    	$cart_data[$k]['residue_count']=$v['residue_count'];
		    	$cart_data[$k]['user_max_buy']=$v['user_max_buy'];
		    	$order_number = $GLOBALS['db']->getOne("select sum(number) from ".DB_PREFIX."deal_order_item where duobao_item_id = ".$v['duobao_item_id']." and user_id = ".intval($GLOBALS['user_info']['id'])." and pay_status = 2 and refund_status = 0 ");
		    	$cart_data[$k]['order_number']=intval($order_number);
		    }


		    $GLOBALS['tmpl']->assign("jsondata",json_encode($cart_data));
			$data['html'] = $GLOBALS['tmpl']->fetch("inc/cart_list.html");

			ajax_return(array("status"=>1,"data"=>$data));
		}
		else
		{
			ajax_return(array("status"=>0));
		}

	}
	public function adjusted()
	{//购物车数量调整
		global_run();
		//strim($_REQUEST['id'])) intval($_REQUEST['id']))
		//调整方式类别，$type为0减，为1加，为2表单调整
		$type=intval($_REQUEST['type']);
		//调整最小值
		$number=intval($_REQUEST['buy_num']);
		$is_effect=intval($_REQUEST['is_effect']);
		//调整的目标
		$data_id=intval($_REQUEST['data_id']);
		$id=intval($_REQUEST['duobao_item_id']);

		$duobao_item = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."duobao_item where id = '".$id."'");

		if(!$duobao_item)
		{
			ajax_return(array("status"=>1,"info"=>"夺宝活动不存在"     ) );
		}
		if($duobao_item['progress']>=100)
		{
			ajax_return(array("status"=>1,"type"=>"夺宝活动已满额"     ) );
		}
		$cart_item = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_cart where user_id = ".$GLOBALS['user_info']['id']." and session_id='".es_session::id()."' and  duobao_item_id = ".$duobao_item['id']);

		$number = intval(floor($number/$duobao_item['min_buy'])*$duobao_item['min_buy']);
		if($type==2)
		{
			$number = $number<=0?$cart_item['number']:$number;
			$number = $number > intval($duobao_item['max_buy']-$duobao_item['current_buy']) ? intval($duobao_item['max_buy']-$duobao_item['current_buy']):$number;
		}else{
			$number = $number<=0?$duobao_item['min_buy']:$number;
			$number = $number >= $duobao_item['max_buy'] ? $duobao_item['max_buy']:$number;
		}

		if($type==0)
		{
			if($cart_item['number']>$duobao_item['min_buy']){
				$cart_item['number']-=$number;
				$cart_item['total_price']-=$number*$duobao_item['unit_price'];
				$cart_item['is_effect']=$is_effect;
			}
		}
		if($type==1)
		{
			$cart_item['number']+=$number;
			$cart_item['total_price']+=$number*$duobao_item['unit_price'];
			$cart_item['is_effect']=$is_effect;
		}
		if($type==2)
		{
			$cart_item['number']=$number;
			$cart_item['total_price']=$number*$duobao_item['unit_price'];
			$cart_item['is_effect']=$is_effect;
		}
		$cart_item['session_id'] = es_session::id();
		$cart_item['update_time'] = NOW_TIME;
		$GLOBALS['db']->autoExecute(DB_PREFIX."deal_cart",$cart_item,"UPDATE","id=".$cart_item['id']);

		$op_result = $GLOBALS['db']->affected_rows();
		require_once APP_ROOT_PATH."system/model/cart.php";

		if($op_result>0)
		{
			$result_other = load_cart_list(true,0);
			$result = load_cart_list(true,1);

			$result['total_data']=$result_other['total_data'];

			$is_whole=0;
			$whole=0;
			foreach($result['cart_list'] as $k=>$v){

				$whole+=$result['cart_list'][$k]['is_effect'];
			}
			if($whole==intval(count($result['cart_list']))){
				$is_whole=1;
			}
			$GLOBALS['tmpl']->assign("cart_result",$result);
			$user_money=$GLOBALS['user_info']['money'];
		    $GLOBALS['tmpl']->assign("user_money",$user_money);
		    $GLOBALS['tmpl']->assign("is_whole",$is_whole);
			$data['html'] = $GLOBALS['tmpl']->fetch("inc/cart_list.html");

			ajax_return(array("status"=>1,"data"=>$data));
		}
		else
		{
			ajax_return(array("status"=>0));
		}

	}


	public function whole()
	{//购物车数量调整
	global_run();
		require_once APP_ROOT_PATH."system/model/cart.php";
		$is_whole=$is_effect=intval($_REQUEST['is_effect']);

		$GLOBALS['db']->query("update ".DB_PREFIX."deal_cart set is_effect=".$is_effect." where session_id = '" . es_session::id () . "' and user_id = " . $GLOBALS['user_info']['id'] );

		$result_other = load_cart_list(true,0);
		$result = load_cart_list(true,1);


		$result['total_data']=$result_other['total_data'];


		$GLOBALS['tmpl']->assign("cart_result",$result);
		$user_money=$GLOBALS['user_info']['money'];
		$GLOBALS['tmpl']->assign("user_money",$user_money);
		$GLOBALS['tmpl']->assign("is_whole",$is_whole);
		$data['html'] = $GLOBALS['tmpl']->fetch("inc/cart_list.html");

		ajax_return(array("status"=>1,"data"=>$data));


	}

}
?>
