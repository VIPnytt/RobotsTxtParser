--
-- Table structure for table `robotstxt__cache0`
--
CREATE TABLE IF NOT EXISTS `robotstxt__cache0` (
  `base`       VARCHAR(250)
               COLLATE utf8_unicode_ci      NOT NULL,
  `content`    TEXT COLLATE utf8_unicode_ci NOT NULL,
  `statusCode` SMALLINT(4) UNSIGNED         NOT NULL,
  `validUntil` INT(10) UNSIGNED             NOT NULL,
  `nextUpdate` INT(10) UNSIGNED             NOT NULL,
  `worker`     TINYINT(3) UNSIGNED DEFAULT NULL
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;

--
-- Indexes for table `robotstxt__cache0`
--
ALTER TABLE `robotstxt__cache0`
ADD PRIMARY KEY (`base`), ADD KEY `worker` (`worker`, `nextUpdate`);
