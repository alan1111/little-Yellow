<?php
// +----------------------------------------------------------------------
// | nthxmai.com 谷创商城夺宝
// +----------------------------------------------------------------------
// | Copyright (c) 2016 http://www.nthxmai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Ant（594177881@qq.com）
// +----------------------------------------------------------------------

class helpsModule extends MainBaseModule
{

	public function index()
	{
		global_run();
		init_app_page();
		$cache_id  = md5(MODULE_NAME.ACTION_NAME);		
		if (!$GLOBALS['tmpl']->is_cached('helps.html', $cache_id)){
			$data = call_api_core("helps","index");
			foreach($data['list'] as $k=>$v){
				foreach($data['list'][$k]['article_list'] as $kk=>$vv){
					$data['list'][$k]['article_list'][$kk]['url']=wap_url("index","helps#show",array("data_id"=>$vv['id']));
				}
					
			}
			
			$GLOBALS['tmpl']->assign("data", $data);
		
		}
		
		
		$GLOBALS['tmpl']->display("helps.html",$cache_id);
	}

  public function show(){
        global_run();
		init_app_page();
		$param['id'] = intval($_REQUEST['data_id']);
		$data = call_api_core("helps","show", $param);

		$GLOBALS['tmpl']->assign("data", $data);
		$GLOBALS['tmpl']->display("article_show.html");
  }
}
?>
