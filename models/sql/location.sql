DROP TABLE IF EXISTS `location`;

CREATE TABLE `location` (
  `id` INT(11)  NOT NULL DEFAULT 0,
  `name` varchar(20) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
