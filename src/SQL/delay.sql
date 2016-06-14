CREATE TABLE `robotstxt__delay0` (
  `base`      VARCHAR(250)
              COLLATE utf8_unicode_ci NOT NULL,
  `userAgent` VARCHAR(250)
              COLLATE utf8_unicode_ci NOT NULL,
  `microTime` BIGINT(20) UNSIGNED     NOT NULL,
  `lastDelay` MEDIUMINT(8) UNSIGNED   NOT NULL,
  PRIMARY KEY (`base`, `userAgent`),
  KEY `microTime` (`microTime`),
  KEY `lastDelay` (`lastDelay`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci
