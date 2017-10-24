<?php 
// +----------------------------------------------------------------------
// | nthxmai.com 谷创商城夺宝
// +----------------------------------------------------------------------
// | Copyright (c) 2016 http://www.nthxmai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Ant（594177881@qq.com）
// +----------------------------------------------------------------------\
define("FILE_PATH",""); //文件目录，空为根目录
echo "correct";
require_once './system/system_init.php';
$sess_id = strim($_REQUEST['sess_id']);
if($sess_id)
{
	es_session::set_sessid($sess_id);
}
es_session::start();
require_once APP_ROOT_PATH."system/utils/es_image.php";
es_image::buildImageVerify(4,1);
?>