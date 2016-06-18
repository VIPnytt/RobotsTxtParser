# SQL feature overview

#### Universal requirements:
- [PHP Data Objects (PDO)](http://php.net/manual/en/book.pdo.php) extension
- [PDO driver](http://php.net/manual/en/pdo.drivers.php) to communicate with the SQL server

## Robots.txt Cache
Every `robots.txt` parser needs some sort of a caching system.

By caching data to the SQL server, overall performance is increased, you'll limit the number of network lags and timeouts to a bare minimum, and last but not least, no more spamming of the remote host.

[Enable the built-in caching system.](https://github.com/VIPnytt/RobotsTxtParser/blob/master/docs/sql/cache.md)

## Delay handler
Many hosts requires you to control the robot's request flow, using a minimum interval between each request.

[Learn how to honor the delay directives.](https://github.com/VIPnytt/RobotsTxtParser/blob/master/docs/sql/delay.md)
