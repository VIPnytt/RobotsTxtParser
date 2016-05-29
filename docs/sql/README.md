# SQL features

#### Universal requirements:
- [PHP Data Objects (PDO)](http://php.net/manual/en/book.pdo.php) extension
- [PDO driver](http://php.net/manual/en/pdo.drivers.php) for your SQL database

## Robots.txt Cache
Caching the `robots.txt` files to the SQL server, greatly improves performance, avoids spamming of the remote host and any unnecessary network lag or timeouts is avoided.

[Read more](cache.md)

## Delay handler
Some hosts requires you to control the request flow, and not send the requests too frequent.

[Read more](delay.md)
