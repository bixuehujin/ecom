DROP TABLE IF EXISTS `term_hierarchy`;

CREATE TABLE `term_hierarchy` (
  `tid` int(11) unsigned NOT NULL DEFAULT 0,
  `parent` int(11) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY `tid_parent` (`tid`, `parent`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
