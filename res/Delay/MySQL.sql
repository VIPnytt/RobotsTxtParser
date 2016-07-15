CREATE TABLE `robotstxt__delay0` (
  `base`       VARCHAR(269)
               COLLATE ascii_bin   NOT NULL,
  `userAgent`  VARCHAR(63)
               COLLATE ascii_bin   NOT NULL,
  `delayUntil` BIGINT(20) UNSIGNED NOT NULL,
  `lastDelay`  BIGINT(20) UNSIGNED NOT NULL,
  PRIMARY KEY (`base`, `userAgent`),
  KEY `delayUntil` (`delayUntil`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = ascii
  COLLATE = ascii_bin
