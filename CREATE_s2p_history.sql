DROP TABLE IF EXISTS `modx_s2p_history`;
CREATE TABLE `modx_s2p_history` (
	`id` int(10) NOT NULL AUTO_INCREMENT ,
	`updatedon` int(10) NOT NULL DEFAULT 0,
	`updatedby` text NOT NULL DEFAULT '',
	`documents` text NOT NULL DEFAULT '',
	PRIMARY KEY (`id`)
) ENGINE = MyISAM DEFAULT CHARSET = utf8 COMMENT = 'History of s2p module.';