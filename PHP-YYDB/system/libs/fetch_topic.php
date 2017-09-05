<?php
// +----------------------------------------------------------------------
// | nthxmai.com 谷创商城夺宝
// +----------------------------------------------------------------------
// | Copyright (c) 2016 http://www.nthxmai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Ant（594177881@qq.com）
// +----------------------------------------------------------------------


interface fetch_topic{
	//采集分享内容的接口
	
	//通过url解析并生成相应的序列化内容
	// 返回: "status"=>"","info"=>"","group_data"=>"","content"=>"","tags"=>"","images"=>array("id"=>"","url"=>"")
	function fetch($url_str);
	
	
	//解析topic内容，还原序列化的data, 重新生成topic通用结构
	function decode($topic); 
	
	
	//解析成同步微博发布的内容
	function decode_weibo($topic);
	
	
	//解析成手机端输出的接口数据
	function decode_mobile($topic);

	
}
?>