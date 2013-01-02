DROP TABLE IF EXISTS `attached_data`;

CREATE TABLE `attached_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `attach_to` int(11) NOT NULL DEFAULT 0,
  `name` varchar(20) NOT NULL DEFAULT '',
  `key` varchar(120) NOT NULL DEFAULT '',
  `data` text NOT NULL,
  `attached` int(11) NOT NULL DEFAULT 0,
  `expire` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  INDEX `attach` (`attach_to`, `key`, `name`),
  INDEX `expire` (`expire`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
