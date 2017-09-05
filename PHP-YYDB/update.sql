ALTER TABLE `fanwe_deal_cate`
DROP COLUMN `xuni`,
ADD COLUMN `is_fictitious`  tinyint(1) UNSIGNED NOT NULL COMMENT '是否为虚拟商品。1为虚拟商品' AFTER `icon`;