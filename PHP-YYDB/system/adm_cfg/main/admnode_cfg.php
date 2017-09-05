<?php

return array(
    "Admin" => array(
        "name" => "管理员",
        "node" => array(
            "index" => array("name" => "管理员列表", "action" => "index"),
            "insert" => array("name" => "添加", "action" => "insert"),
            "update" => array("name" => "编辑", "action" => "update"),
            "foreverdelete" => array("name" => "删除", "action" => "foreverdelete"),
            "set_default" => array("name" => "设置默认管理员", "action" => "set_default"),
            "set_effect" => array("name" => "设置生效", "action" => "set_effect"),
        )
    ),
    "Role" => array(
        "name" => "角色管理",
        "node" => array(
            "index" => array("name" => "管理员分组列表", "action" => "index"),
            "insert" => array("name" => "添加执行", "action" => "insert"),
            "update" => array("name" => "编辑执行", "action" => "update"),
            "foreverdelete" => array("name" => "删除", "action" => "foreverdelete"),
        )
    ),
		"deal" => array(
				"name" => "商品管理",
				"node" => array(
						"index" => array("name" => "商品列表", "action" => "index"),
						"toogle_status" => array("name" => "状态修改", "action" => "toogle_status"),
						"edit" => array("name" => "编辑", "action" => "edit"),
						"add" => array("name" => "添加", "action" => "add"),
						"insert" => array("name" => "添加提交", "action" => "insert"),
						"update" => array("name" => "编辑提交", "action" => "update"),
						"foreverdelete" => array("name" => "永久删除", "action" => "foreverdelete"),
				)
		),
		"DealCate" => array(
				"name" => "分类管理",
				"node" => array(
						"index" => array("name" => "分类列表", "action" => "index"),
						"set_effect" => array("name" => "设置生效", "action" => "set_effect"),
						"edit" => array("name" => "编辑", "action" => "edit"),
						"add" => array("name" => "添加", "action" => "add"),
						"insert" => array("name" => "添加提交", "action" => "insert"),
						"update" => array("name" => "编辑提交", "action" => "update"),
						"delete" => array("name" => "删除", "action" => "delete"),
				)
		),
		"Brand" => array(
				"name" => "品牌管理",
				"node" => array(
						"index" => array("name" => "品牌列表", "action" => "index"),
						"edit" => array("name" => "编辑", "action" => "edit"),
						"add" => array("name" => "添加", "action" => "add"),
						"insert" => array("name" => "添加提交", "action" => "insert"),
						"update" => array("name" => "编辑提交", "action" => "update"),
						"foreverdelete" => array("name" => "永久删除", "action" => "foreverdelete"),
				)
		),
    "Duobao" => array(
        "name" => "夺宝计划",
        "node" => array(
            "index" => array("name" => "夺宝计划", "action" => "index"),
            "toogle_status" => array("name" => "状态修改", "action" => "toogle_status"),
            "edit" => array("name" => "编辑", "action" => "edit"),
            "add" => array("name" => "添加", "action" => "add"),
            "insert" => array("name" => "添加提交", "action" => "insert"),
            "update" => array("name" => "编辑提交", "action" => "update"),
            "foreverdelete" => array("name" => "永久删除", "action" => "foreverdelete"),
        )
    ),
    "DuobaoItem" => array(
        "name" => "夺宝活动",
        "node" => array(
            "index" => array("name" => "夺宝计划", "action" => "index"),
            "prepare_lottery" => array("name" => "机器人凑单", "action" => "prepare_lottery"),
            "draw_lottery" => array("name" => "人工开奖", "action" => "draw_lottery"),
            "foreverdelete" => array("name" => "永久删除", "action" => "foreverdelete"),
        )
    ),
    "DuobaoItemHistory" => array(
        "name" => "往期夺宝",
        "node" => array(
            "index" => array("name" => "往期夺宝列表", "action" => "index"),
        )
    ),
    "DuobaoOrder" => array(
        "name" => "夺宝订单",
        "node" => array(
            "index" => array("name" => "夺宝订单列表", "action" => "index"),
            "view_order" => array("name" => "查看详情", "action" => "view_order"),
            "trash" => array("name" => "往期夺宝订单", "action" => "trash"),
            "view_order_history" => array("name" => "往期夺宝订单详情", "action" => "view_order_history"),
            "foreverdelete" => array("name" => "永久删除", "action" => "foreverdelete"),
        )
    ),
    "DealOrder" => array(
        "name" => "中奖订单",
        "node" => array(
            "index" => array("name" => "中奖订单列表", "action" => "index"),
            "view_order" => array("name" => "查看详情", "action" => "view_order"),
            "trash" => array("name" => "往期中奖订单", "action" => "trash"),
            "view_order_history" => array("name" => "往期中奖订单详情", "action" => "view_order_history"),
            "foreverdelete" => array("name" => "永久删除", "action" => "foreverdelete"),
        )
    ),
    "InchargeOrder" => array(
        "name" => "充值订单",
        "node" => array(
            "index" => array("name" => "充值订单列表", "action" => "index"),
            "trash" => array("name" => "往期充值订单", "action" => "trash"),
            "foreverdelete" => array("name" => "永久删除", "action" => "foreverdelete"),
        )
    ),
    "PaymentNotice" => array(
        "name" => "收款单",
        "node" => array(
            "index" => array("name" => "收款单列表", "action" => "index"),
        )
    ),
    "User" => array(
        "name" => "会员",
        "node" => array(
            "account_detail" => array("name" => "帐户详情", "action" => "account_detail"),
            "delete" => array("name" => "删除", "action" => "delete"),
            "edit" => array("name" => "编辑页面", "action" => "edit"),
            "export_csv" => array("name" => "导出csv", "action" => "export_csv"),
            "foreverdelete" => array("name" => "永久删除", "action" => "foreverdelete"),
            "foreverdelete_account_detail" => array("name" => "永久删除帐户详情", "action" => "foreverdelete_account_detail"),
            "index" => array("name" => "会员列表", "action" => "index"),
            "insert" => array("name" => "添加执行", "action" => "insert"),
            "modify_account" => array("name" => "更新账户金额，积分，经验", "action" => "modify_account"),
            "restore" => array("name" => "恢复", "action" => "restore"),
            "set_effect" => array("name" => "设置生效", "action" => "set_effect"),
            "trash" => array("name" => "会员回收站", "action" => "trash"),
            "update" => array("name" => "编辑执行", "action" => "update"),
            "withdrawal_index" => array("name" => "会员提现列表", "action" => "withdrawal_index"),
            "withdrawal_edit" => array("name" => "会员提现弹出框", "action" => "withdrawal_edit"),
            "do_withdrawal" => array("name" => "会员提现审核", "action" => "do_withdrawal"),
            "del_withdrawal" => array("name" => "会员提现记录删除", "action" => "del_withdrawal"),
        )
    ),
    "UserGroup" => array(
        "name" => "会员组别",
        "node" => array(
            "index" => array("name" => "会员组别列表", "action" => "index"),
            "insert" => array("name" => "添加执行", "action" => "insert"),
            "update" => array("name" => "编辑执行", "action" => "update"),
            "foreverdelete" => array("name" => "永久删除", "action" => "foreverdelete"),
        )
    ),
    "UserLevel" => array(
        "name" => "会员等级",
        "node" => array(
            "index" => array("name" => "会员等级列表", "action" => "index"),
            "insert" => array("name" => "添加提交", "action" => "insert"),
            "update" => array("name" => "编辑提交", "action" => "update"),
            "foreverdelete" => array("name" => "永久删除", "action" => "foreverdelete"),
        )
    ),
    "Referrals" => array(
        "name" => "邀请返利",
        "node" => array(
            "foreverdelete" => array("name" => "永久删除", "action" => "foreverdelete"),
            "index" => array("name" => "邀请返利列表", "action" => "index"),
            "pay" => array("name" => "发放返利", "action" => "pay"),
        )
    ),
    "Conf" => array(
        "name" => "系统配置",
        "node" => array(
            "index" => array("name" => "系统配置", "action" => "index"),
            "update" => array("name" => "更新配置", "action" => "update"),
            "mobile" => array("name" => "手机端配置", "action" => "mobile"),
            "savemobile" => array("name" => "保存手机端配置", "action" => "savemobile"),
            "news" => array("name" => "手机端公告", "action" => "news"),
            "insertnews" => array("name" => "添加手机端公告", "action" => "insertnews"),
            "updatenews" => array("name" => "编辑手机端公告", "action" => "updatenews"),
            "foreverdelete" => array("name" => "删除公告", "action" => "foreverdelete"),
        )
    ),
    "Database" => array(
        "name" => "数据库",
        "node" => array(
            "delete" => array("name" => "删除备份", "action" => "delete"),
            "dump" => array("name" => "备份数据", "action" => "dump"),
            "execute" => array("name" => "执行SQL语句", "action" => "execute"),
            "index" => array("name" => "数据库备份列表", "action" => "index"),
            "restore" => array("name" => "恢复备份", "action" => "restore"),
            "sql" => array("name" => "SQL操作", "action" => "sql"),
        )
    ),
    "Log" => array(
        "name" => "系统日志",
        "node" => array(
            "index" => array("name" => "系统日志列表", "action" => "index"),
            "coupon" => array("name" => "第三方验证日志", "action" => "coupon"),
            "foreverdelete" => array("name" => "永久删除", "action" => "foreverdelete"),
            "foreverdeletelog" => array("name" => "永久删除第三方验证日志", "action" => "foreverdeletelog"),
        )
    ),
    "ApiLogin" => array(
        "name" => "API登录",
        "node" => array(
            "index" => array("name" => "API插件列表", "action" => "index"),
            "insert" => array("name" => "API插件安装", "action" => "insert"),
            "update" => array("name" => "API插件编辑", "action" => "update"),
            "uninstall" => array("name" => "API插件卸载", "action" => "uninstall"),
        )
    ),
    "Integrate" => array(
        "name" => "会员整合",
        "node" => array(
            "index" => array("name" => "会员整合插件", "action" => "index"),
            "install" => array("name" => "安装页面", "action" => "install"),
            "save" => array("name" => "保存", "action" => "save"),
            "uninstall" => array("name" => "卸载", "action" => "uninstall"),
        )
    ),
    "MIndex" => array(
        "name" => "手机端首页菜单",
        "node" => array(
            "index" => array("name" => "首页菜单列表", "action" => "index"),
            "insert" => array("name" => "添加提交", "action" => "insert"),
            "update" => array("name" => "编辑提交", "action" => "update"),
            "foreverdelete" => array("name" => "删除菜单", "action" => "foreverdelete"),
        )
    ),
    "Nav" => array(
        "name" => "导航菜单",
        "node" => array(
            "index" => array("name" => "导航菜单列表", "action" => "index"),
            "insert" => array("name" => "添加执行", "action" => "insert"),
            "update" => array("name" => "编辑执行", "action" => "update"),
            "set_effect" => array("name" => "设置生效", "action" => "set_effect"),
            "set_sort" => array("name" => "排序", "action" => "set_sort"),
        )
    ),
    "Adv" => array(
        "name" => "广告模块",
        "node" => array(
            "index" => array("name" => "广告列表", "action" => "index"),
            "save" => array("name" => "保存", "action" => "save"),
            "foreverdelete" => array("name" => "永久删除", "action" => "foreverdelete"),
            "set_effect" => array("name" => "设置生效", "action" => "set_effect"),
        )
    ),
    "Payment" => array(
        "name" => "支付方式",
        "node" => array(
            "index" => array("name" => "支付接口列表", "action" => "index"),
            "insert" => array("name" => "安装保存", "action" => "insert"),
            "update" => array("name" => "编辑执行", "action" => "update"),
            "uninstall" => array("name" => "卸载", "action" => "uninstall"),
        )
    ),
    "Sms" => array(
        "name" => "短信接口",
        "node" => array(
            "index" => array("name" => "短信接口列表", "action" => "index"),
            "insert" => array("name" => "安装保存", "action" => "insert"),
            "update" => array("name" => "编辑执行", "action" => "update"),
            "uninstall" => array("name" => "卸载", "action" => "uninstall"),
            "send_demo" => array("name" => "发送测试短信", "action" => "send_demo"),
            "set_effect" => array("name" => "设置生效", "action" => "set_effect"),
        )
    ),
   
    "Balance" => array(
        "name" => "报表",
        "node" => array(
            "foreverdelete" => array("name" => "删除", "action" => "foreverdelete"),
            "index" => array("name" => "统计报表", "action" => "index"),
        )
    ),
);
?>