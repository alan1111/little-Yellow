<?php
/**
 * 定期处理的杂项事务计划任务
 */
require_once(APP_ROOT_PATH.'system/libs/schedule.php');
class gc_schedule implements schedule {
	
	/**
	 * $data 格式
	 * array();
	 */
	public function exec($data){
				
		$path = APP_ROOT_PATH."public/lottery_data_dir/";
		if ( $dir = opendir( $path ) )
		{
			while ( $file = readdir( $dir ) )
			{				
				if($file!='.'&&$file!='..')
				{
					preg_match("/\d+/", $file,$matches);
					$duobao_item_id = intval($matches[0]);
					if($duobao_item_id>0)
					{
						require_once APP_ROOT_PATH."system/model/duobao.php";
						duobao::init_robot($duobao_item_id);
						duobao::create_lottery_pool($duobao_item_id);
					}
					break;
				}				
			}
			closedir($dir);
		}
		
		
		$GLOBALS['db']->query("delete from ".DB_PREFIX."schedule_list where (exec_status = 2 and exec_end_time < ".(NOW_TIME-24*3600).") or ((type='robot' or type = 'robot_cfg') and exec_status = 2 and exec_end_time < ".(NOW_TIME-3600).")");  //清空1天前的计划任务 或 清空关于机器人下单的海量记录1小时
		
		require_once APP_ROOT_PATH."system/model/deal_order.php";
		//清空过期的购物清单
		$GLOBALS['db']->query("delete from ".DB_PREFIX."deal_cart where update_time < ".(NOW_TIME-1200));
	
		//删除2天前的开奖彩集
		$sql = "delete from ".DB_PREFIX."fair_fetch where updatetime < ".(NOW_TIME-24*3600*2);
		$GLOBALS['db']->query($sql);
	
		//关闭未付款的定单(20分钟)
		$order_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."deal_order where pay_status = 0 and update_time < ".(NOW_TIME-1200)." order by update_time asc limit 1");
		if($order_info)
		{
			cancel_order($order_info['id']);
			del_order($order_info['id']);
		}
	
	
		//7天未完善配送地址的订单取消  //by hc4.18
	
		$sql = "select * from ".DB_PREFIX."deal_order where type = 0 and region_info = '' and create_date_ymd  < '".to_date(NOW_TIME-24*3600*7,"Y-m-d")."' order by create_date_ymd asc limit 1";
		//$sql = "select * from ".DB_PREFIX."deal_order where type = 0 and region_info = '' and update_time < ".(NOW_TIME-24*3600*7)." order by update_time asc  limit 1";
		$order_info = $GLOBALS['db']->getRow($sql);
		if($order_info)
		{
			over_order($order_info['id']);
			del_order($order_info['id']);
		}
	
		//7天未收货的自动收货
		$sql = "select * from ".DB_PREFIX."deal_order_item where delivery_status = 1 and is_arrival = 0 and create_date_ymd < '".to_date(NOW_TIME-24*3600*7,"Y-m-d")."' order by create_date_ymd asc limit 1";
		//$sql = "select doi.* from ".DB_PREFIX."deal_order as do left join ".DB_PREFIX."deal_order_item as doi on doi.order_id = do.id where doi.delivery_status = 1 and doi.is_arrival = 0 and do.update_time < ".(NOW_TIME-24*3600*7)." order by do.update_time asc limit 1";
		$order_item = $GLOBALS['db']->getRow($sql);
		if($order_item)
		{
			$delivery_notice = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."delivery_notice where order_item_id = ".$order_item['id']." and is_arrival = 0 order by delivery_time desc limit 1");
			if($delivery_notice)
			{
				confirm_delivery($delivery_notice['notice_sn'],$order_item['id']);
			}
		}
		
		$del_time = es_session::get("del_time");
		if(empty($del_time)){
		    $del_time = NOW_TIME-3600;
		    es_session::set("del_time",NOW_TIME);
		}

        if(NOW_TIME-$del_time>=3600){
            //定期清理，事务表
            $from_del_time = to_date((NOW_TIME-3600),"Y-m-d-H");
            $sql = "delete from ".DB_PREFIX."form_verify where update_time='".$from_del_time."'";

            $GLOBALS['db']->query($sql);
            es_session::set("del_time",NOW_TIME);
        }
		
		
		
		
		send_schedule_plan("gc", "定时任务", array(), NOW_TIME);
		
		$result['status'] = 1;
		$result['attemp'] = 0;
		$result['info'] = "处理成功";
		return $result;
		
		
				
	}	
}



?>