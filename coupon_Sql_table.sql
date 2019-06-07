CREATE TABLE `coupons` (
  `id` int(11) NOT NULL,
  `code` varchar(255) NOT NULL,
  `data_start` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `data_end` datetime NOT NULL,
  `user` int(11) DEFAULT NULL COMMENT '',
  `is_percenurl` int(11) NOT NULL DEFAULT '0' COMMENT 'is percenual descount ',
  `min_value` int(11) NOT NULL DEFAULT '0' COMMENT '',
  `max_value` int(11) NOT NULL DEFAULT '0' COMMENT 'ì',
  `descount_value` float NOT NULL COMMENT ' ',
  `status` int(11) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
