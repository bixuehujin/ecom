DROP TABLE IF EXISTS `term`;

CREATE TABLE `term` (
  `tid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `vid` int(11) unsigned NOT NULL DEFAULT 0,
  `name` varchar(255) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `weight` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY `tid` (`tid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
