CREATE TABLE `robotstxt__cache1` (
  `base`       VARCHAR(269)
               CHARACTER SET ascii
               COLLATE ascii_bin           NOT NULL,
  `content`    MEDIUMTEXT COLLATE utf8_bin NOT NULL,
  `statusCode` SMALLINT(3) UNSIGNED DEFAULT NULL,
  `validUntil` INT(10) UNSIGNED            NOT NULL,
  `nextUpdate` INT(10) UNSIGNED            NOT NULL,
  `effective`  VARCHAR(269)
               CHARACTER SET ascii
               COLLATE ascii_bin    DEFAULT NULL,
  `worker`     TINYINT(3) UNSIGNED  DEFAULT NULL,
  PRIMARY KEY (`base`),
  KEY `worker` (`worker`, `nextUpdate`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_bin
