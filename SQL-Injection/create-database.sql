CREATE DATABASE IF NOT EXISTS `sql_injection`;

CREATE TABLE IF NOT EXISTS `sql_injection`.`user` (
    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL,
    `password` varchar(255) NOT NULL,
    PRIMARY KEY (`id`))
