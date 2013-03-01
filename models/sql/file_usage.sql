DROP TABLE IF EXISTS `file_usage`;

CREATE TABLE `file_usage` (
  `fid` int(11) unsigned NOT NULL DEFAULT 0,
  `entity_type` varchar(50) NOT NULL DEFAULT '',
  `entity_id` int(11) unsigned NOT NULL DEFAULT 0,
  `count` int(11) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY `pk` (`fid`, `domain`, `id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
