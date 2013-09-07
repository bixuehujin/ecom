DROP TABLE IF EXISTS `term_hierarchy`;

CREATE TABLE `term_hierarchy` (
  `vid` int(11) unsigned NOT NULL DEFAULT 0,
  `tid` int(11) unsigned NOT NULL DEFAULT 0,
  `parent` int(11) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY `vid_tid_parent` (`vid`, `tid`, `parent`),
  INDEX `vid_tid` (`vid`, `tid`),
  INDEX `vid_parent` (`vid`, `parent`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
