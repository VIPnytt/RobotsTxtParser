CREATE TABLE `robotstxt__cache1` (
  `base`       VARCHAR(250)
               COLLATE utf8_unicode_ci      NOT NULL,
  `content`    TEXT COLLATE utf8_unicode_ci NOT NULL,
  `statusCode` SMALLINT(4) UNSIGNED    DEFAULT NULL,
  `validUntil` INT(10) UNSIGNED             NOT NULL,
  `nextUpdate` INT(10) UNSIGNED             NOT NULL,
  `effective`  VARCHAR(250)
               COLLATE utf8_unicode_ci DEFAULT NULL,
  `worker`     TINYINT(3) UNSIGNED     DEFAULT NULL,
  PRIMARY KEY (`base`),
  KEY `worker` (`worker`, `nextUpdate`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci
