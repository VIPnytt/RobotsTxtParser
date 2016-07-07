CREATE TABLE `robotstxt__delay0` (
  `base`       VARCHAR(250)
               COLLATE utf8_unicode_ci NOT NULL,
  `userAgent`  VARCHAR(250)
               COLLATE utf8_unicode_ci NOT NULL,
  `delayUntil` BIGINT(20) UNSIGNED     NOT NULL,
  `lastDelay`  BIGINT(20) UNSIGNED     NOT NULL,
  PRIMARY KEY (`base`, `userAgent`),
  KEY `delayUntil` (`delayUntil`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci
