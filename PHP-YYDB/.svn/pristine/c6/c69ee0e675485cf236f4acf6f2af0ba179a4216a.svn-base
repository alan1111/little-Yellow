<?php
// +----------------------------------------------------------------------
// | nthxmai.com 谷创商城夺宝
// +----------------------------------------------------------------------
// | Copyright (c) 2016 http://www.nthxmai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Ant（594177881@qq.com）
// +----------------------------------------------------------------------

/**
 * 搜索主页
 * @author jobin.lin
 *
 */
class searchModule extends MainBaseModule
{
	public function index()
	{
		global_run();
		
		init_app_page();
		$data['page_title'] = '搜索';
		
		$ajax		 	= intval($_REQUEST['ajax']);
		$search_type 	= intval($_REQUEST['search_type']);
		$search_keyword = $_POST['keyword']?strim($_POST['keyword']):urldecode(strim($_GET['keyword']));
		$orderby		= strim($_REQUEST['orderby']);	//排序规则
		$page 			= intval($_REQUEST['p']);
		$page			= $page>0?$page:1;

		
		$GLOBALS['tmpl']->assign("hot_kw",$data['hot_kw']);
		$GLOBALS['tmpl']->assign("data",$data);
		$GLOBALS['tmpl']->display("search_index.html");
	}
	
	public function do_search(){
	   
	    $keyword = strim($_REQUEST['keyword']);
	    

	    app_redirect(wap_url("index","duobaos#index",array("keyword"=>$keyword)));
	}
}
?>