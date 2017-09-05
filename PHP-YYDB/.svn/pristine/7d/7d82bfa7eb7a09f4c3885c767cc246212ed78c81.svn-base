<?php 
// +----------------------------------------------------------------------
// | nthxmai.com 谷创商城夺宝
// +----------------------------------------------------------------------
// | Copyright (c) 2016 http://www.nthxmai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Ant（594177881@qq.com）
// +----------------------------------------------------------------------

define("DEAL_OUT_OF_STOCK",4); //库存不足
define("DEAL_ERROR_MIN_USER_BUY",5); //用户最小购买数不足
define("DEAL_ERROR_MAX_USER_BUY",6); //用户最大购买数超出
define("EXIST_DEAL_COUPON_SN",1);  //消费券序列号已存在

define("DEAL_NOTICE",3); //未上线
define("DEAL_ONLINE",1); //进行中
define("DEAL_HISTORY",2); //过期

define("DEAL_NOT_SUCCESS",0); //未成团
define("DEAL_SUCCESS",1); //成团
define("DEAL_NOT_STOCK",2); //卖光

/**
 * 公用生成消费券的函数
 * 用于环境
 * 1. 自动发放的消费券，即有$order_item_id, 将生成 order_id, order_deal_id, 且user_id与order_id相关数据同步，begin_time与end_time也与deal的同步
 * 2. 无自定义sn，由系统自动生成，以deal_id数据的code为前缀
 * 3. 手动生成，指定user_id, is_valid, sn, password,begin_time,end_time, 无order_id与相关数据 * 
 */
function add_coupon($deal_id,$user_id,$is_valid=0,$sn='',$password='',$begin_time=0,$end_time=0,$order_item_id=0,$order_id=0,$staff_id=0 )
{
	$res = array('status'=>1,'info'=>'',$data=''); //用于返回的结果集
	$coupon_data = array();
	$deal_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal where id =".$deal_id);
	$order_item_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_order_item where id = ".$order_item_id);
	
	
	if($deal_info['deal_type']==1)
	{
		//按单
		$coupon_data['balance_price'] = $order_item_info['balance_total_price'];
		$coupon_data['add_balance_price'] = $order_item_info['add_balance_price_total'];
		$coupon_data['coupon_price'] = $order_item_info['total_price'];
		
		$coupon_data['coupon_score'] = $order_item_info['return_total_score'];
		$coupon_data['coupon_money'] = $order_item_info['return_total_money'];
		$coupon_data['staff_id'] = $staff_id;
		$coupon_data['deal_type'] = 1;

	}
	else
	{
		//按件
		$coupon_data['balance_price'] = $order_item_info['balance_unit_price'];
		$coupon_data['add_balance_price'] = $order_item_info['add_balance_price'];
		$coupon_data['coupon_price'] = $order_item_info['unit_price'];
		
		$coupon_data['coupon_score'] = $order_item_info['return_score'];
		$coupon_data['coupon_money'] = $order_item_info['return_money'];
		$coupon_data['staff_id'] = $staff_id;
		$coupon_data['deal_type'] = 0;
	}
	
	//自动发券
	if($order_id>0)
	{
		$order_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_order where id = ".$order_id);			
		$coupon_data['user_id'] = $order_info['user_id'];
		$coupon_data['order_id'] = $order_info['id'];
		$coupon_data['order_deal_id'] = $order_item_id;
	}
	else
	{
		$coupon_data['user_id'] = $user_id;
	}
	
	if($deal_info['coupon_time_type']==0)
	{
		if($begin_time == 0)
		{
			$coupon_data['begin_time'] = $deal_info['coupon_begin_time'];
		}
		else
		{
			$coupon_data['begin_time'] = $begin_time;
		}
		
		if($end_time == 0)
		{
			$coupon_data['end_time'] = $deal_info['coupon_end_time'];
		}
		else
		{
			$coupon_data['end_time'] = $end_time;
		}
	}
	else
	{
		$day = $deal_info['coupon_day'];
		if($begin_time == 0)
		$coupon_data['begin_time'] = NOW_TIME;
		else
		{
			$coupon_data['begin_time'] = $begin_time;
		}
		
		if($end_time == 0)
		{
			if($day>0)
			{
				$coupon_data['end_time'] = NOW_TIME+$day*3600*24;
			}
		}
		else
		{
			$coupon_data['end_time'] = $end_time;
		}
		
	}
	
	if($sn!='')
	{
		if($GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."deal_coupon where sn='".$sn."'")>0)
		{
			$res['status'] = 0;
			$res['info'] = EXIST_DEAL_COUPON_SN;
			return $res;
		}
		$coupon_data['sn'] = $sn;
	}
	else
	{
		$coupon_data['sn'] = $deal_info['code'].$deal_id.rand(100000,999999);
	}
	
	if($password!='')
	{
		$coupon_data['password'] = $password;
	}
	else
	{
		$password = unpack('H8',str_shuffle(md5(uniqid())));
		$password = $password[1];
		$coupon_data['password'] = $password;
	}
	
	$coupon_data['is_valid'] = $is_valid;
	$coupon_data['deal_id'] = $deal_id;
	$coupon_data['supplier_id'] = $order_item_info['supplier_id'];
	$coupon_data['expire_refund'] = $deal_info['expire_refund'];
	$coupon_data['any_refund'] = $deal_info['any_refund'];
	while($GLOBALS['db']->autoExecute(DB_PREFIX."deal_coupon",$coupon_data,'INSERT','','SILENT')==false)
	{
		$coupon_data['sn'] = $deal_info['code'].$deal_id.rand(100000,999999);
		$password = unpack('H8',str_shuffle(md5(uniqid())));
		$password = $password[1];
		$coupon_data['password'] = $password;
	}
	
	$res['data'] = $coupon_data;
	return $res;
		
}


/**
 * 检测团购的时间状态
 * $id 团购ID
 * 
 */
function check_deal_time($id)
{
	$deal_info = get_deal($id);
	$now = NOW_TIME;
	
			
	//开始验证团购时间
	if($deal_info['begin_time']!=0)
	{
		//有开始时间
		if($now<$deal_info['begin_time'])
		{		
			$result['status'] = 0;
			$result['data'] = DEAL_NOTICE;  //未上线
			$result['info'] = $deal_info['sub_name'];
			return $result;
		}
	}
	

			
	if($deal_info['end_time']!=0)
	{
		//有结束时间
		if($now>=$deal_info['end_time'])
		{
			$result['status'] = 0;
			$result['data'] = DEAL_HISTORY;  //过期
			$result['info'] = $deal_info['sub_name'];
			return $result;
		}
	}
	//验证团购时间
	
	$result['status'] = 1;
	$result['info'] = $deal_info['name'];
	return $result;	
}

/**
 * 检测团购的数量状态
 * $id 团购ID
 * $number 数量
 */
function check_deal_number($id,$number = 0,$type='')
{
	require_once APP_ROOT_PATH."system/model/cart.php";
	$cart_result = load_cart_list();
	
	
	$id = intval($id);
	$deal_info = get_deal($id);
	
	
	/*验证数量*/	
	//定义几组需要的数据
	//1. 本团购记录下的购买量
	$deal_buy_count = $deal_info['buy_count'];
	//2. 本团购当前会员的购物车中数量
	$deal_user_cart_count = 0;
	foreach($cart_result['cart_list'] as $k=>$v)
	{
		if($v['deal_id']==$id)
		{
			$deal_user_cart_count += intval($v['number']);
		}
	}
	//3. 本团购当前会员已付款的数量
	$deal_user_paid_count = intval($GLOBALS['db']->getOne("select sum(oi.number) from ".DB_PREFIX."deal_order_item as oi left join ".DB_PREFIX."deal_order as o on oi.order_id = o.id where o.user_id = ".intval($GLOBALS['user_info']['id'])." and o.pay_status = 2 and oi.deal_id = ".$id." and o.is_delete = 0"));
	//4. 本团购当前会员未付款的数量
	$deal_user_unpaid_count = intval($GLOBALS['db']->getOne("select sum(oi.number) from ".DB_PREFIX."deal_order_item as oi left join ".DB_PREFIX."deal_order as o on oi.order_id = o.id where o.user_id = ".intval($GLOBALS['user_info']['id'])." and o.pay_status <> 2 and o.order_status = 0 and oi.deal_id = ".$id." and o.is_delete = 0"));
	
	$invalid_count = 0;
	foreach($cart_result['cart_list'] as $k=>$v)
	{
		if($v['number']<=0)
		{
			$invalid_count++;
		}
	}
	if($invalid_count>0)
	{
		$result['status'] = 0;
		$result['data'] = DEAL_ERROR_MIN_USER_BUY;  //用户最小购买数不足
		$result['info'] = $deal_info['sub_name']." ".sprintf($GLOBALS['lang']['DEAL_USER_MIN_BOUGHT'],1);
		return $result;
	}
	
	if($deal_info['max_bought'] == 0||($deal_user_cart_count+$deal_user_unpaid_count+$number>$deal_info['max_bought']&&$deal_info['max_bought']>=0))
	{		
		$result['status'] = 0;
		$result['data'] = DEAL_OUT_OF_STOCK;  //库存不足
		$result['info'] = $deal_info['sub_name']." ".sprintf($GLOBALS['lang']['DEAL_MAX_BOUGHT'],$deal_info['max_bought']);
		return $result;
	}
	
	if($deal_user_cart_count + $deal_user_unpaid_count + $number < $deal_info['user_min_bought'] && $deal_info['user_min_bought'] > 0)
	{
		$result['status'] = 0;
		$result['data'] = DEAL_ERROR_MIN_USER_BUY;  //用户最小购买数不足
		$result['info'] = $deal_info['sub_name']." ".sprintf($GLOBALS['lang']['DEAL_USER_MIN_BOUGHT'],$deal_info['user_min_bought']);
		return $result;
	}

	
	if($deal_user_cart_count + $deal_user_paid_count + $deal_user_unpaid_count + $number > $deal_info['user_max_bought'] && $deal_info['user_max_bought'] > 0)
	{
		$result['status'] = 0;
		$result['data'] = DEAL_ERROR_MAX_USER_BUY;  //用户最大购买数超出
		$result['info'] = $deal_info['sub_name']." ".sprintf($GLOBALS['lang']['DEAL_USER_MAX_BOUGHT'],$deal_info['user_max_bought']);
		return $result;
	}
	
	
	/*验证数量*/
	
	$result['status'] = 1;
	$result['info'] = $deal_info['sub_name'];
	return $result;	
}


/**
 * 检测团购的属性数量状态
 * $id 团购ID
 * $attr_setting 属性组合的字符串
 * $number 数量
 */
function check_deal_number_attr($id,$attr_setting,$number=0)
{
	require_once APP_ROOT_PATH."system/model/cart.php";
	$cart_result = load_cart_list();
	
	$id = intval($id);	
	$deal_info = get_deal($id);
	
	$attr_stock_cfg = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."attr_stock where deal_id = ".$id." and locate(attr_str,'".$attr_setting."') > 0 ");

	
	$stock_setting = $attr_stock_cfg?intval($attr_stock_cfg['stock_cfg']):-1;
	$stock_attr_setting = $attr_stock_cfg['attr_str'];
	// 获取到当前规格的库存
		
	/*验证数量*/	
	//定义几组需要的数据
	//1. 本团购记录下的购买量
	$deal_buy_count = intval($attr_stock_cfg['buy_count']);
	//2. 本团购当前会员的购物车中数量
	$deal_user_cart_count = 0;
	foreach($cart_result['cart_list'] as $k=>$v)
	{
		if($v['deal_id']==$id&&strpos($v['attr_str'],$stock_attr_setting)!==false)
		{
			$deal_user_cart_count+=intval($v['number']);
		}
	}
	//3. 本团购当前会员未付款的数量
	$deal_user_unpaid_count = intval($GLOBALS['db']->getOne("select sum(oi.number) from ".DB_PREFIX."deal_order_item as oi left join ".DB_PREFIX."deal_order as o on oi.order_id = o.id where o.user_id = ".intval($GLOBALS['user_info']['id'])." and o.pay_status <> 2 and o.order_status = 0 and oi.deal_id = ".$id." and o.is_delete = 0 and oi.attr_str like '%".$stock_attr_setting."%'"));
	

	if($stock_setting == 0||($deal_user_cart_count+$deal_user_unpaid_count+$number>$stock_setting&&$stock_setting>=0))
	{		
		$result['status'] = 0;
		$result['data'] = DEAL_OUT_OF_STOCK;  //库存不足
		$result['info'] = $deal_info['sub_name'].$stock_attr_setting." ".sprintf($GLOBALS['lang']['DEAL_MAX_BOUGHT'],$stock_setting);
		$result['attr'] = $stock_attr_setting;
		return $result;
	}
	/*验证数量*/
	
	$result['status'] = 1;
	$result['info'] = $deal_info['sub_name'];	
	return $result;	

}


/**
 * 获取指定的团购产品
 * @param unknown_type $key 商品的关键ID或uname
 * @param unknown_type $preview 是否为管理员预览
 * @return number
 */
function get_deal($key,$preview=false)
{
	static $deals;
	$deal = $deals[$key];	
	if($deal)return $deal;
	
	$deal = load_auto_cache("deal",array("id"=>$key));

	if($deal)
	{
		if(!$preview&&$deal['is_effect']==0) //未生效的商品，在非预览状态下不可见
		return false;
		
		//重定义time_status
		if($deal['begin_time']>NOW_TIME)
		{
			if($deal['notice']==1||$preview) //未开团不允许预告的团购，预览状态下可见
				$deal['time_status'] = DEAL_NOTICE; //未开始
			else
				return false; //不允许预告
		}
		elseif($deal['end_time']>0&&$deal['end_time']<=NOW_TIME)
		{
			$deal['time_status'] = DEAL_HISTORY; //已过期
		}
		else
		{
			$deal['time_status'] = DEAL_ONLINE; //上线中
		}
		
		//重定义buy_status
		if($deal['min_bought']>$deal['buy_count'])
		{
			$deal['buy_status'] = DEAL_NOT_SUCCESS; //未成团
		}
		elseif($deal['max_bought']==0)
		{
			$deal['buy_status'] = DEAL_NOT_STOCK; //卖光
		}
		else
		{
			$deal['buy_status'] = DEAL_SUCCESS; //成团
		}
			
		//格式化数据
		$deal['begin_time_format'] = to_date($deal['begin_time']);
		$deal['end_time_format'] = to_date($deal['end_time']);
		$deal['coupon_begin_time_format'] = to_date($deal['coupon_begin_time'],"Y-m-d");
		$deal['coupon_end_time_format'] = to_date($deal['coupon_end_time'],"Y-m-d");
		$deal['origin_price_format'] = format_price($deal['origin_price']);
		$deal['current_price_format'] = format_price($deal['current_price']);
		$deal['success_time_format']  = to_date($deal['success_time']);
			
		if($deal['origin_price']>0&&floatval($deal['discount'])==0) //手动折扣
			$deal['save_price'] = $deal['origin_price'] - $deal['current_price'];
		else
			$deal['save_price'] = $deal['origin_price']*((10-$deal['discount'])/10);
			
		if($deal['origin_price']>0&&floatval($deal['discount'])==0)
			$deal['discount'] = round(($deal['current_price']/$deal['origin_price'])*10,1);

		$deal['discount'] = round($deal['discount'],2);
			
		$deal['save_price_format'] = format_price($deal['save_price']);

		$deal['deal_success_num'] = sprintf($GLOBALS['lang']['SUCCESS_BUY_COUNT'],$deal['buy_count']);
		$deal['current_bought'] = $deal['buy_count'];
			
		//团购图片集
		$img_list = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."deal_gallery where deal_id=".intval($deal['id'])." order by sort asc");
		
		if($img_list)
		{
			$img_list[0]['current'] = 1;
			$deal['image_list'] = $img_list;
			$deal['icon'] = $img_list[0]['img'];						
		}		
		if(count($deal['image_list'])<=1)
		{
			unset($deal['image_list']);
		}
			
// 		//商户信息
		if($deal['supplier_id']>0)
		{
 			$deal['supplier_info'] = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."supplier where id = ".intval($deal['supplier_id']));
 			//$deal['supplier_address_info'] = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."supplier_location where supplier_id = ".intval($deal['supplier_id'])." and is_main = 1");
 			$deal['supplier_info']['url'] = url("index","stores",array("supplier_id"=>$deal['supplier_id']));

            // 获取门店数量
            $sql = "select count(*) from ".DB_PREFIX."deal_location_link where  deal_id = ".$deal['id'];
            $deal['supplier_location_count'] = $GLOBALS['db']->getOne($sql,false);

 			//获取门店QQ号
 			$deal['location_qqs'] = $GLOBALS['db']->getAll("select name,location_qq from ".DB_PREFIX."supplier_location where supplier_id=".$deal['supplier_id']." limit 0,3");
		}

		//品牌信息
		if($deal['brand_id']>0)
		{
			$deal['brand_info'] = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."brand where id = ".intval($deal['brand_id']));
			if($deal['brand_info'])
				$deal['brand_info']['url'] = url("index","brand#".$deal['brand_id']);
		}
			
		//规格属性选择
		$deal_attr = $GLOBALS['db']->getAll("select id,name from ".DB_PREFIX."goods_type_attr where goods_type_id = ".$deal['deal_goods_type']);
		foreach($deal_attr as $k=>$v)
		{
			$deal_attr[$k]['attr_list'] = $GLOBALS['db']->getAll("select id,name,price,is_checked from ".DB_PREFIX."deal_attr where deal_id = ".$deal['id']." and goods_type_attr_id = ".$v['id']);
			if(!$deal_attr[$k]['attr_list'])
				unset($deal_attr[$k]);
		}
		$deal['deal_attr'] = $deal_attr;
		//开始输出库存json
		$attr_stock_list =$GLOBALS['db']->getAll("select * from ".DB_PREFIX."attr_stock where deal_id = ".$deal['id'],false);
		$attr_stock_data = array();
		foreach($attr_stock_list as $row)
		{
			$row['attr_cfg'] = unserialize($row['attr_cfg']);
			$attr_stock_data[$row['attr_key']] = $row;
		}
		$deal['deal_attr_stock_json'] = json_encode($attr_stock_data);
		
		$durl = $deal['url'];
			
		$deal['share_url'] = SITE_DOMAIN.$durl;
		if($GLOBALS['user_info'])
		{
			if(app_conf("URL_MODEL")==0)
			{
				$deal['share_url'] .= "&r=".base64_encode(intval($GLOBALS['user_info']['id']));
			}
			else
			{
				$deal['share_url'] .= "?r=".base64_encode(intval($GLOBALS['user_info']['id']));
			}
		}
		
		//开始解析商品标签
		for($tt=0;$tt<10;$tt++)
		{
			if(($deal['deal_tag']&pow(2,$tt))==pow(2,$tt))
			{
				$deal['deal_tags'][] = $tt;
			}
		}
		
		//$deal['is_today'] = get_is_today($deal);
		
		//查询抽奖号
		//$deal['lottery_count'] = intval($GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."lottery where deal_id = ".intval($deal['id'])." and buyer_id <> 0 ")) + intval($deal['buy_count']);
	}
	$deals[$deal['id']] = $deal;
	$deals[$deal['uname']] = $deal;
	return $deal;

}


/**
 * 获取指定条件的商品数量
 */
function get_deal_count($type=array(DEAL_ONLINE,DEAL_HISTORY,DEAL_NOTICE),$param=array("cid"=>0,"tid"=>0,"aid"=>0,"qid"=>0,"city_id"=>0), $join='', $where='')
{
	if(empty($param))
		$param=array("cid"=>0,"tid"=>0,"aid"=>0,"qid"=>0,"city_id"=>0);
	
	$tname = "d";
	$time = $GLOBALS['db']->getCacheTime(NOW_TIME);
	$condition = ' '.$tname.'.is_effect = 1 and '.$tname.'.is_delete = 0 and ( 1<>1 ';
	if(in_array(DEAL_ONLINE,$type))
	{
		//进行中的团购
		$condition .= " or ((".$time.">= ".$tname.".begin_time or ".$tname.".begin_time = 0) and (".$time."< ".$tname.".end_time or ".$tname.".end_time = 0) and ".$tname.".buy_status <> 2) ";
	}
	
	if(in_array(DEAL_HISTORY,$type))
	{
		//往期团购
		$condition .= " or ((".$time.">=".$tname.".end_time and ".$tname.".end_time <> 0) or ".$tname.".buy_status = 2) ";
	}
	if(in_array(DEAL_NOTICE,$type))
	{
		//预告
		$condition .= " or ((".$time." < ".$tname.".begin_time and ".$tname.".begin_time <> 0 and ".$tname.".notice = 1)) ";
	}
	
	$condition .= ')';
	
	
	$param_condition = build_deal_filter_condition($param,$tname);
	$condition.=" ".$param_condition;
	
	if($where != '')
	{
		$condition.=" and ".$where;
	}
	
	if($join)
		$sql = "select count(*) from ".DB_PREFIX."deal as ".$tname." ".$join." where  ".$condition;
	else
		$sql = "select count(*) from ".DB_PREFIX."deal as ".$tname." where  ".$condition;

	$count = $GLOBALS['db']->getOne($sql,false);
	return $count;
}

/**
 * 获取产品列表
 */
function get_deal_list($limit,$type=array(DEAL_ONLINE,DEAL_HISTORY,DEAL_NOTICE),$param=array("cid"=>0,"tid"=>0,"aid"=>0,"qid"=>0,"city_id"=>0), $join='', $where='',$orderby = '',$append_field="")
{


	$condition .= ')';


	$param_condition = build_deal_filter_condition($param,$tname);

	$condition.=" ".$param_condition;

	if($where != '')
	{
		$condition.=" and ".$where;
	}

	if($join)
		$sql = "select ".$tname.".*".$append_field." from ".DB_PREFIX."deal as ".$tname." ".$join." where  ".$condition;
	else
		$sql = "select ".$tname.".*".$append_field." from ".DB_PREFIX."deal as ".$tname." where  ".$condition;

	if($orderby=='')
		$sql.=" order by ".$tname.".sort desc limit ".$limit;
	else
		$sql.=" order by ".$orderby." limit ".$limit;
	
	$sql = "select * from ".DB_PREFIX."deal";
	$deals = $GLOBALS['db']->getAll($sql,false);
        
	if($deals)
	{
		foreach($deals as $k=>$deal)
		{
			//格式化数据
			$deal['begin_time_format'] = to_date($deal['begin_time']);
			$deal['end_time_format'] = to_date($deal['end_time']);
			$deal['origin_price_format'] = format_price($deal['origin_price']);
			$deal['current_price_format'] = format_price($deal['current_price']);
			$deal['success_time_format']  = to_date($deal['success_time']);

			if($deal['origin_price']>0&&floatval($deal['discount'])==0) //手动折扣
				$deal['save_price'] = $deal['origin_price'] - $deal['current_price'];
			else
				$deal['save_price'] = $deal['origin_price']*((10-$deal['discount'])/10);
			if($deal['origin_price']>0&&floatval($deal['discount'])==0)
			{
				$deal['discount'] = round(($deal['current_price']/$deal['origin_price'])*10,2);
			}

			$deal['discount'] = round($deal['discount'],2);

			if($deal['uname']!='')
				$durl = url("index","deal#".$deal['uname']);
			else
				$durl = url("index","deal#".$deal['id']);
			$deal['share_url'] = SITE_DOMAIN.$durl;
			$deal['url'] = $durl;

				

			//$deal['is_today'] = get_is_today($deal);
			$deal['save_price_format'] = format_price($deal['save_price']);
			$deal['deal_success_num'] = sprintf($GLOBALS['lang']['SUCCESS_BUY_COUNT'],$deal['buy_count']);
			$deal['current_bought'] = $deal['buy_count'];

			$deal['percent'] = $deal['avg_point']/5.0*100.0;
			$deals[$k] = $deal;
		}
	}

	return array('list'=>$deals,'condition'=>$condition);
}


/**
 * 构建商品查询条件
 * @param unknown_type $param
 * @return string
 */
function build_deal_filter_condition($param,$tname="")
{
	$area_id = intval($param['aid']);
	$quan_id = intval($param['qid']);
	$cate_id = intval($param['cid']);
	$deal_type_id = intval($param['tid']);
	$city_id = intval($param['city_id']);
	$condition = "";
	if($city_id>0)
	{		
		$city_list_result = load_auto_cache("city_list_result");
		$city_list = $city_list_result['ls'];
		$city_name = $city_list[$city_id]['name'];
		
		$unicode_city_name = str_to_unicode_string($city_name);
		if($tname)
			$condition.=" and ((match(".$tname.".city_match) against('".$unicode_city_name."'  IN BOOLEAN MODE))  or ".$tname.".city_match='') ";
		else
			$condition.=" and ((match(city_match) against('".$unicode_city_name."'  IN BOOLEAN MODE)) or city_match='' ) ";
		
	}
	if($area_id>0)
	{
		if($quan_id>0)
		{

			$area_name = $GLOBALS['db']->getOne("select name from ".DB_PREFIX."area where id = ".$quan_id);
			$kw_unicodes[] = str_to_unicode_string($area_name);
				
			$kw_unicode = implode(" ",$kw_unicodes);
			//有筛选
			if($tname)
				$condition .=" and (match(".$tname.".locate_match) against('".$kw_unicode."' IN BOOLEAN MODE)) ";
			else
				$condition .=" and (match(locate_match) against('".$kw_unicode."' IN BOOLEAN MODE)) ";
		}
		else
		{
			//$ids = load_auto_cache("deal_quan_ids",array("quan_id"=>$area_id));
			$quan_list = $GLOBALS['db']->getAll("select `name` from ".DB_PREFIX."area where id=".$area_id." or  pid=".$area_id);
			$unicode_quans = array();
			foreach($quan_list as $k=>$v){
				$unicode_quans[] = str_to_unicode_string($v['name']);
			}
			$kw_unicode = implode(" ", $unicode_quans);
			if($tname)
				$condition .= " and (match(".$tname.".locate_match) against('".$kw_unicode."' IN BOOLEAN MODE))";
			else
				$condition .= " and (match(locate_match) against('".$kw_unicode."' IN BOOLEAN MODE))";
		}
	}

	if($cate_id>0)
	{
		$cate_name = $GLOBALS['db']->getOne("select name from ".DB_PREFIX."deal_cate where id = ".$cate_id);
		$cate_name_unicode = str_to_unicode_string($cate_name);
			
		if($deal_type_id>0)
		{
			$deal_type_name = $GLOBALS['db']->getOne("select name from ".DB_PREFIX."deal_cate_type where id = ".$deal_type_id);
			$deal_type_name_unicode = str_to_unicode_string($deal_type_name);
			if($tname)
				$condition .= " and (match(".$tname.".deal_cate_match) against('+".$cate_name_unicode." +".$deal_type_name_unicode."' IN BOOLEAN MODE)) ";
			else
				$condition .= " and (match(deal_cate_match) against('+".$cate_name_unicode." +".$deal_type_name_unicode."' IN BOOLEAN MODE)) ";
		}
		else
		{
			if($tname)
				$condition .= " and (match(".$tname.".deal_cate_match) against('".$cate_name_unicode."' IN BOOLEAN MODE)) ";
			else
				$condition .= " and (match(deal_cate_match) against('".$cate_name_unicode."' IN BOOLEAN MODE)) ";
		}
	}
	
	//品牌
	if($param['bid']>0)
	{
		$condition.=" and ".$tname.".brand_id = ".$param['bid'];
	}
	
	
	//标签
	foreach($param as $k=>$v)
	{
		if(preg_match("/fid_(\d+)/i", $k,$matches))
		{
			if($v!="")
			{
				$unicode_tags[] = "+".str_to_unicode_string($v);
			}
		}
	}
	if(count($unicode_tags)>0)
	{
		$kw_unicode = implode(" ", $unicode_tags);
		//有筛选
		$condition .=" and (match(".$tname.".tag_match) against('".$kw_unicode."' IN BOOLEAN MODE))";
	}
	return $condition;
}


/**
 * 获取某个条件的商品数量
 */
function get_goods_count($type=array(DEAL_ONLINE,DEAL_HISTORY,DEAL_NOTICE),$param=array("cid"=>0), $join='', $where='')
{
	if(empty($param))
		$param=array("cid"=>0);
	
	$tname = "d";
	$time = $GLOBALS['db']->getCacheTime(NOW_TIME);
	$condition = ' '.$tname.'.is_effect = 1 and '.$tname.'.is_delete = 0 and ( 1<>1 ';
	if(in_array(DEAL_ONLINE,$type))
	{
		//进行中的团购
		$condition .= " or ((".$time.">= ".$tname.".begin_time or ".$tname.".begin_time = 0) and (".$time."< ".$tname.".end_time or ".$tname.".end_time = 0) and ".$tname.".buy_status <> 2) ";
	}
	
	if(in_array(DEAL_HISTORY,$type))
	{
		//往期团购
		$condition .= " or ((".$time.">=".$tname.".end_time and ".$tname.".end_time <> 0) or ".$tname.".buy_status = 2) ";
	}
	if(in_array(DEAL_NOTICE,$type))
	{
		//预告
		$condition .= " or ((".$time." < ".$tname.".begin_time and ".$tname.".begin_time <> 0 and ".$tname.".notice = 1)) ";
	}
	
	$condition .= ')';
	
	
	$param_condition = build_goods_filter_condition($param,$tname);
	$condition.=" ".$param_condition;
	
	if($where != '')
	{
		$condition.=" and ".$where;
	}
	
	if($join)
		$sql = "select count(*) from ".DB_PREFIX."deal as ".$tname." ".$join." where  ".$condition;
	else
		$sql = "select count(*) from ".DB_PREFIX."deal as ".$tname." where  ".$condition;

	
	
	$count = $GLOBALS['db']->getOne($sql,false);
	return $count;
}

/**
 * 获取商品列表
 * cid:分类ID, bid:品牌ID, fid_x: ID为x的分组筛选的关键词,kw:关键词,city_id:城市ID
 */
function get_goods_list($limit,$type=array(DEAL_ONLINE,DEAL_HISTORY,DEAL_NOTICE),$param=array("cid"=>0), $join='', $where='',$orderby = '')
{
	if(empty($param))
		$param=array("cid"=>0);

	$tname = "d";
	$time = $GLOBALS['db']->getCacheTime(NOW_TIME);
	$condition = ' '.$tname.'.is_effect = 1 and '.$tname.'.is_delete = 0 and ( 1<>1 ';
	if(in_array(DEAL_ONLINE,$type))
	{
		//进行中的团购
		$condition .= " or ((".$time.">= ".$tname.".begin_time or ".$tname.".begin_time = 0) and (".$time."< ".$tname.".end_time or ".$tname.".end_time = 0) and ".$tname.".buy_status <> 2) ";
	}

	if(in_array(DEAL_HISTORY,$type))
	{
		//往期团购
		$condition .= " or ((".$time.">=".$tname.".end_time and ".$tname.".end_time <> 0) or ".$tname.".buy_status = 2) ";
	}
	if(in_array(DEAL_NOTICE,$type))
	{
		//预告
		$condition .= " or ((".$time." < ".$tname.".begin_time and ".$tname.".begin_time <> 0 and ".$tname.".notice = 1)) ";
	}

	$condition .= ')';


	$param_condition = build_goods_filter_condition($param,$tname);
	$condition.=" ".$param_condition;

	if($where != '')
	{
		$condition.=" and ".$where;
	}

	if($join)
		$sql = "select ".$tname.".* from ".DB_PREFIX."deal as ".$tname." ".$join." where  ".$condition;
	else
		$sql = "select ".$tname.".* from ".DB_PREFIX."deal as ".$tname." where  ".$condition;

	if($orderby=='')
		$sql.=" order by ".$tname.".sort desc limit ".$limit;
	else
		$sql.=" order by ".$orderby." limit ".$limit;


	$deals = $GLOBALS['db']->getAll($sql,false);
	//		echo $count_sql;
	if($deals)
	{
		foreach($deals as $k=>$deal)
		{
			//格式化数据
			$deal['begin_time_format'] = to_date($deal['begin_time']);
			$deal['end_time_format'] = to_date($deal['end_time']);
			$deal['origin_price_format'] = format_price($deal['origin_price']);
			$deal['current_price_format'] = format_price($deal['current_price']);
			$deal['success_time_format']  = to_date($deal['success_time']);

			if($deal['origin_price']>0&&floatval($deal['discount'])==0) //手动折扣
				$deal['save_price'] = $deal['origin_price'] - $deal['current_price'];
			else
				$deal['save_price'] = $deal['origin_price']*((10-$deal['discount'])/10);
			if($deal['origin_price']>0&&floatval($deal['discount'])==0)
			{
				$deal['discount'] = round(($deal['current_price']/$deal['origin_price'])*10,2);
			}

			$deal['discount'] = round($deal['discount'],2);

			if($deal['uname']!='')
				$durl = url("index","deal#".$deal['uname']);
			else
				$durl = url("index","deal#".$deal['id']);
			$deal['share_url'] = SITE_DOMAIN.$durl;
			$deal['url'] = $durl;


			if($GLOBALS['user_info'])
			{
				if(app_conf("URL_MODEL")==0)
				{
					$deal['share_url'] .= "&r=".base64_encode(intval($GLOBALS['user_info']['id']));
				}
				else
				{
					$deal['share_url'] .= "?r=".base64_encode(intval($GLOBALS['user_info']['id']));
				}
			}


			//$deal['is_today'] = get_is_today($deal);
			$deal['save_price_format'] = format_price($deal['save_price']);
			$deal['deal_success_num'] = sprintf($GLOBALS['lang']['SUCCESS_BUY_COUNT'],$deal['buy_count']);
			$deal['current_bought'] = $deal['buy_count'];
			
			//开始解析商品标签
			for($tt=0;$tt<10;$tt++)
			{
				if(($deal['deal_tag']&pow(2,$tt))==pow(2,$tt))
				{
					$deal['deal_tags'][] = $tt;
				}
			}

			$deal['percent'] = $deal['avg_point']/5.0*100.0;
			$deals[$k] = $deal;
		}
	}
	return array('list'=>$deals,'condition'=>$condition);
}


/**
 * 获得商品的查询条件，根据输入的参数
 * cid:分类ID, bid:品牌ID, fid_x: ID为x的分组筛选的关键词,city_id:城市ID
 */
function build_goods_filter_condition($filter_param,$tname="")
{
	 $condition = " ";
	 
	 //禁用商城的城市传入
// 	 $city_id = intval($filter_param['city_id']);
	 
// 	 if($city_id>0)
// 	 {
// 	 	$ids = load_auto_cache("deal_city_belone_ids",array("city_id"=>$city_id));
// 	 	if($ids)
// 	 	{
// 	 		if($tname)
// 	 			$condition .= " and ".$tname.".city_id in (".implode(",",$ids).")";
// 	 		else
// 	 			$condition .= " and city_id in (".implode(",",$ids).")";
// 	 	}
// 	 }
	 
	 
	 //分类
	 if($filter_param['cid']>0)
	 {
	 		$cate_cache = load_auto_cache("cache_shop_cate",array("all"=>1));
	 		$cate_info = $cate_cache[$filter_param['cid']];
	 		$cate_name_unicode = "";	 		
	 		while($cate_info)
	 		{
	 			$cate_name_unicode .= " +".str_to_unicode_string($cate_info['name'])." ";
	 			$cate_info = $cate_cache[$cate_info['pid']];
	 		}
	 		if($cate_name_unicode)
	 		$condition .=" and (match(".$tname.".shop_cate_match) against('".$cate_name_unicode."' IN BOOLEAN MODE))";	 	
	 }
	 //品牌
	 if($filter_param['bid']>0)
	 {
	 	  $condition.=" and ".$tname.".brand_id = ".$filter_param['bid'];
	 }
	
	 
	 //标签
	 foreach($filter_param as $k=>$v)
	 {
	 	if(preg_match("/fid_(\d+)/i", $k,$matches))
	 	{
	 			if($v!="")
	 			{	 				
	 				$unicode_tags[] = "+".str_to_unicode_string($v);	 	 				
	 			}
	 	}
	 }
	 if(count($unicode_tags)>0)
	 {
	 	$kw_unicode = implode(" ", $unicode_tags);
	 	//有筛选
	 	$condition .=" and (match(".$tname.".tag_match) against('".$kw_unicode."' IN BOOLEAN MODE))";
	 }
	 

	 
	 return $condition;
}

/**
 * 服务/商品保存
 * @param type $param_data 所有参数
 * @param type $is_admin    是否为后台提交:1 后台 2 商户中心自动审核
 *          关键不同的ID {
 *                          id  主表ID   deal 表
 *                          edit_id  商户提交的ID  deal_submit 表
 *                          edit_type  1. 后台提交  2. 商户提交
 *                      }
 * @return array(status=>1,info=>错误消息)
 */
function deal_save($param_data,$is_admin = 1){
	$edit_type = intval($param_data['edit_type']);  //编辑的商家提交数据
        $edit_id = intval($param_data['edit_id']);

        if($is_admin == 1){
            $data['is_shop'] = intval($param_data['is_shop']);
            $data['buy_type'] = intval($param_data['buy_type']);
            //基础数据接收
            $data['name'] = strim($param_data['name']);
            $data['sub_name'] = strim($param_data['sub_name']);
            $data['brief'] = strim($param_data['brief']);
            $data['uname'] = strim($param_data['uname']);
            $data['supplier_id'] = intval($param_data['supplier_id']);
            $data['order_manage'] = intval($param_data['order_manage']); //派单方式
            $data['channel_id'] = intval($param_data['channel_id']);
            $data['cate_id'] = intval($param_data['cate_id']); //分类ID
            $data['shop_cate_id'] = intval($param_data['shop_cate_id']);  //积分分类ID
            $data['brand_id'] = intval($param_data['brand_id']); //品牌ID
            $data['city_id'] = intval($param_data['city_id']); //城市ID 0全国 -1多城市

            $data['is_join'] = intval($param_data['is_join']);
            $data['agent_id'] = $GLOBALS['db']->getOne("select agent_id from ".DB_PREFIX."supplier where id = '".$data['supplier_id']."'");

            $channel_item = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."channel where id = '".$data['channel_id']."'");
            if($channel_item['delivery_type']==0) //同城
            {
                    $data['is_depot'] = 0;  //是否开启区域库存
                    $data['depot_type'] = 0; //区域库存配置  0:未设置区域有库存 1:未设置区域无库存
            }
            else
            {
                    //异地
                    if($data['is_depot']==0)
                    {
                            $data['depot_type'] = 0;
                    }
            }

            //购物车状态
            /**
             * `open_supplier` tinyint(1) NOT NULL COMMENT '是否允许多商户合并付款，常见于商城应用',
             * `open_cart` tinyint(1) NOT NULL COMMENT '是否开启购物车（只允许同商家才可以买多件商品），0：不开启则只能单件购买，不含配件',
             * 
             * `cart_type` tinyint(1) NOT NULL COMMENT '购物车规则
             * 0:启用购物车(每次可以买多款)
             * 1按商品(同款商品可买多款属性)
             * 2按商家(同个商家可买多款商品)
             * 3禁用购物车(每次只能买一款)
             * 
             */
            if($channel_item['open_cart']==1)
            {
                    if($channel_item['open_supplier']==1)
                    {
                            $data['cart_type'] = 0;
                    }
                    else
                    {
                            $data['cart_type'] = 2;
                    }
            }
            else
            {
                    $data['cart_type'] = 3;  //禁用购物车
            }


            //动态计算标签
            $deal_tags = $param_data['deal_tag'];
            $deal_tag = 0;
            foreach($deal_tags as $t)
            {
                    $t2 = pow(2,$t);
                    $deal_tag = $deal_tag|$t2;
            }
            $data['deal_tag'] = $deal_tag;

            $data['buyin_app'] = intval($param_data['buyin_app']);
            $data['icon'] = strim($param_data['icon']);
            //将第一张图片设为团购图片
            $imgs = $param_data['img'];
            foreach($imgs as $k=>$v)
            {
                    if($v!='')
                    {
                            $data['img'] = $v;
                            break;
                    }
            }

            $data['description'] = btrim($param_data['description']);
            $data['notes'] = btrim($param_data['notes']);
            $data['sort'] = intval($param_data['sort']);
            $data['is_effect'] = intval($param_data['is_effect']);
            $data['is_recommend'] = intval($param_data['is_recommend']);


            //时间与价格
            $data['create_time'] = NOW_TIME;
            $data['update_time'] = NOW_TIME;
            $data['notice'] = intval($param_data['notice']);
            $data['begin_time'] = strim($param_data['begin_time'])==''?0:to_timespan($param_data['begin_time']);
            $data['end_time'] = strim($param_data['end_time'])==''?0:to_timespan($param_data['end_time']);
            $data['buy_count'] = intval($param_data['buy_count']);
            $data['max_bought'] = intval($param_data['max_bought']); //库存
            $data['user_min_bought'] = intval($param_data['user_min_bought']); //会员最小购买量
            $data['user_max_bought'] = intval($param_data['user_max_bought']); //会员最大购买量
            $data['origin_price'] = floatval($param_data['origin_price']);
            $data['balance_price'] = floatval($param_data['balance_price']);
            $data['current_price'] = floatval($param_data['current_price']);
            $data['return_money'] = floatval($param_data['return_money']);
            $data['return_score'] = intval($param_data['return_score']);


            //类型属性
            $data['order_verify'] = intval($param_data['order_verify']);  //订单确认方式 0:自动确认 1:人工确认
            $data['user_type'] = intval($param_data['user_type']); //消费方式 0到店  1上门

            if($data['user_type']==1)
            {
                    $data['is_pick'] = intval($param_data['is_pick']);
            }
            else
            {
                    $data['is_pick'] = 1;
            }

            $data['coupon_time_type'] = intval($param_data['coupon_time_type']); //有效期类型  0指定日期 1指定天数
            $data['coupon_begin_time'] = strim($param_data['coupon_begin_time'])==''?0:to_timespan($param_data['coupon_begin_time']);
            $data['coupon_end_time'] = strim($param_data['coupon_end_time'])==''?0:to_timespan($param_data['coupon_end_time']);
            $data['coupon_day'] = intval($param_data['coupon_day']);
            $data['is_refund'] = intval($param_data['is_refund']);
            //支持退款表示为随时退
            $data['any_refund'] = $data['is_refund'];
            $data['expire_refund'] = intval($param_data['expire_refund']); //过期退
            $data['is_coupon'] = intval($param_data['is_coupon']); //是否发券
            $data['deal_type'] = intval($param_data['deal_type']); //发券类型 0按件 1按单
            $data['forbid_sms'] = intval($param_data['forbid_sms']); //是否禁用发券短信
            $data['is_fitting'] = intval($param_data['is_fitting']); //是否为配件
            $data['only_fitting'] = intval($param_data['only_fitting']); //仅为配件购买
            $data['define_payment'] = intval($param_data['define_payment']); //是否定义禁用的支付方式 0否 1是
            $data['is_delivery'] = intval($param_data['is_delivery']); //是否计算运费
            $data['weight'] = floatval($param_data['weight']);  //重量
            $data['weight_id'] = intval($param_data['weight_id']);  //计量单位
            $data['free_delivery'] = intval($param_data['free_delivery']);  //是否开启免运费
            $data['is_depot'] = intval($param_data['is_depot']);  //是否开启区域库存
            $data['depot_type'] = intval($param_data['depot_type']); //区域库存配置  0:未设置区域有库存 1:未设置区域无库存
            $data['allow_promote'] = intval($param_data['allow_promote']); //是否参与促销计划
            $data['deal_goods_type'] = intval($param_data['deal_goods_type']); //商品类型
            $data['max_schedule'] = intval($param_data['max_schedule']); //最大预约天数

            //修正数据集
            if($data['user_type']==0)
            {
                    //到店
                    $data['is_delivery'] = 0; //是否计算运费
                    $data['weight'] = 0;  //重量
                    $data['weight_id'] = 0;  //计量单位
                    $data['free_delivery'] = 0;  //是否开启免运费
                    unset($param_data['free_count_lbs']);
                    unset($param_data['free_count']);
                    unset($param_data['delivery_id_lbs']);
                    unset($param_data['delivery_id']);
                    unset($param_data['forbid_delivery_id_lbs']);
                    unset($param_data['forbid_delivery_id']);
            }
            else
            {
                    //上门
                    $data['expire_refund'] = 1;
                    if($data['is_delivery']==0)
                    {
                            $data['weight'] = 0;  //重量
                            $data['weight_id'] = 0;  //计量单位
                            $data['free_delivery'] = 0;  //是否开启免运费
                            unset($param_data['free_count_lbs']);
                            unset($param_data['free_count']);
                            unset($param_data['delivery_id_lbs']);
                            unset($param_data['delivery_id']);
                            unset($param_data['forbid_delivery_id_lbs']);
                            unset($param_data['forbid_delivery_id']);
                    }
                    else
                    {
                            if($data['free_delivery']==0)
                            {
                                    unset($param_data['free_count_lbs']);
                                    unset($param_data['free_count']);
                                    unset($param_data['delivery_id_lbs']);
                                    unset($param_data['delivery_id']);
                            }
                    }
            }

            if($data['coupon_time_type']==0)
            {
                    //指定日期
                    $data['coupon_day'] = 0;
            }
            else
            {
                    //指定天数
                    $data['coupon_begin_time'] = 0;
                    $data['coupon_end_time'] = 0;
            }

            if($data['is_coupon']==0)
            {
                    $data['deal_type'] = 0;
                    $data['forbid_sms'] = 0;
            }

            if($data['is_fitting']==0)
            {
                    $data['only_fitting'] = 0;
            }

            if($data['order_manage']==1||$data['order_manage']==3)  //卖家抢单人工派单模式，只允许人工确认定单
            {
                $data['order_verify'] = 1;
            }

            if($data['define_payment']==0)
            {
                    //未开启禁用支付方式
                    unset($param_data['payment_id']);
            }

            if($data['deal_goods_type'] == 0) //未选取商品类型
            {
                    unset($param_data['deal_attr']);
                    unset($param_data['deal_attr_price']);
                    unset($param_data['deal_add_balance_price']);
                    unset($param_data['deal_attr_stock']);
                    unset($param_data['stock_attr']);
                    unset($param_data['stock_cfg_num']);
            }

            $score_price = intval($param_data['score_price']);
            if($score_price)
            {
                    $data['return_score'] = "-".$score_price;
            }

            //seo设置
            $data['seo_title'] = strim($param_data['seo_title']);
            $data['seo_keyword'] = strim($param_data['seo_keyword']);
            $data['seo_description'] = strim($param_data['seo_description']);


            //开始数据验证
            $err_str = '';
            if(!$data['name'])
            {
                $err_str = "请输入项目名称";
            }
            if(!$data['sub_name'] && $err_str=='')
            {
                $err_str = "请输入项目副标题";
            }
            if(!preg_match("/^[a-z]*$/", $data['uname']) && $err_str=='')
            {
                $err_str = "URL名称只能使用小写字母";
            }		
            if(!$data['icon'] && $err_str=='')
            {
                $err_str = "请上传缩略图";
            }
            if($data['buy_count']<0 && $err_str=='')
            {
                $err_str = "销量不能为负数";
            }
            if($data['max_bought']<-1 && $err_str=='')
            {
                $err_str = "库存非法";
            }
            if($data['user_min_bought']<0 && $err_str=='')
            {
                $err_str = "会员最小购买量不能为负数";
            }
            if($data['user_max_bought']<0 && $err_str=='')
            {
                $err_str = "会员最大购买量不能为负数";
            }
            if($data['user_min_bought']>$data['user_max_bought'] && $err_str=='')
            {
                $err_str = "会员最小购买量不能大于最大购买量";
            }
            if($data['origin_price']<$data['current_price'] && $err_str=='')
            {
                $err_str = "原价请勿小于现价";
            }

            if($data['buy_type']==0 && $err_str=='')
            {
                    if($data['channel_id']==0 && $err_str=='')
                    {
                        $err_str = "请选择频道";
                    }
                    if($data['cate_id']==0 && $err_str=='')
                    {
                        $err_str = "请选择分类";
                    }
                    if($data['return_score']<0 && $err_str=='')
                    {
                        $err_str = "积分返还不能为负数";
                    }
                    if($data['return_money']<0 && $err_str=='')
                    {
                        $err_str = "现金返还不能为负数";
                    }
            }
            else
            {
                    if($data['shop_cate_id']==0 && $err_str=='')
                    {
                        $err_str = "请选择分类";
                    }
            }

            $result = array();
            //判断是否有错误
            if($err_str){
                $result['status'] = 0;
                $result['info'] = $err_str;
                return $result;
            }
        }else{//商户自动发布
            $data = $param_data;
        }
        
//            [cache_deal_cate_type_id] => a:2:{i:0;s:2:"77";i:1;s:2:"78";}
//            [cache_location_id] => a:1:{i:0;s:2:"36";}
//            [cache_focus_imgs] => a:1:{i:0;s:50:"./public/attachment/201509/21/17/55ffd047aadcd.jpg";}
//            [cache_staff_list] => a:2:{i:0;a:1:{s:8:"staff_id";i:13;}i:1;a:1:{s:8:"staff_id";i:12;}}
//            [cache_relate] => a:1:{i:0;i:110;}
//            [cache_deal_attr] => a:0:{}
//            [cache_attr_stock] => a:0:{}
//            [cache_deal_schedule] => a:0:{}
//            [cache_deal_filter] => a:0:{}
//            [cache_depot] => a:0:{}
        //取出所有缓存数据，并从$data 中删除掉
        if($is_admin == 2){
            $cache_deal_cate_type_id = unserialize($data['cache_deal_cate_type_id']);   //子分类
            $cache_location_id = unserialize($data['cache_location_id']);               //门店
            $cache_focus_imgs = unserialize($data['cache_focus_imgs']);                 //图集
            $cache_staff_list = unserialize($data['cache_staff_list']);                 //服务人员
            $cache_relate = unserialize($data['cache_relate']);                         //配件
            $cache_deal_attr = unserialize($data['cache_deal_attr']);                   //属性
            $cache_attr_stock = unserialize($data['cache_attr_stock']);                 //属性库存
            $cache_deal_schedule = unserialize($data['cache_deal_schedule']);           //排期
            $cache_deal_filter = unserialize($data['cache_deal_filter']);               //过滤
            $cache_depot = unserialize($data['cache_depot']);                           //区域库存
            
            $cache_free_delivery = unserialize($data['cache_free_delivery']);
            $cache_deal_delivery = unserialize($data['cache_deal_delivery']);
                    
            unset($data['cache_deal_cate_type_id']);
            unset($data['cache_location_id']);
            unset($data['cache_focus_imgs']);
            unset($data['cache_staff_list']);
            unset($data['cache_relate']);
            unset($data['cache_deal_attr']);
            unset($data['cache_attr_stock']);
            unset($data['cache_deal_schedule']);
            unset($data['cache_deal_filter']);
            unset($data['cache_depot']);
            
            unset($data['cache_free_delivery']);
            unset($data['cache_deal_delivery']);
            
            unset($data['admin_check_status']);
        }
        
        $id = intval($param_data['id']);


        if($id>0)
        {
                //保存
                $GLOBALS['db']->autoExecute(DB_PREFIX."deal",$data,"UPDATE","id=".$id);
                $err = $GLOBALS['db']->error();
        }
        else
        {
                //新增
                $GLOBALS['db']->autoExecute(DB_PREFIX."deal",$data);
                $id = $GLOBALS['db']->insert_id();
                $err = $GLOBALS['db']->error();
        }

        if($err)
        {
                $result['status'] = 0;
                $result['info'] = $err;
                return $result;
        }
        else
        {

            //商户提交
            if($edit_type==2 && $edit_id>0){
                if($is_admin)
                    $depot = unserialize($GLOBALS['db']->getOne("select cache_depot from ".DB_PREFIX."deal_submit where id=".$edit_id));
                else
                    $depot = $cache_depot;
                
                foreach ($depot as $k=>$v){
                    $stock_data = array();
                    $depot_id = intval($v['id']);
                    $stock_data['deal_id'] = $id;
                    $stock_data['region_id'] = $v['region_id'];
                    $stock_data['stock_cfg'] = $v['stock_cfg'];
                    if($depot_id)
                    {
                            $GLOBALS['db']->autoExecute(DB_PREFIX."depot",$stock_data,"UPDATE","id=".$depot_id);
                    }
                    else
                    {
                            $GLOBALS['db']->autoExecute(DB_PREFIX."depot",$stock_data);
                    }

                }

                /*同步商户发布表状态*/
                $GLOBALS['db']->autoExecute(DB_PREFIX."deal_submit",array("admin_check_status"=>1),"UPDATE","id=".$edit_id); // 1 通过 2 拒绝',

            }
                $data['id'] = $id;

                $deal_stock = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_stock where deal_id = '".$data['id']."'");
                if(!$deal_stock)
                {
                        $deal_stock['deal_id'] = $data['id'];
                        $deal_stock['stock_cfg'] = $data['max_bought'];
                        $GLOBALS['db']->autoExecute(DB_PREFIX."deal_stock",$deal_stock,"INSERT","","SILENT");
                }
                else
                {
                        $GLOBALS['db']->query("update ".DB_PREFIX."deal_stock set stock_cfg = ".$data['max_bought']." where deal_id = ".$data['id']);
                }

                if($data['is_depot']==0)
                $GLOBALS['db']->query("delete from ".DB_PREFIX."depot where deal_id = '".$data['id']."'");



                //同步消费券
                $GLOBALS['db']->query("update ".DB_PREFIX."deal_coupon set expire_refund = ".$data['expire_refund'].",any_refund = ".$data['any_refund'].",supplier_id=".$data['supplier_id']." where deal_id = '".$id."'");

                if($data['coupon_time_type']==0)
                        $GLOBALS['db']->query("update ".DB_PREFIX."deal_coupon set end_time=".$data['coupon_end_time'].",begin_time=".$data['coupon_begin_time']." where deal_id = '".$id."'");


                //开始处理图片
                $GLOBALS['db']->query("delete from ".DB_PREFIX."deal_gallery where deal_id = '".$id."'");
                if($is_admin == 1)
                    $imgs = $param_data['img'];
                else
                    $imgs = $cache_focus_imgs;
                
                foreach($imgs as $k=>$v)
                {
                        if($v!='')
                        {
                                $img_data['deal_id'] = $id;
                                $img_data['img'] = strim($v);
                                $img_data['sort'] = intval($k);
                                $GLOBALS['db']->autoExecute(DB_PREFIX."deal_gallery",$img_data);
                        }
                }
                //end 处理图片


                //开始处理属性
                $new_ids = array(0);
                if($is_admin == 1){
                    $deal_attr = $param_data['deal_attr'];
                    $deal_attr_price = $param_data['deal_attr_price'];
                    $deal_add_balance_price = $param_data['deal_add_balance_price'];
                    $deal_attr_stock_hd		= $param_data['deal_attr_stock_hd'];
                    
                    foreach($deal_attr as $goods_type_attr_id=>$arr)
                    {
                            foreach($arr as $k=>$v)
                            {
                                    if($v!='')
                                    {					
                                            $deal_attr_item = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_attr where name = '".$v."' and goods_type_attr_id = '".$goods_type_attr_id."' and deal_id = '".$data['id']."'");
                                            if($deal_attr_item)
                                            {
                                                    $deal_attr_item['add_balance_price'] = $deal_add_balance_price[$goods_type_attr_id][$k];
                                                    $deal_attr_item['price'] = $deal_attr_price[$goods_type_attr_id][$k];
                                                    $deal_attr_item['is_checked'] = intval($deal_attr_stock_hd[$goods_type_attr_id][$k]);
                                                    $GLOBALS['db']->autoExecute(DB_PREFIX."deal_attr",$deal_attr_item,"UPDATE","id=".$deal_attr_item['id']);
                                                    $new_ids[] = $deal_attr_item['id'];
                                            }
                                            else
                                            {
                                                    $deal_attr_item['deal_id'] = $data['id'];
                                                    $deal_attr_item['goods_type_attr_id'] = $goods_type_attr_id;
                                                    $deal_attr_item['name'] = $v;
                                                    $deal_attr_item['add_balance_price'] = $deal_add_balance_price[$goods_type_attr_id][$k];
                                                    $deal_attr_item['price'] = $deal_attr_price[$goods_type_attr_id][$k];
                                                    $deal_attr_item['is_checked'] = intval($deal_attr_stock_hd[$goods_type_attr_id][$k]);
                                                    $GLOBALS['db']->autoExecute(DB_PREFIX."deal_attr",$deal_attr_item);
                                                    $new_ids[] = intval($GLOBALS['db']->insert_id());
                                            }
                                    }
                            }
                    }
                }else{
                    foreach($cache_deal_attr as $deal_attr_k=>$deal_attr_item)
                    {
                        $deal_attr_item_res = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_attr where name = '".$deal_attr_item['name']."' and goods_type_attr_id = '".$deal_attr_item['goods_type_attr_id']."' and deal_id = '".$data['id']."'");
                        if($deal_attr_item_res)
                        {
                                $GLOBALS['db']->autoExecute(DB_PREFIX."deal_attr",$deal_attr_item,"UPDATE","id=".$deal_attr_item_res['id']);
                                $new_ids[] = $deal_attr_item_res['id'];
                        }
                        else
                        {            	
                                $deal_attr_item['deal_id'] = $data['id'];
                                $GLOBALS['db']->autoExecute(DB_PREFIX."deal_attr",$deal_attr_item);
                                $new_ids[] = $GLOBALS['db']->insert_id();
                        }
                    }
                }
                
                $sql = "delete from ".DB_PREFIX."deal_attr where id not in (".implode(",",$new_ids).") and deal_id = '".$data['id']."'";
                $GLOBALS['db']->query($sql);

                //开始创建属性库存
                $new_ids = array(0);
                if($is_admin == 1){
                    //M("AttrStock")->where("deal_id=".$data['id'])->delete();
                    $stock_cfg = $param_data['stock_cfg_num'];
                    $attr_cfg = $param_data['stock_attr'];
                    $attr_str = $param_data['stock_cfg'];
                    $new_ids = array(0);
                    foreach($stock_cfg as $row=>$v)
                    {
                            $stock_data = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."attr_stock where deal_id = '".$data['id']."' and attr_str = '".$attr_str[$row]."'");
                            if($stock_data)
                            {
                                    $stock_data['stock_cfg'] = $v;
                                    $GLOBALS['db']->autoExecute(DB_PREFIX."attr_stock",$stock_data,"UPDATE","id=".$stock_data['id']);
                                    $new_ids[] = $stock_data['id'];
                            }
                            else
                            {
                                    $stock_data = array();
                                    $stock_data['deal_id'] = $data['id'];
                                    $stock_data['stock_cfg'] = $v;
                                    $stock_data['attr_str'] = $attr_str[$row];
                                    $attr_cfg_data = array();
                                    foreach($attr_cfg as $attr_id=>$cfg)
                                    {
                                            $attr_cfg_data[$attr_id] = $cfg[$row];
                                    }
                                    $stock_data['attr_cfg'] = serialize($attr_cfg_data);
                                    // 					$sql = "select sum(oi.number) from ".DB_PREFIX."deal_order_item as oi left join ".
                                    // 							DB_PREFIX."deal as d on d.id = oi.deal_id left join ".
                                    // 							DB_PREFIX."deal_order as do on oi.order_id = do.id where".
                                    // 							" do.pay_status = 2 and do.is_delete = 0 and d.id = ".$data['id'].
                                    // 							" and oi.attr_str like '%".$attr_str[$row]."%'";

                                    // 					$stock_data['buy_count'] = intval($GLOBALS['db']->getOne($sql));
                                    $GLOBALS['db']->autoExecute(DB_PREFIX."attr_stock",$stock_data);
                                    $new_ids[] = intval($GLOBALS['db']->insert_id());
                            }
                    }
                }else{
                    foreach($cache_attr_stock as $attr_stock_k=>$attr_stock_v){
                        
                        $stock_data = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."attr_stock where deal_id = '".$data['id']."' and attr_str = '".$attr_stock_v['attr_str']."'");
                        if($stock_data)
                        {
                                $GLOBALS['db']->autoExecute(DB_PREFIX."attr_stock",$attr_stock_v,"UPDATE","id=".$stock_data['id']);
                                $new_ids[] = $stock_data['id'];
                        }
                        else
                        {
                                $attr_stock_v['deal_id'] = $data['id'];
                                $GLOBALS['db']->autoExecute(DB_PREFIX."attr_stock",$attr_stock_v);
                                $new_ids[] = $GLOBALS['db']->insert_id();
                        }
                    }
                }
                
                $sql = "delete from ".DB_PREFIX."attr_stock where id not in (".implode(",",$new_ids).") and deal_id = '".$data['id']."'";
                $GLOBALS['db']->query($sql);

                //开始处理运费
                if(intval($param_data['is_delivery'])==1)
                {
                        if($channel_item['delivery_type']==0)
                        {
                                $is_lbs = 1;
                        }
                        else
                        {
                                $is_lbs = 0;
                        }

                        if(intval($param_data['free_delivery'])==1) //开启免运费
                        {
                                $GLOBALS['db']->query("delete from ".DB_PREFIX."free_delivery where deal_id = '".$data['id']."'");
                                if($is_admin == 1){
                                    if($is_lbs==0)
                                    {
                                            foreach($param_data['delivery_id'] as $k=>$delivery_id)
                                            {
                                                    $link_data = array();
                                                    $link_data['deal_id'] = $data['id'];
                                                    $link_data['delivery_id'] = intval($delivery_id);
                                                    $link_data['free_count'] = intval($param_data['free_count'][$k]);
                                                    $link_data['is_lbs'] = $is_lbs;
                                                    $GLOBALS['db']->autoExecute(DB_PREFIX."free_delivery",$link_data);
                                            }
                                    }
                                    else
                                    {
                                            foreach($param_data['delivery_id_lbs'] as $k=>$delivery_id)
                                            {
                                                    $link_data = array();
                                                    $link_data['deal_id'] = $data['id'];
                                                    $link_data['delivery_id'] = intval($delivery_id);
                                                    $link_data['free_count'] = intval($param_data['free_count_lbs'][$k]);
                                                    $link_data['is_lbs'] = $is_lbs;
                                                    $GLOBALS['db']->autoExecute(DB_PREFIX."free_delivery",$link_data);
                                            }
                                    }
                                }else{
                                    foreach($cache_free_delivery as $k=>$free_delivery_item)
                                    {
                                            $free_delivery_item['deal_id'] = $data['id'];
                                            $GLOBALS['db']->autoExecute(DB_PREFIX."free_delivery",$free_delivery_item);
                                    }
                                }
                                
                        }


                        $GLOBALS['db']->query("delete from ".DB_PREFIX."deal_delivery where deal_id = '".$data['id']."'");
                        if($is_admin == 1){
                            if($is_lbs==0)
                            {
                                    foreach($param_data['forbid_delivery_id'] as $k=>$delivery_id)
                                    {
                                            $link_data = array();
                                            $link_data['deal_id'] = $data['id'];
                                            $link_data['delivery_id'] = intval($delivery_id);
                                            $link_data['is_lbs'] = $is_lbs;
                                            $GLOBALS['db']->autoExecute(DB_PREFIX."deal_delivery",$link_data);
                                    }
                            }
                            else
                            {
                                    foreach($param_data['forbid_delivery_id_lbs'] as $k=>$delivery_id)
                                    {
                                            $link_data = array();
                                            $link_data['deal_id'] = $data['id'];
                                            $link_data['delivery_id'] = intval($delivery_id);
                                            $link_data['is_lbs'] = $is_lbs;
                                            $GLOBALS['db']->autoExecute(DB_PREFIX."deal_delivery",$link_data);
                                    }
                            }
                        }else{
                            foreach($cache_deal_delivery as $k=>$deal_delivery_item)
                            {
                                    $deal_delivery_item['deal_id'] = $data['id'];
                                    $GLOBALS['db']->autoExecute(DB_PREFIX."deal_delivery",$deal_delivery_item);
                            }
                        }
                        
                }
                //关于运费


                //关于定义的禁用支付
                $GLOBALS['db']->query("delete from ".DB_PREFIX."deal_payment where deal_id = '".$data['id']."'");
                if(intval($param_data['define_payment'])==1)
                {
                        $payment_ids = $param_data['payment_id'];
                        foreach($payment_ids as $k=>$v)
                        {
                                $payment_conf = array();
                                $payment_conf['payment_id'] = intval($payment_ids[$k]);
                                $payment_conf['deal_id'] = $data['id'];
                                $GLOBALS['db']->autoExecute(DB_PREFIX."deal_payment",$payment_conf);
                        }
                }
                //定义的禁用支付

                //开始创建筛选项
                $GLOBALS['db']->query("delete from ".DB_PREFIX."deal_filter where deal_id = '".$data['id']."'");
                $filter = $param_data['filter'];
                foreach($filter as $filter_group_id=>$filter_value)
                {
                        $filter_data = array();
                        $filter_data['filter'] = $filter_value;
                        $filter_data['filter_group_id'] = $filter_group_id;
                        $filter_data['deal_id'] = $data['id'];
                        $GLOBALS['db']->autoExecute(DB_PREFIX."deal_filter",$filter_data);

                        $filter_array = preg_split("/[ ,]/i",$filter_value);
                        foreach($filter_array as $filter_item)
                        {
                                if(trim($filter_item)!="")
                                {
                                        $filter_row = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."filter where filter_group_id = ".$filter_group_id." and name = '".$filter_item."'");
                                        if(!$filter_row)
                                        {
                                                $filter_row = array();
                                                $filter_row['name'] = $filter_item;
                                                $filter_row['filter_group_id'] = $filter_group_id;
                                                $GLOBALS['db']->autoExecute(DB_PREFIX."filter",$filter_row);
                                        }
                                }
                        }
                }
                //筛选选项


                //子分类
                $GLOBALS['db']->query("delete from ".DB_PREFIX."deal_cate_type_deal_link where deal_id = '".$data['id']."'");
                if($is_admin == 1){
                    foreach($param_data['deal_cate_type_id'] as $type_id)
                    {
                            $link_data = array();
                            $link_data['deal_cate_type_id'] = $type_id;
                            $link_data['deal_id'] = $data['id'];
                            $GLOBALS['db']->autoExecute(DB_PREFIX."deal_cate_type_deal_link",$link_data);
                    }
                }else{
                    foreach($cache_deal_cate_type_id as $type_id)
                    {
                            $link_data = array();
                            $link_data['deal_cate_type_id'] = $type_id;
                            $link_data['deal_id'] = $data['id'];
                            $GLOBALS['db']->autoExecute(DB_PREFIX."deal_cate_type_deal_link",$link_data);
                    }
                }
                
                //子分类


                //门店与geo
                $GLOBALS['db']->query("delete from ".DB_PREFIX."deal_location_link where deal_id = '".$data['id']."'");
                if($is_admin == 1){
                    foreach($param_data['location_id'] as $location_id)
                    {
                            $link_data = array();
                            $link_data['location_id'] = intval($location_id);
                            $link_data['deal_id'] = $data['id'];
                            $GLOBALS['db']->autoExecute(DB_PREFIX."deal_location_link",$link_data);
                    }
                }else{
                    foreach($cache_location_id as $location_id)
                    {
                            $link_data = array();
                            $link_data['location_id'] = intval($location_id);
                            $link_data['deal_id'] = $data['id'];
                            $GLOBALS['db']->autoExecute(DB_PREFIX."deal_location_link",$link_data);
                    }
                }

                //开始处理geo信息
                $GLOBALS['db']->query("delete from ".DB_PREFIX."deal_geo where deal_id = '".$data['id']."'");			
                if(count($param_data['location_id'])>0)
                {
                        foreach($param_data['location_id'] as $location_id)
                        {
                                $location_data = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."supplier_location where id = '".$location_id."'");
                                if($location_data)
                                {
                                        $deal_geo['scale_meter'] = intval($location_data['scale_meter']); //LBS范围（米）
                                        $deal_geo['xpoint'] = strim($location_data['xpoint']);
                                        $deal_geo['ypoint'] = strim($location_data['ypoint']);
                                        $deal_geo['deal_id'] = $data['id'];
                                        $deal_geo['location_id'] = $location_data['id'];
                                        $GLOBALS['db']->autoExecute(DB_PREFIX."deal_geo",$deal_geo);
                                }
                        }
                }
                else
                {
                        if($channel_item['business_type']==0&&count($param_data['location_id'])==0&&$data['buy_type']==0)
                        {
                                $deal_geo['scale_meter'] = intval($param_data['scale_meter']); //LBS范围（米）
                                $deal_geo['xpoint'] = strim($param_data['xpoint']);
                                $deal_geo['ypoint'] = strim($param_data['ypoint']);
                                $deal_geo['deal_id'] = $data['id'];
                                $GLOBALS['db']->autoExecute(DB_PREFIX."deal_geo",$deal_geo);
                        }
                }
                //门店geo

                //开始处理服务人员
                $GLOBALS['db']->query("delete from ".DB_PREFIX."deal_supplier_staff where deal_id = '".$data['id']."'");
                $current_staff_ids = array(0);
                if($is_admin == 1){
                    foreach($param_data['staff_id'] as $staff_id)
                    {
                            $link_data = array();
                            $link_data['deal_id'] = $data['id'];
                            $link_data['supplier_id'] = $data['supplier_id'];
                            $link_data['staff_id'] = intval($staff_id);
                            $current_staff_ids[] = intval($staff_id);
                            $GLOBALS['db']->autoExecute(DB_PREFIX."deal_supplier_staff",$link_data);
                    }
                }else{
                    foreach($cache_staff_list as $staff_item)
                    {
                            $staff_item['deal_id'] = $data['id'];
                            $staff_item['supplier_id'] = $data['supplier_id'];
                            $current_staff_ids[] = intval($staff_item['staff_id']);
                            $GLOBALS['db']->autoExecute(DB_PREFIX."deal_supplier_staff",$staff_item);
                    }
                }
                $GLOBALS['db']->query("delete from ".DB_PREFIX."deal_schedule_cfg where deal_id = '".$data['id']."' and staff_id not in (".implode(",",$current_staff_ids).")");
                $GLOBALS['db']->query("delete from ".DB_PREFIX."deal_schedule_order where deal_id = '".$data['id']."' and staff_id not in (".implode(",",$current_staff_ids).")");
                //服务人员


                //商圈处理
                $GLOBALS['db']->query("delete from ".DB_PREFIX."deal_area_link where deal_id = '".$data['id']."'");
                foreach($param_data['area_id'] as $area_id)
                    {
                            $link_data = array();
                            $link_data['deal_id'] = $data['id'];
                            $link_data['area_id'] = intval($area_id);
                            $GLOBALS['db']->autoExecute(DB_PREFIX."deal_area_link",$link_data);
                    }
                
                //end 商圈


                //多城市列表
                $GLOBALS['db']->query("delete from ".DB_PREFIX."deal_city_link where deal_id = '".$data['id']."'");
                foreach($param_data['city_ids'] as $city_id)
                {
                        $link_data = array();
                        $link_data['deal_id'] = $data['id'];
                        $link_data['city_id'] = intval($city_id);
                        $GLOBALS['db']->autoExecute(DB_PREFIX."deal_city_link",$link_data);
                }
                //end 多城市


                //同步排期表
                $schedule_ids = array(0);
                if($is_admin == 1){
                    foreach($param_data['schedule_id'] as $k=>$schedule_id)
                    {
                            $link_data = array();
                            $link_data['id'] = $schedule_id;
                            $link_data['deal_id'] = $data['id'];
                            $link_data['name'] = strim($param_data['schedule_name'][$k]);
                            $link_data['begin_time'] = str_pad(intval($param_data['begin_hour'][$k]), 2,"0",STR_PAD_LEFT).":".str_pad(intval($param_data['begin_minute'][$k]), 2,"0",STR_PAD_LEFT).":00";
                            $link_data['end_time'] = str_pad(intval($param_data['end_hour'][$k]), 2,"0",STR_PAD_LEFT).":".str_pad(intval($param_data['end_minute'][$k]), 2,"0",STR_PAD_LEFT).":00";
                            $link_data['sort'] = $k;
                            if($schedule_id)
                            {
                                    $GLOBALS['db']->autoExecute(DB_PREFIX."deal_schedule",$link_data,"UPDATE","id=".$schedule_id);
                                    $schedule_ids[] = $schedule_id;
                            }
                            else
                            {
                                    $GLOBALS['db']->autoExecute(DB_PREFIX."deal_schedule",$link_data);
                                    $schedule_ids[] = $GLOBALS['db']->insert_id();
                            }
                    }
                }else{
                    foreach($cache_deal_schedule as $k=>$deal_schedule_item)
                    {
                            $schedule_id = intval($deal_schedule_item['id']);
                            $deal_schedule_item['deal_id'] = $data['id'];
                            
                            if($schedule_id)
                            {
                                    $GLOBALS['db']->autoExecute(DB_PREFIX."deal_schedule",$deal_schedule_item,"UPDATE","id=".$schedule_id);
                                    $schedule_ids[] = $schedule_id;
                            }
                            else
                            {
                                    $GLOBALS['db']->autoExecute(DB_PREFIX."deal_schedule",$deal_schedule_item);
                                    $schedule_ids[] = $GLOBALS['db']->insert_id();
                            }
                    }
                }
                
                $GLOBALS['db']->query("delete from ".DB_PREFIX."deal_schedule where deal_id = '".$data['id']."' and id not in (".implode(",", $schedule_ids).")");
                $GLOBALS['db']->query("delete from ".DB_PREFIX."deal_schedule_cfg where deal_id = '".$data['id']."' and schedule_id not in (".implode(",", $schedule_ids).")");
                $GLOBALS['db']->query("delete from ".DB_PREFIX."deal_schedule_order where deal_id = '".$data['id']."' and schedule_id not in (".implode(",", $schedule_ids).")");
                //排期


                //增加商品关联购买
                $GLOBALS['db']->query("delete from ".DB_PREFIX."relate_goods where good_id = '".$data['id']."'");
                if($is_admin == 1){
                    if($param_data['relate_goods_id']){
                            $saveArray = array(
                                            'good_id'		=> $data['id'],
                                            'relate_ids'	=> implode(',', $param_data['relate_goods_id']),
                                            'is_shop'		=> 0,
                            );
                            $GLOBALS['db']->autoExecute(DB_PREFIX."relate_goods",$saveArray);
                    }
                }else{
                    if($cache_relate){
                            $saveArray = array(
                                            'good_id'		=> $data['id'],
                                            'relate_ids'	=> implode(',', $cache_relate),
                                            'is_shop'		=> 0,
                            );
                            $GLOBALS['db']->autoExecute(DB_PREFIX."relate_goods",$saveArray);
                    }
                }
                
                //关联购买


                //同步商品数据
                syn_deal_status($data['id']);
                syn_deal_match($data['id']);
                syn_attr_stock_key($data['id']);

                
                $result['status'] = 1;
                $result['info'] = "保存成功";
                return $result;
        }//end if err	
}

