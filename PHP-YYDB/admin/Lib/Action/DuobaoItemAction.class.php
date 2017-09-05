<?php
// +----------------------------------------------------------------------
// | nthxmai.com 谷创商城夺宝
// +----------------------------------------------------------------------
// | Copyright (c) 2016 http://www.nthxmai.com All rights reserved.
// +----------------------------------------------------------------------

class DuobaoItemAction extends CommonEnhanceAction{
    public function index(){
    	
    	if(!isset($_REQUEST['is_success']))$_REQUEST['is_success'] = -1;
    	if(!isset($_REQUEST['has_lottery']))$_REQUEST['has_lottery'] = -1;
    	if(!isset($_REQUEST['prepare_lottery']))$_REQUEST['prepare_lottery'] = -1;
    	
        //列表过滤器，生成查询Map对象
        $model = D ('DuobaoItemView');
        $map = $this->_search ($model);
       
        $is_success = intval($_REQUEST['is_success']);
        if($is_success==0)
        {
        	$map['success_time'] = 0;
        }
        elseif($is_success==1)
        {
        	$map['progress'] = 100;
        }
        
        $has_lottery = intval($_REQUEST['has_lottery']);
        if($has_lottery==-1)
        {
        	unset($map['has_lottery']);
        }
        
        $prepare_lottery = intval($_REQUEST['prepare_lottery']);
        if($prepare_lottery==0)
        {
        	$map['lottery_time'] = 0;
        }
        elseif($prepare_lottery==1)
        {
        	$map['lottery_time'] = array("gt",0);
        }
        
        foreach ($map as $key=>$value){
            if(stripos($key, 'create_time')){
                $k = str_replace('create_time', 'lottery_time', $key);
                $map[$k] = $value;
                unset($map[$key]);
            }
            
        }
        $this->_list( $model, $map );
        $this->display ();
    }
    
    public function foreverdelete(){
		set_time_limit(0);
        $id = $_REQUEST ['id'];
        $force = intval($_REQUEST['force']);
        $DuobaoItem_model = M('DuobaoItem');
		$map['id']  = array('in',$id);
        $item_result = $DuobaoItem_model->where($map)->select();
		foreach($item_result as $key=>$value){
			if ($value['has_lottery'] !=1&&$value['current_buy']>0&&$force==0) {
				$this->error('未开奖的夺宝活动，不能删除');
			}
		}
       foreach($item_result as $key=>$value){
		   require_once APP_ROOT_PATH."system/model/duobao.php";
		   $duobao = new duobao($value['id']);
		   $duobao->del_duobao();
			
		   save_log($duobao->duobao_item['name'].$duobao->duobao_item['id']."期".l("DELETE_SUCCESS"),1);
	   }
       $this->success (l("DELETE_SUCCESS"),1);
    }
    
    
    public function prepare_lottery()
    {
    	set_time_limit(0);
    	$id = intval($_REQUEST['id']);
    	require_once APP_ROOT_PATH."system/model/duobao.php";
    	$duobao = new duobao($id);
    	if($duobao->duobao_item['current_buy']<$duobao->duobao_item['max_buy'])
    	{
    		//凑单
    		require_once APP_ROOT_PATH."system/model/robot.php";
    		$result = robot::set_robot_schedule(5, $id);
    		if($result['status']==1)
    		{
    			$GLOBALS['db']->query("update ".DB_PREFIX."duobao_item set robot_is_db = 1,robot_end_time=5 where id = ".$id);
    			$this->success($result['info'],1);
    		}
    		else
    		{
    			$this->error($result['info'],1);
    		}
    	}   	
    }
    
