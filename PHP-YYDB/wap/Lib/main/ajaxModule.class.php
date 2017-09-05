<?php
// +----------------------------------------------------------------------
// | nthxmai.com 谷创商城夺宝
// +----------------------------------------------------------------------
// | Copyright (c) 2016 http://www.nthxmai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Ant（594177881@qq.com）
// +----------------------------------------------------------------------

class ajaxModule extends MainBaseModule
{

	public function duobao_status()
	{
		$duobao_item_id = intval($_REQUEST['duobao_item_id']);
		$data = call_api_core("duobao","duobao_status",array("duobao_item_id"=>$duobao_item_id));
		ajax_return($data);
	}
	
	public function update_dev_token()
	{
		$dev_type = strim($_REQUEST['dev_type']);
		$dev_token = strim($_REQUEST['dev_token']);
		$data = call_api_core("user","update_dev_token",array("dev_type"=>$dev_type,"dev_token"=>$dev_token));
		ajax_return($data);
	}
	
	public function send_sms_code()
	{
		$mobile = strim($_REQUEST['mobile']);
		$unique = intval($_REQUEST['unique']);
		$verify_code = strim($_REQUEST['verify_code']);
                $account = intval($_REQUEST['account']);
		$data = call_api_core("sms","send_sms_code",array("mobile"=>$mobile,"unique"=>$unique,"verify_code"=>$verify_code,"account"=>$account));
		ajax_return($data);
	}

	public function send_fxsms_code()
	{
		global_run();

		$mobile = $GLOBALS['user_info']['mobile'];
		$unique = intval($_REQUEST['unique']);
		$verify_code = strim($_REQUEST['verify_code']);
		if($mobile==""){
			$data['status']=0;
			$data['info']="请完善会员手机号";
			ajax_return($data);
		}
		$data = call_api_core("sms","send_sms_code",array("mobile"=>$mobile,"unique"=>$unique,"verify_code"=>$verify_code));
		ajax_return($data);
	}

	public function close_appdown()
	{
		es_cookie::set('is_app_down',1,3600*24*7);
	}


	public function count_buy_total()
	{
		
		$ecvsn = $_REQUEST['ecvsn']?addslashes(trim($_REQUEST['ecvsn'])):'';
		$ecvpassword = $_REQUEST['ecvpassword']?addslashes(trim($_REQUEST['ecvpassword'])):'';
		$payment = intval($_REQUEST['payment']);
		$all_account_money = intval($_REQUEST['all_account_money']);
		$all_account_points = intval($_REQUEST['all_account_points']);
		$paypassword = $_REQUEST['paypassword']?addslashes(trim($_REQUEST['paypassword'])):'';
		

		$data = call_api_core("cart","count_buy_total",array("ecvsn"=>$ecvsn,"ecvpassword"=>$ecvpassword,"payment"=>$payment,"all_account_money"=>$all_account_money,"all_account_points"=>$all_account_points));

		$feeinfo['feeinfo'] = $data['feeinfo'];
		$GLOBALS['tmpl']->assign("data",$feeinfo);
		$ajaxdata['html'] = $GLOBALS['tmpl']->fetch("inc/cart_total.html");
		$ajaxdata['pay_price'] = $data['pay_price'];
		$ajaxdata['is_pick'] = $data['is_pick'];
		ajax_return($ajaxdata);
	}
	
	public function count_buy_order_total()
	{
	
	    $ecvsn = $_REQUEST['ecvsn']?addslashes(trim($_REQUEST['ecvsn'])):'';
	    $ecvpassword = $_REQUEST['ecvpassword']?addslashes(trim($_REQUEST['ecvpassword'])):'';
	    $payment = intval($_REQUEST['payment']);
	    $all_account_money = intval($_REQUEST['all_account_money']);
                 $all_account_points = intval($_REQUEST['all_account_points']);
	    $order_id = intval($_REQUEST['order_id']);
	
	    $data = call_api_core("cart","count_buy_order_total",array("ecvsn"=>$ecvsn,"ecvpassword"=>$ecvpassword,"payment"=>$payment,"all_account_money"=>$all_account_money,"all_account_points"=>$all_account_points, "order_id"=>$order_id));
	
	    $feeinfo['feeinfo'] = $data['feeinfo'];
	    $GLOBALS['tmpl']->assign("data",$feeinfo);
	    $ajaxdata['html'] = $GLOBALS['tmpl']->fetch("inc/cart_total.html");
	    $ajaxdata['pay_price'] = $data['pay_price'];
	    $ajaxdata['is_pick'] = $data['is_pick'];
	    ajax_return($ajaxdata);
	}

