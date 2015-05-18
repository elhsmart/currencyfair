CREATE TABLE `trades` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `currency_from` char(3) DEFAULT NULL,
  `currency_to` char(3) DEFAULT NULL,
  `amount_sell` decimal(8,2) DEFAULT NULL,
  `amount_buy` decimal(8,2) DEFAULT NULL,
  `rate` decimal(8,4) DEFAULT NULL,
  `time_placed` datetime DEFAULT NULL,
  `originating_country` char(2) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;