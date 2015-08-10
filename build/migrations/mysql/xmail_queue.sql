DROP TABLE IF EXISTS `__PREFIX__xmail_queue`;

CREATE TABLE `__PREFIX__xmail_queue` (
  `pid` int(11) unsigned DEFAULT NULL COMMENT 'Queue ID',
  `adapter` varchar(32) NOT NULL DEFAULT '' COMMENT 'Used queue adapter',
  `date_activation` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Activation date',
  PRIMARY KEY (`pid`, `adapter`),
  KEY `idx_adapter` (`adapter`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Mail queue list';