function format_deal_submit($data){
    $temp_data = array();
    $temp_data['name'] = $data['name'];
    $temp_data['sub_name'] = $data['sub_name'];
    $temp_data['supplier_id'] = $data['supplier_id'];
    $temp_data['img'] = $data['img'];
    $temp_data['description'] = $data['description'];
    $temp_data['begin_time'] = $data['begin_time'];
    $temp_data['end_time'] = $data['end_time'];
    $temp_data['max_bought'] = $data['max_bought'];
    $temp_data['user_min_bought'] = $data['user_min_bought'];
    $temp_data['user_max_bought'] = $data['user_max_bought'];
    $temp_data['origin_price'] = $data['origin_price'];
    $temp_data['is_effect'] = $data['is_effect'];
    $temp_data['allow_promote'] = $data['allow_promote'];
    $temp_data['return_money'] = $data['return_money'];
    $temp_data['return_score'] = $data['return_score'];
    $temp_data['brief'] = $data['brief'];
    $temp_data['sort'] = $data['sort'];
    $temp_data['is_referral'] = $data['is_referral'];
    $temp_data['icon'] = $data['icon'];
    $temp_data['define_payment'] = $data['define_payment'];
    $temp_data['seo_title'] = $data['seo_title'];
    $temp_data['seo_keyword'] = $data['seo_keyword'];
    $temp_data['seo_description'] = $data['seo_description'];
    $temp_data['uname'] = $data['uname'];
    $temp_data['cart_type'] = $data['cart_type'];
    $temp_data['is_recommend'] = $data['is_recommend'];
    $temp_data['balance_price'] = $data['balance_price'];
    $temp_data['deal_tag'] = $data['deal_tag'];
    $temp_data['update_time'] = $data['update_time'];
    $temp_data['publish_wait'] = $data['publish_wait'];
    $temp_data['multi_attr'] = $data['multi_attr'];
    $temp_data['is_lottery'] = $data['is_lottery'];
    $temp_data['is_delivery'] = $data['is_delivery'];
    $temp_data['is_refund'] = $data['is_refund'];
    $temp_data['coupon_begin_time'] = $data['coupon_begin_time'];
    $temp_data['coupon_end_time'] = $data['coupon_end_time'];
    $temp_data['current_price'] = $data['current_price'];
    $temp_data['expire_refund'] = $data['expire_refund'];
    $temp_data['any_refund'] = $data['any_refund'];
    $temp_data['update_time'] = NOW_TIME;
     
    if(intval($data['deal_id']) == 0)
        $temp_data['create_time'] = NOW_TIME;
     
    //图片数据
    $imgs = unserialize($data['cache_focus_imgs']);
    $img_data = array();
    $temp_other = array();
    foreach($imgs as $img_k=>$img_v)
    {
        if($img_v!='')
        {
            $img_data['img'] = $img_v;
            $img_data['sort'] = $img_k;
        }
        $temp_other['imgs'][] = $img_data;
    }
     
    //开始处理属性
    $temp_other['deal_attr'] = unserialize($data['cache_deal_attr']);
    $temp_other['attr_stock'] = unserialize($data['cache_attr_stock']);
     
    $temp_other['cache_relate'] = unserialize($data['cache_relate']);     
    
    if($data['is_shop'] == 1){
        $temp_data['is_delivery'] = $data['is_delivery'];
        $temp_data['weight'] = $data['weight'];
        $temp_data['weight_id'] = $data['weight_id'];
        $temp_data['buy_type'] = $data['buy_type'];
        $temp_data['free_delivery'] = $data['free_delivery'];
        $temp_data['shop_cate_id'] = $data['shop_cate_id'];
        $temp_data['brand_id'] = $data['brand_id'];
        $temp_data['is_refund'] = $data['is_refund'];
        $temp_data['is_shop'] = 1;
         
        $temp_data['deal_goods_type'] = $data['deal_goods_type'];
         
         
        //免运费
        $temp_other['cache_free_delivery'] = unserialize($data['cache_free_delivery']);
        //支付方式
        $temp_other['cache_deal_payment'] = unserialize($data['cache_deal_payment']);
        //快递
        $temp_other['cache_deal_delivery'] = unserialize($data['cache_deal_delivery']);
        //过滤
        $temp_other['cache_deal_filter'] = unserialize($data['cache_deal_filter']);
        //门店
        $temp_other['location_id'] = unserialize($data['cache_location_id']);
    }else{
        $temp_data['auto_order'] = $data['auto_order'];
        $temp_data['notes'] = $data['notes'];
        $temp_data['notice'] = $data['notice'];
        $temp_data['deal_goods_type'] = $data['deal_goods_type'];
        $temp_data['cate_id'] = $data['cate_id'];
        $temp_data['min_bought'] = $data['min_bought'];
        $temp_data['city_id'] = $data['city_id'];
        $temp_data['is_coupon'] = 1;
        $temp_data['buy_count'] = $data['buy_count'];
        $temp_data['deal_type'] = $data['deal_type'];
        $temp_data['coupon_time_type'] = intval($data['coupon_time_type']);
        $temp_data['coupon_day'] = intval($data['coupon_day']);
        $temp_data['discount'] = $data['discount'];
        $temp_data['forbid_sms'] = $data['forbid_sms'];
        $temp_data['cart_type'] = $data['cart_type'];
         
        //分类
        $temp_other['deal_cate_type_id'] = unserialize($data['cache_deal_cate_type_id']);
        //门店
        $temp_other['location_id'] = unserialize($data['cache_location_id']);
    }
     
     
    $act_type = 0; //0:新增，1更新
    
    if($data['deal_id']>0){
        $act_type = 1;
    }
    
    $result_data = array("data"=>$temp_data,"other_data"=>$temp_other,"act_type"=>$act_type,"deal_submit_id"=>$data['id'],"deal_id"=>$data['deal_id']);
    
    return $result_data;

}

