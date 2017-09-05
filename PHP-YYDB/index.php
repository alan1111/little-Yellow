<?php 
// +----------------------------------------------------------------------
// | 谷创电子商务商城系统
// +----------------------------------------------------------------------
// | Copyright (c) 2016 http://www.nthxmai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Ant（594177881@qq.com）
// +----------------------------------------------------------------------
header('content-type:text/html;charset=utf-8');
define("FILE_PATH",""); //文件目录，空为根目录
require_once './system/system_init.php';

require_once APP_ROOT_PATH.'app/Lib/'.APP_TYPE.'/core/MainApp.class.php';
//实例化一个网站应用实例
$AppWeb = new MainApp();
?>