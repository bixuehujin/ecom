DROP TABLE IF EXISTS `file_usage`;

CREATE TABLE `file_usage` (
  `fid`         int(11)     unsigned NOT NULL DEFAULT 0,
  `entity_type` varchar(20)          NOT NULL DEFAULT '',
  `entity_id`   int(11)     unsigned NOT NULL DEFAULT 0,
  `type`        tinyint     unsigned NOT NULL DEFAULT 0,
  `count`       int(11)     unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`fid`, `entity_type`, `entity_id`, `type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
