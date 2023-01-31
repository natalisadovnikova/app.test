
CREATE TABLE `timezone_items` (
                        `id` char(36) CHARACTER SET ascii NOT NULL,
                        `city_id` char(36) CHARACTER SET ascii NOT NULL,
                        `city_name` varchar(100) NOT NULL,
                        `zone_name` varchar(100) NOT NULL,
                        `dst` boolean DEFAULT 0 NOT NULL,
                        `gmt_offset` integer DEFAULT 0 NOT NULL,
                        `zone_start` timestamp NOT NULL ,
                        `zone_end` timestamp NULL,
                        PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