    public function prepare_lottery_1()
    {
    	set_time_limit(0);
    	$id = intval($_REQUEST['id']);
    	require_once APP_ROOT_PATH."system/model/duobao.php";
    	$duobao = new duobao($id);
    	if($duobao->duobao_item['current_buy']<$duobao->duobao_item['max_buy'])
    	{
    		//凑单
    		require_once APP_ROOT_PATH."system/model/robot.php";
    		
    		$duobao_plan = M("Duobao")->getById($duobao->duobao_item['duobao_id']);
    		$result = robot::set_robot_schedule_by_cfg(
    				array(
    						"robot_buy_min_time"=>$duobao_plan['robot_buy_min_time'],
    						"robot_buy_max_time"=>$duobao_plan['robot_buy_max_time'],
    						"robot_buy_min"=>$duobao_plan['robot_buy_min'],
    						"robot_buy_max"=>$duobao_plan['robot_buy_max']
    				)
    				, $id);
    		if($result['status']==1)
    		{
    			//$GLOBALS['db']->query("update ".DB_PREFIX."duobao_item set robot_is_db = 1,robot_end_time=5 where id = ".$id);
    			$this->success($result['info'],1);
    		}
    		else
    		{
    			$this->error($result['info'],1);
    		}
    	}
    }
    
    public function set_sort()
    {
        $id = intval($_REQUEST['id']);
        $sort = intval($_REQUEST['sort']);
    
        $log_info = M(MODULE_NAME)->where("id=".$id)->getField("name");
        if(!check_sort($sort))
        {
            $this->error(l("SORT_FAILED"),1);
        }
        M(MODULE_NAME)->where("id=".$id)->setField("sort",$sort);
        save_log($log_info.l("SORT_SUCCESS"),1);
    
        $this->success(l("SORT_SUCCESS"),1);
    }
    
    public function draw_lottery()
    {
    	$id = intval($_REQUEST['id']);
    	$lottery_sn = intval($_REQUEST['lottery_sn']);
    	$lottery_sn = 100000000 + $lottery_sn;
    	require_once APP_ROOT_PATH."system/model/duobao.php";
    	$duobao_item = new duobao($id);
    	if($duobao_item->duobao_item['current_buy']<$duobao_item->duobao_item['max_buy'])
    	{
    		$this->error('人需未满，无法开奖',1);
    	}

    	if($duobao_item->duobao_item['fair_type']=="yydb")
    	{
    		$duobao_item->draw_lottery_yydb($lottery_sn);
    	}
    	else
    	{
    		//人工开奖
    		$fair_type = $duobao_item->duobao_item['fair_type'];
    		$cname = $fair_type."_fair_fetch";
    		 
    		$sql = "select * from ".DB_PREFIX."fair_fetch where fair_type = '".$duobao_item->duobao_item['fair_type']."' and period = '".$duobao_item->duobao_item['fair_period']."'";
    		$fair_period = $GLOBALS['db']->getRow($sql);
    		if($fair_period['number'])
    		{
    			//当前期已开奖
    			$duobao_item->draw_lottery($fair_period['period'], $fair_period['number']);
    		}
    		else
    		{
    			if($duobao_item->duobao_item['fair_period']=="000000")//未指定
    			{
    				//采集最新的开奖
    				require_once APP_ROOT_PATH."system/fair_fetch/".$cname.".php";
    				$fetch_obj = new $cname;
    				$fetch_obj->createData();
    				$fetch_infos = $fetch_obj->collectData();  //开奖并获取开奖的信息
    				if($fetch_infos)
    					$fair_period = $fetch_infos[count($fetch_infos)-1];
    		
    				if($fair_period&&$fair_period['number'])
    				{
    					$duobao_item->draw_lottery($fair_period['period'], $fair_period['number']);
    				}
    				else
    				{
    					$duobao_item->draw_lottery($duobao_item->duobao_item['fair_period'], DEFAULT_LOTTERY);
    				}
    			}
    			else
    			{
    				$duobao_item->draw_lottery($duobao_item->duobao_item['fair_period'], DEFAULT_LOTTERY);
    			}
    		}
    	}
    	
    	
    	$this->success('开奖成功',1);   	
    	
    }
   
}