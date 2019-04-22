CREATE TABLE IF NOT EXISTS `prefix_entity_plugin_mapping` (
    `id` INT AUTO_INCREMENT,
    `type` TEXT NOT NULL,
    `subtype` TEXT NOT NULL,
    `plugin_id` TEXT NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=INNODB;


