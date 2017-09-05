<?php
// +----------------------------------------------------------------------
// | nthxmai.com 谷创商城夺宝
// +----------------------------------------------------------------------
// | Copyright (c) 2016 http://www.nthxmai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Ant（594177881@qq.com）
// +----------------------------------------------------------------------

class cateModule extends MainBaseModule
{

	public function index()
	{
		global_run();
		init_app_page();

		$data_id = intval($_REQUEST['data_id']);
		
		$data = call_api_core("cate","index",array("data_id"=>$data_id));

		$GLOBALS['tmpl']->assign("data",$data);
		$GLOBALS['tmpl']->display("cate.html");
	}


}
?>
