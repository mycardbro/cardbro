DROP TABLE IF EXISTS `cc_card_stocks`; CREATE TABLE `cc_card_stocks` (`id` integer(11) NOT NULL AUTO_INCREMENT,`stock_id` INTEGER(11) NOT NULL DEFAULT 0,`amount` INTEGER(11) NOT NULL DEFAULT 0,PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8;INSERT INTO cc_card_stocks (stock_id, amount)  VALUES (2, 540),(3, -19697),(4, 5961),(1, -3273),(5, 952),(6, 14920),(7, -1676),(8, 1768);