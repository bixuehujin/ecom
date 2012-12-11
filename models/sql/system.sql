DROP TABLE IF EXISTS `system`;

CREATE TABLE `system` (
  `name` varchar(255) NOT NULL,
  `value` text NOT NULL,
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
