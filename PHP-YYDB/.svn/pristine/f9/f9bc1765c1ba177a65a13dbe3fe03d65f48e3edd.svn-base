<?php
// +----------------------------------------------------------------------
// | nthxmai.com 谷创商城夺宝
// +----------------------------------------------------------------------
// | Copyright (c) 2016 http://www.nthxmai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Ant（594177881@qq.com）
// +----------------------------------------------------------------------

class cartModule extends MainBaseModule
{

	public function index()
	{
		global_run();		
		init_app_page();
		
		$data = call_api_core("cart","index");
 
		if(empty($data['cart_list']))
		{
			//app_redirect(wap_url("index"));
		}
		
		$GLOBALS['tmpl']->assign("sms_lesstime",load_sms_lesstime());
		$GLOBALS['tmpl']->assign("data",$data);
                
		//生成json数据
		$jsondata = array();
		foreach($data['cart_list'] as $k=>$v)
		{       
			$bind_data = array();
			$bind_data['id'] = $v['id'];
			$bind_data['residue_count'] = $v['residue_count'];
			$bind_data['number'] = $v['number'];
			$bind_data['min_buy'] = $v['min_buy'];
                        $bind_data['unit_price'] = $v['unit_price'];
			
			$jsondata[$v['id']] = $bind_data;
		}

		$GLOBALS['tmpl']->assign("jsondata",json_encode($jsondata));
		$GLOBALS['tmpl']->display("cart.html");
	}
	
	public function addcart(){
	    global_run();
		$is_relate = false;
		$ids = $_REQUEST['id'];

	    if( !empty($ids)&&(is_array($ids)) ){
			$is_relate = true;
			$data = call_api_core("cart","addcartByRelate",array("ids"=>$ids,"deal_attr"=>$_REQUEST['dealAttrArray'], "staff_id"=>$_REQUEST['staff_id'], "main_id"=>$_REQUEST['main_id']));
		}else{
			$id = intval($ids);
			$deal_attr = array();
			if($_REQUEST['deal_attr'])
			{
				foreach($_REQUEST['deal_attr'] as $k=>$v)
				{
					$deal_attr[$k] = intval($v);
				}
			}
			$data = call_api_core("cart","addcart",array("id"=>$id,"deal_attr"=>$deal_attr, 'staff_id'=>$_REQUEST['staff_id']));
		}
		
	    $ajax_data = array();
	    $ajax_data['status'] = $data['status'];
	    if($data['status']==1)
	    {
	    	$ajax_data['jump'] = wap_url("index","cart");
	    }
	    elseif($data['status']==-1)
	    {
	    	$ajax_data['jump'] = wap_url("index","user#login");
	    }
	    else
	    {
			if( $is_relate ){
				//有没有购买成功的商品
//				$ajax_data['info'] = array();
//				foreach($data as $kk=>$info){
//					if( in_array($kk,$ids) ){
//						$ajax_data['info'][$kk] = $info;
//					}
//				}
				$ajax_data['jump'] = wap_url("index","cart");
			}else{
				$ajax_data['info'] = $data['info'];
			}
	    }
	    
	    ajax_return($ajax_data);
	}
	
	public function check_cart()
	{
		global_run();
		
		$num = array();
	    if($_REQUEST['num'])
	    {
	    	foreach($_REQUEST['num'] as $k=>$v)
	    	{
	    		$num[$k] = intval($v);
	    	}
	    }
	    
	    $mobile = strim($_REQUEST['mobile']);
	    $sms_verify = strim($_REQUEST['sms_verify']);
	    
	    $data = call_api_core("cart","check_cart",array("num"=>$num, "mobile"=>$mobile,"sms_verify"=>$sms_verify));

	    if($data['status'])
	    {
	    	$ajaxdata['jump'] = wap_url("index","cart#check");
	    	$ajaxdata['status'] = 1;
	    	ajax_return($ajaxdata);
	    }
	    elseif($data['status']==-1)
	    {
	    	$ajaxdata['status'] = -1;
	    	$ajaxdata['info'] = $data['info'];
	    	$ajaxdata['jump'] = wap_url("index","user#login");
	    	ajax_return($ajaxdata);
	    }
	    else
	    {
	    	$ajaxdata['status'] = 0;
	    	$ajaxdata['info'] = $data['info'];
            $ajaxdata['expire_ids'] = $data['expire_ids']?$data['expire_ids']:array();
	    	ajax_return($ajaxdata);
	    }
	}
	

	
	public function check()
	{
		global_run();		
		init_app_page();
        //避免重复提交
        //assign_form_verify();
		$data = call_api_core("cart","check");
		$data['cencel_url'] = wap_url("index");
		if(!$GLOBALS['is_weixin'])
		{
			foreach($data['payment_list'] as $k=>$v)
			{
				if($v['code']=="Wwxjspay")
				{
					unset($data['payment_list'][$k]);
				}
			}
		}
		else
		{
			foreach($data['payment_list'] as $k=>$v)
			{
				if($v['code']=="Upacpwap")
				{
					unset($data['payment_list'][$k]);
				}
			}
		}

		if($data['status']==-1)
		{
			app_redirect(wap_url("index","user#login"));
		}
		
		if(empty($data['cart_list']))
		{
			app_redirect(wap_url("index"));
		}
		
		$account_amount = round($GLOBALS['user_info']['money'],2);
		$GLOBALS['tmpl']->assign("account_amount",$account_amount);
		$GLOBALS['tmpl']->assign("data",$data);
                
		$GLOBALS['tmpl']->display("cart_check.html");
	}
	
