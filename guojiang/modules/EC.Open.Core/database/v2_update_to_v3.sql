
ALTER TABLE `guojiang.ec`.`ibrand_order_comment`
ADD COLUMN `goods_id` int(10) NULL DEFAULT 0 AFTER `order_item_id`;