	public function count_order_total()
	{
		$order_id = intval($_REQUEST['id']);
		$delivery_id =  intval($_REQUEST['delivery_id']); //配送方式
		$payment = intval($_REQUEST['payment']);
		$all_account_money = intval($_REQUEST['all_account_money']);
                         $all_account_points = intval($_REQUEST['all_account_points']);

		$data = call_api_core("cart","count_order_total",array("id"=>$order_id,"delivery_id"=>$delivery_id,"payment"=>$payment,"all_account_money"=>$all_account_money,"all_account_points"=>$all_account_points));

		$feeinfo['feeinfo'] = $data['feeinfo'];
		$GLOBALS['tmpl']->assign("data",$feeinfo);
		$ajaxdata['html'] = $GLOBALS['tmpl']->fetch("inc/cart_total.html");
		$ajaxdata['pay_price'] = $data['pay_price'];
		$ajaxdata['delivery_fee_supplier'] = $data['delivery_fee_supplier'];
		$ajaxdata['delivery_info'] = $data['delivery_info'];
		$ajaxdata['is_pick'] = $data['is_pick'];
		ajax_return($ajaxdata);
	}

	public function focus(){
	    global_run();
	    $param=array();
	    $param['uid'] = intval($_REQUEST['uid']);

	    $data = call_api_core("uc_home","focus",$param);

	    if($data['user_login_status']!=LOGIN_STATUS_LOGINED){
	        $data['info'] = "请先登录后操作";
	        $data['jump'] = wap_url("index","user#login");
	    }

	    ajax_return($data);
	}


	public function do_refund_coupon()
	{
		global_run();
		init_app_page();

		$param['id'] = intval($_REQUEST['id']);
		$data = call_api_core("biz_ordermanage","do_refund_coupon",$param);


		if ($data['biz_user_status']==0){ //用户未登录
			$return['status'] = 1000;
			ajax_return($return);
		}
		else
		{
			ajax_return($data);
		}
	}

	public function do_refuse_coupon()
	{
		global_run();
		init_app_page();

		$param['id'] = intval($_REQUEST['id']);
		$data = call_api_core("biz_ordermanage","do_refuse_coupon",$param);


		if ($data['biz_user_status']==0){ //用户未登录
			$return['status'] = 1000;
			ajax_return($return);
		}
		else
		{
			ajax_return($data);
		}
	}


	public function do_refund_item()
	{
		global_run();
		init_app_page();

		$param['id'] = intval($_REQUEST['id']);
		$data = call_api_core("biz_ordermanage","do_refund_item",$param);


		if ($data['biz_user_status']==0){ //用户未登录
			$return['status'] = 1000;
			ajax_return($return);
		}
		else
		{
			ajax_return($data);
		}
	}

	public function do_refuse_item()
	{
		global_run();
		init_app_page();

		$param['id'] = intval($_REQUEST['id']);
		$data = call_api_core("biz_ordermanage","do_refuse_item",$param);


		if ($data['biz_user_status']==0){ //用户未登录
			$return['status'] = 1000;
			ajax_return($return);
		}
		else
		{
			ajax_return($data);
		}
	}

	public function do_verify_delivery()
	{
		global_run();
		init_app_page();

		$param['id'] = intval($_REQUEST['id']);
		$data = call_api_core("biz_ordermanage","do_verify_delivery",$param);


		if ($data['biz_user_status']==0){ //用户未登录
			$return['status'] = 1000;
			ajax_return($return);
		}
		else
		{
			ajax_return($data);
		}
	}

	public function add_cart(){
            $data_id = intval($_REQUEST['data_id']);
            $buy_num = intval($_REQUEST['buy_num']);
           
            $data = call_api_core("cart","addcart",array("data_id"=>$data_id,"buy_num"=>$buy_num));
         
            $ajax_data['status'] = $data['status'];
            $ajax_data['info'] = $data['info'];
            $ajax_data['cart_item_num'] = $data['cart_item_num'];
            $ajax_data['jump'] = wap_url("index","user#login");
            ajax_return($ajax_data);
	}
        
        public function get_cart(){
            $data = call_api_core("cart","getcart");
            ajax_return($data);
        }
        
        public function del_cart(){
            $data = call_api_core("cart","del",array("id"=>  intval($_REQUEST['id'])));
            ajax_return($data);
        }
        
        public function load_index_list_data(){
            $data = call_api_core("index","wap_load_page",array("page"=>  intval($_REQUEST['page'])));

            $GLOBALS['tmpl']->assign("index_duobao_list",$data['index_duobao_list']);
            $data['html'] = $GLOBALS['tmpl']->fetch("inc/index_list_data.html");
            
            ajax_return($data);
        }
        
        function get_wx_app_userinfo()
        {
        	$m_config = getMConfig();//初始化手机端配置
        	$param = json_decode(trim($_REQUEST['param']),1);
        	if($param['err_code']==0)
        	{
	        	require_once APP_ROOT_PATH.'system/utils/weixin.php';
	        	$weixin=new weixin($m_config['wx_mappid'],$m_config['wx_mappsecret']);
	        	$wx_info = $weixin->scope_get_userinfo($param['code']);
	        	$is_login = wx_info_login($wx_info,1);
	        	if($is_login)
	        	{
	        		$url = get_gopreview();
// 	        		$url = preg_replace("/[&|?]show_prog=[^&]*/i", "", $url);
	        		ajax_return(array("err_code"=>intval($param['err_code']),"jump"=>$url));
	        	}
	        	else
	        	{
	        		ajax_return(array("err_code"=>intval($param['err_code'])));
	        	}
        	}
        	else
        	{
        		ajax_return(array("err_code"=>intval($param['err_code'])));
        	}
        	
        	
        }
}
?>