function deal_auto_downline($id){
    $deal_submit_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_submit where id = ".$id);
    if($deal_submit_info && $deal_submit_info['biz_apply_status']==3){
        //更新商户表状态
        $GLOBALS['db']->autoExecute(DB_PREFIX."deal_submit",array("admin_check_status"=>1),"UPDATE","id=".$id);
        //更新团购数据表
        $GLOBALS['db']->autoExecute(DB_PREFIX."deal",array("is_effect"=>0),"UPDATE","id=".$deal_submit_info['deal_id']);
        return true;
    }else{
        return false;
    }
}

/**
 * 根据deal_ids获取列表信息(包括属性，库存)
 * 
 * return array(
 * 	'goodsList'	=>	array(),
 * 	'dealArray'	=>	array(
 * 						'id'=>array(
 * 							'name'=>'','origin_price'=>'','current_price'=>''
 * 						),
 * 					),
 * 	'attrArray'	=>	array(
 * 						'id'=>array(
 * 							'规格类型'=>array(
 * 								'规格id'=>array(),
 * 							),
 * 						),
 * 					),
 * 	'stockArray'	=>	array(
 * 						'id'=>array(
 * 							'规格类型_规格类型'=>array(),
 * 						),
 * 					),
 * 
 * )
*/

function getDetailedList($id,$param=array()){
	if( is_array($id) ){
		$id = implode(',', $id);
	}
	
	$dealArray  = array();
	$attrArray  = array();
	$stockArray = array();	//库存
	
	$goodsList = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."deal where id in (".$id.")");

	foreach( $goodsList as &$item ){
//		$item['deal_attr']  = array();
		$item['stock'] 		= array();
		//商品属性
		$itemAttr = $GLOBALS['db']->getAll("select ".DB_PREFIX."deal_attr.*,".DB_PREFIX."deal_attr.id as id_1,".DB_PREFIX."deal_attr.name as name_1,".DB_PREFIX."goods_type_attr.* from ".DB_PREFIX."deal_attr left join ".DB_PREFIX."goods_type_attr on ".DB_PREFIX."deal_attr.goods_type_attr_id=".DB_PREFIX."goods_type_attr.id where ".DB_PREFIX."deal_attr.deal_id=".$item['id']);
		
		$dealAttrTypeArr = array();
		foreach($itemAttr as $attrItem){
			if( !empty($dealAttrTypeArr[$attrItem['id']]) ){
				$dealAttrTypeArr[$attrItem['id']]['attr_list'][] = array(
					'id'	=>	$attrItem['id_1'],
					'name'	=>	$attrItem['name_1'],
					'price'	=>	$attrItem['price'],
					'is_checked'	=>	$attrItem['is_checked'],
				);
			}else{
				$dealAttrTypeArr[$attrItem['id']] = array(
					'id'	=>	$attrItem['id'],
					'name'	=>	$attrItem['name']
				);
				$dealAttrTypeArr[$attrItem['id']]['attr_list'][] = array(
					'id'	=>	$attrItem['id_1'],
					'name'	=>	$attrItem['name_1'],
					'price'	=>	$attrItem['price'],
					'is_checked'	=>	$attrItem['is_checked'],
				);
			}
			
			unset($attrItem['id']);
			$attrArray[$item['id']][$attrItem['name']][$attrItem['id_1']] = $attrItem;
		}
		
		$dealAttrTypeArr = array_values($dealAttrTypeArr);
		$item['deal_attr']  = $dealAttrTypeArr;
		
		//如果有规格，查询库存
		if( $itemAttr ){
			$itemStock = $GLOBALS['db']->getAll("select * from ".DB_PREFIX."attr_stock where deal_id=".$item['id']);
			foreach( $itemStock as $stockItem ){
				$stockItem['attr_cfg'] = unserialize($stockItem['attr_cfg']);
				$stockArray[$item['id']][$stockItem['attr_key']] = $stockItem;
				$item['stock'][$stockItem['attr_key']] = $stockItem;
			}
		}
	
		$dealArray[$item['id']] = array(
				'name' => $item['name'],
				'origin_price' 	=> $item['origin_price'],
				'current_price' => $item['current_price'],
				'min_bought' 	=> $item['min_bought'],
				'max_bought' 	=> $item['max_bought'],
		);
		
		if(empty($item['deal_attr'])){
			unset($item['deal_attr']);
		}
		
		if(empty($item['stock'])){
			unset($item['stock']);
		}
		
		if(function_exists("format_deal_item"))
		$item = format_deal_item($item);
	}
	return array(
				'goodsList'		=>	$goodsList,
				'dealArray'		=>	$dealArray,
				'attrArray'		=>	$attrArray,
				'stockArray'	=>	$stockArray,
			);
}

