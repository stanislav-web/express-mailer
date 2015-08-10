DROP TABLE IF EXISTS `__PREFIX__xmail_queue`;

CREATE TABLE `__PREFIX__xmail_queue` (
  `pid` int(11) unsigned DEFAULT NULL COMMENT 'Queue ID',
  `storage` varchar(32) NOT NULL DEFAULT '' COMMENT 'Used storage adapter',
  `broker` varchar(32) NOT NULL DEFAULT '' COMMENT 'Used queue adapter',
  `mail` varchar(32) NOT NULL DEFAULT '' COMMENT 'Used mail adapter',
  `date_activation` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Activation date',
  PRIMARY KEY (`pid`, `storage`, `broker`, `mail`),
  KEY `idx_adapters` (`storage`, `broker`, `mail`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Mail queue list';