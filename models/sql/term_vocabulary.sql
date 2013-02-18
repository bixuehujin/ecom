DROP TABLE IF EXISTS `term_vocabulary`;

CREATE TABLE `term_vocabulary` (
  `vid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `mname` varchar(255) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `weight` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY `vid` (`vid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
