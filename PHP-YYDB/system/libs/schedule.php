<?php
// +----------------------------------------------------------------------
// | nthxmai.com 谷创商城夺宝
// +----------------------------------------------------------------------
// | Copyright (c) 2016 http://www.nthxmai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Ant（594177881@qq.com）
// +----------------------------------------------------------------------

//计划任务执行接口
interface schedule {

	/**
	 * 执行指定的计划任务,
	 * 返回   返回 array("status"=>0/1, "attemp"=>0/1,  "info"=>string);
	 * @param unknown_type $data
	 */
	function exec($data);

	
	
	
}
?>