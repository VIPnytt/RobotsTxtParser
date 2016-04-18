[![Build Status](https://travis-ci.org/VIPnytt/RobotsTxtParser.svg?branch=master)](https://travis-ci.org/VIPnytt/RobotsTxtParser)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/VIPnytt/RobotsTxtParser/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/VIPnytt/RobotsTxtParser/?branch=master)
[![Code Climate](https://codeclimate.com/github/VIPnytt/RobotsTxtParser/badges/gpa.svg)](https://codeclimate.com/github/VIPnytt/RobotsTxtParser)
[![Test Coverage](https://codeclimate.com/github/VIPnytt/RobotsTxtParser/badges/coverage.svg)](https://codeclimate.com/github/VIPnytt/RobotsTxtParser/coverage)
[![License](https://poser.pugx.org/VIPnytt/RobotsTxtParser/license)](https://github.com/VIPnytt/RobotsTxtParser/blob/master/LICENSE)
[![Packagist](https://img.shields.io/packagist/v/vipnytt/robotstxtparser.svg)](https://packagist.org/packages/vipnytt/robotstxtparser)
[![Chat](https://badges.gitter.im/VIPnytt/RobotsTxtParser.svg)](https://gitter.im/VIPnytt/RobotsTxtParser)

# Robots.txt parser class
PHP class to parse `robots.txt` according to [_Google_](https://developers.google.com/webmasters/control-crawl-index/docs/robots_txt), [_Yandex_](https://yandex.com/support/webmaster/controlling-robot/robots-txt.xml), [_W3C_](https://www.w3.org/TR/html4/appendix/notes.html#h-B.4.1.1) and [_The Web Robots Pages_](http://www.robotstxt.org/robotstxt.html) specifications.

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/6fb47427-166b-45d0-bd41-40f7a63c2b0c/big.png)](https://insight.sensiolabs.com/projects/6fb47427-166b-45d0-bd41-40f7a63c2b0c)

#### Requirements:
- PHP >=5.6
- PHP [mbstring](http://php.net/manual/en/book.mbstring.php) extension

#### Note about HHVM compatibility
Full HHVM support is planned once [facebook/hhvm#4277](https://github.com/facebook/hhvm/issues/4277) is fixed. This is due to missing "constant array" support (PHP 5.6 and 7.0 incompatibility).