	public function order()
	{
	    global_run();		
		init_app_page();
		
		$order_id = intval($_REQUEST['id']);
	    $data = call_api_core("cart","order",array("id"=>$order_id));
	    $data['order_id'] = $order_id;
		$data['cencel_url'] = wap_url("index");
		if(!$GLOBALS['is_weixin'])
		{
			foreach($data['payment_list'] as $k=>$v)
			{
				if($v['code']=="Wwxjspay")
				{
					unset($data['payment_list'][$k]);
				}
			}
		}
		else
		{
			foreach($data['payment_list'] as $k=>$v)
			{
				if($v['code']=="Upacpwap")
				{
					unset($data['payment_list'][$k]);
				}
			}
		}

		if($data['status']==-1)
		{
			app_redirect(wap_url("index","user#login"));
		}
		
		if(empty($data['cart_list']))
		{
			app_redirect(wap_url("index"));
		}
		
		$account_amount = round($GLOBALS['user_info']['money'],2);
		$GLOBALS['tmpl']->assign("account_amount",$account_amount);
		$GLOBALS['tmpl']->assign("data",$data);
                
		$GLOBALS['tmpl']->display("cart_order.html");
	}
	
	
	public function done()
	{
		global_run();
		//check_form_verify();
		$param['ecvsn'] = $_REQUEST['ecvsn']?addslashes(trim($_REQUEST['ecvsn'])):'';
		$param['ecvpassword'] = $_REQUEST['ecvpassword']?addslashes(trim($_REQUEST['ecvpassword'])):'';
		$param['payment'] = intval($_REQUEST['payment']);
		$param['all_account_money'] = intval($_REQUEST['all_account_money']);
		$param['all_account_points'] = intval($_REQUEST['all_account_points']);
		$param['content'] = strim($_REQUEST['content']);
		$param['paypassword'] = $_REQUEST['paypassword']?addslashes(trim($_REQUEST['paypassword'])):'';

		$data = call_api_core("cart","done",$param);

		$ajaxobj['is_app'] = $data['is_app'];
		$ajaxobj['order_id'] = $data['order_id'];
		// $ajaxobj['reload_url'] = SITE_DOMAIN.wap_url("index","cart#order",array("id"=>$data['order_id']));
		$ajaxobj['reload_url'] = SITE_DOMAIN.wap_url("index","payment#done",array("id"=>$data['order_id']));
		$ajaxobj['success_url'] = SITE_DOMAIN.wap_url("index","payment#done",array("id"=>$data['order_id'], 'is_done'=>1));
		if($data['status']==-1)
		{
			$ajaxobj['status'] = 1;
			$ajaxobj['jump'] = wap_url("index","user#login");
			ajax_return($ajaxobj);
		}
		elseif($data['status']==1)
		{
			$ajaxobj['status'] = 1;
			$ajaxobj['jump'] = SITE_DOMAIN.wap_url("index","payment#done",array("id"=>$data['order_id']));

			ajax_return($ajaxobj);
		}
		elseif($data['status']==2) //sdk
		{
			$ajaxobj['status'] = 2;
			$ajaxobj['sdk_code'] = $data['sdk_code'];
			ajax_return($ajaxobj);
		}
		else
		{
			$ajaxobj['status'] = $data['status'];
			$ajaxobj['info'] = $data['info'];
			ajax_return($ajaxobj);
		}
		
	}
	
	public function order_done()
	{
	    global_run();
	    $param['ecvsn'] = $_REQUEST['ecvsn']?addslashes(trim($_REQUEST['ecvsn'])):'';
	    $param['ecvpassword'] = $_REQUEST['ecvpassword']?addslashes(trim($_REQUEST['ecvpassword'])):'';
	    $param['payment'] = intval($_REQUEST['payment']);
	    $param['all_account_money'] = intval($_REQUEST['all_account_money']);
	    $param['all_account_points'] = intval($_REQUEST['all_account_points']);
	    $param['content'] = strim($_REQUEST['content']);
	    $param['order_id'] = intval($_REQUEST['order_id']);
	
	    
	    $data = call_api_core("cart","order_done",$param);
	
	    $ajaxobj['is_app'] = $data['is_app'];
	    $ajaxobj['order_id'] = $data['order_id'];
	   
	    $ajaxobj['reload_url'] = SITE_DOMAIN.wap_url("index","payment#done",array("id"=>$data['order_id']));
	    $ajaxobj['success_url'] = SITE_DOMAIN.wap_url("index","payment#done",array("id"=>$data['order_id'], 'is_done'=>1));
	    if($data['status']==-1)
	    {
	        $ajaxobj['status'] = 1;
	        $ajaxobj['jump'] = wap_url("index","user#login");
	        ajax_return($ajaxobj);
	    }
	    elseif($data['status']==1)
	    {
	        $ajaxobj['status'] = 1;
	        $ajaxobj['jump'] = SITE_DOMAIN.wap_url("index","payment#done",array("id"=>$data['order_id']));
	
	        ajax_return($ajaxobj);
	    }
	    elseif($data['status']==2) //sdk
	    {
	        $ajaxobj['status'] = 2;
	        $ajaxobj['sdk_code'] = $data['sdk_code'];
	        ajax_return($ajaxobj);
	    }
	    else
	    {
	        $ajaxobj['status'] = $data['status'];
	        $ajaxobj['info'] = $data['info'];
	        ajax_return($ajaxobj);
	    }
	
	}
 
	 
}
?>