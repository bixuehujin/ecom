DROP TABLE IF EXISTS `term_entity`;

CREATE TABLE `term_entity` (
  `tid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `entity_id` int(11) unsigned NOT NULL DEFAULT 0,
  `entity_type` varchar(30) NOT NULL DEFAULT '',
  `created` int(11) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY `pk` (`tid`, `entity_id`, `entity_type`),
  INDEX `entity` (`entity_id`, `entity_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
