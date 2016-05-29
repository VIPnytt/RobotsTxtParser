--
-- Table structure for table `robotstxt__delay0`
--

CREATE TABLE IF NOT EXISTS `robotstxt__delay0` (
  `base`      VARCHAR(250)
              COLLATE utf8_unicode_ci NOT NULL,
  `userAgent` VARCHAR(250)
              COLLATE utf8_unicode_ci NOT NULL,
  `microTime` BIGINT(20) UNSIGNED     NOT NULL
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;

--
-- Indexes for table `robotstxt__delay0`
--
ALTER TABLE `robotstxt__delay0`
ADD PRIMARY KEY (`base`, `userAgent`);