/**
 * 关键字搜索商品
 * $keyword 可能关键字是数组(分词后的数组)
*/
function search_deal_by_keyword($limit,$keyword,$order=''){
	if(!$keyword){return array();}
	
	$tname = 'd';
	$condition = ' '.$tname.'.is_effect = 1 and '.$tname.'.is_delete = 0 and ( 1<>1';
	
	$unicode_s = '';
	$def_order = '';
	foreach($keyword as $kItem){
		$condition .= ' or '.$tname.'.name like "%'.$kItem.'%" ';
		$def_order .= '(CASE WHEN '.$tname.'.name like "%'.$kItem.'%" THEN 1 ELSE 0 END)+';
		
		$unicode_s .= str_to_unicode_string($kItem).'+';
	}
	$def_order = substr($def_order,0,-1);
	$unicode_s = substr($unicode_s,0,-1);
	
	$condition .= ' or match('.$tname.'.name_match) against ("'.$unicode_s.'")';			//名称
	$condition .= ' or match('.$tname.'.deal_cate_match) against ("'.$unicode_s.'")';	//分类
	$condition .= ' or match('.$tname.'.locate_match) against ("'.$unicode_s.'")';		//地区
	$condition .= ' or match('.$tname.'.tag_match) against ("'.$unicode_s.'")';			//标签
	//$condition .= ' or match('.$tname.'.city_match) against ("'.$unicode_s.'")';			//城市
	$condition .= ')';
	
	if($order==''){
		$orderby = ' order by ('.$def_order.') desc limit '.$limit;
	}else{
		if( $order=='add_time' ){
			$orderby = ' order by '.$tname.'.update_time desc limit '.$limit;
		}else if( $order=='price' ){
			$orderby = ' order by '.$tname.'.current_price desc limit '.$limit;
		}else if( $order=='sale' ){
			$orderby = ' order by '.$tname.'.buy_count desc limit '.$limit;
		}
	}
	
	$sql_count = "select count(*) from ".DB_PREFIX."deal as ".$tname." where  ".$condition;
	$sql_list  = "select ".$tname.".* from ".DB_PREFIX."deal as ".$tname." where  ".$condition.$orderby ;
//trace($sql_list);
	$data_total = $GLOBALS['db']->getOne($sql_count,false);
	$list  = $GLOBALS['db']->getAll($sql_list,false);
	return array(
				'data_total'	=>	$data_total,
				'list'	=>	$list
			);
}







?>