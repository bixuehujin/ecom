DROP TABLE IF EXISTS `term_entity`;

CREATE TABLE `term_entity` (
  `tid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `eid` int(11) unsigned NOT NULL DEFAULT 0,
  `created` int(11) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY `tid_eid` (`tid`, `eid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
