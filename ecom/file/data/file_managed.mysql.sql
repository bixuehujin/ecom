DROP TABLE IF EXISTS `file_managed`;

CREATE TABLE `file_managed` (
  `fid`       int(11)      unsigned NOT NULL AUTO_INCREMENT,
  `uid`       int(11)      unsigned NOT NULL DEFAULT 0,
  `domain`    varchar(50)           NOT NULL DEFAULT '',
  `name`      varchar(255)          NOT NULL DEFAULT '',
  `hash`      varchar(255)          NOT NULL DEFAULT '',
  `mime`      varchar(255)          NOT NULL DEFAULT '',
  `size`      int(11)      unsigned NOT NULL DEFAULT 0,
  `status`    tinyint               NOT NULL DEFAULT 0,
  `created`   timestamp             NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY `fid` (`fid`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
