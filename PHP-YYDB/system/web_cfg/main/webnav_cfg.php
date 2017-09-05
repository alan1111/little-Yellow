<?php
// +----------------------------------------------------------------------
// | Fanwe 谷创商城系统
// +----------------------------------------------------------------------
// | Copyright (c) 2016 http://www.nthxmai.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Ant（594177881@qq.com）
// 前端可配置的导航菜单
// +----------------------------------------------------------------------

return array(
			'index' => array(
				'app_index'	=>	'index',
				'name'	=>	'首页',  //首页
			),
			'duobaost'	=>	array(
				'app_index'	=>	'index',
				'name'	=>	'10元区',
			),
			'duobaosh'	=>	array(
					'app_index'	=>	'index',
					'name'	=>	'百元区',
			),
			'anno'=>array(
					'app_index'=>'index',
					'name'=>'最新揭晓',
			),
			'duobaos'	=>	array(
					'app_index'	=>	'index',
					'name'	=>	'分类列表',
			),
			'share'  =>  array(
			    'app_index'=>'index',
			    'name'=> '晒单分享',
			),				
			'help' => array(
					'app_index'	=>	'index',
					'name'	=>	'帮助' 
			),
			'news' => array(
					'app_index'	=>	'index',
					'name'	=>	'公告列表'
			),

			'user' => array(
				'app_index'	=>	'index',
				'name'	=>	'会员',
				'acts'	=> array(
						'login'	=>	'登录',
						'register'	=>	'注册',
				),
			)	
    
		);
?>