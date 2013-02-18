DROP TABLE IF EXISTS `file_usage`;

CREATE TABLE `file_usage` (
  `fid` int(11) unsigned NOT NULL DEFAULT 0,
  `domain` varchar(50) NOT NULL DEFAULT '',
  `id` int(11) NOT NULL DEFAULT 0,
  `count int(11) unsigned NOT NULL DEFAULT 0,  
  PRIMARY KEY `fid` (`fid`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
