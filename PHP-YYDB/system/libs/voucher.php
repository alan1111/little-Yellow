<?php
// +----------------------------------------------------------------------
// | nthxmai.com 谷创商城夺宝
// +----------------------------------------------------------------------
// | Copyright (c) 2016 http://www.nthxmai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Ant（594177881@qq.com）
// +----------------------------------------------------------------------

//关于代金券的全局函数
/**
 * 代金券发放
 * @param $ecv_type_id 代金券类型ID
 * @param $user_id  发放给的会员。0为线下模式的发放
 */
function send_voucher($ecv_type_id,$user_id=0,$is_password=false)
{
	$GLOBALS['db']->query("update ".DB_PREFIX."ecv_type set gen_count = gen_count + 1 where id = ".$ecv_type_id." and (total_limit = 0 or gen_count + 1 <= total_limit)");
	if(!$GLOBALS['db']->affected_rows())
	{
		return -1;
	}
	
	$ecv_type = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."ecv_type where id = ".$ecv_type_id);
	if(!$ecv_type)return false;
	if($is_password)$ecv_data['password'] = rand(10000000,99999999);
	$ecv_data['use_limit'] = $ecv_type['use_limit'];
	$ecv_data['begin_time'] = $ecv_type['begin_time'];
	$ecv_data['end_time'] = $ecv_type['end_time'];
	$ecv_data['money'] = $ecv_type['money'];
	$ecv_data['ecv_type_id'] = $ecv_type_id;
	$ecv_data['user_id'] = $user_id;	

	do{
		$sn = unpack('H12',str_shuffle(md5(uniqid())));
		$sn = $sn[1];
		$ecv_data['sn'] = $sn;
		//$ecv_data['sn'] = md5(NOW_TIME);
		$GLOBALS['db']->autoExecute(DB_PREFIX."ecv",$ecv_data,'INSERT','','SILENT');
		$insert_id = $GLOBALS['db']->insert_id();
	}while(intval($insert_id) == 0);
	if(!$insert_id)
	{
		$GLOBALS['db']->query("update ".DB_PREFIX."ecv_type set gen_count = gen_count - 1 where id = ".$ecv_type_id);		
	}
	return $insert_id;
}

?>