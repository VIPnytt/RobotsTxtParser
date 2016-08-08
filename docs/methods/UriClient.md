# Class UriClient
```php
@package vipnytt\RobotsTxtParser
```

Parse the robots.txt content of an URI

## Public functions
- [__construct](#__construct)
- [parent::TxtClient](#parenttxtclient)
- [getBaseUri](#getbaseuri)
- [getContents](#getcontents)
- [getEffectiveUri](#geteffectiveuri)
- [getEncoding](#getencoding)
- [getStatusCode](#getstatuscode)
- [nextUpdate](#nextupdate)
- [validUntil](#validuntil)

### __construct
```php
@param string $baseUri
@param array $curlOptions
@param int|null $byteLimit
```

### parent::TxtClient
The `UriClient` class extends the [`TxtClient`](TxtClient.md) class, every single public function from that class class, is also available here.

See [`TxtClient`](TxtClient.md) for more information.

### getBaseUri
```php
@return string
```
Get the base uri.

### getContents
```php
@return string
```
Contents of the `robots.txt` file.

### getEffectiveUri
```php
@return string|null
```
Get the effective base uri.

### getEncoding
```php
@return string
```
The `robots.txt` file's character encoding.

### getStatusCode
```php
@return int|null
```
Get the HTTP/FTP status code

### nextUpdate
```php
@return int
```
Unix timestamp for next update. The rules is normally cached for up to 24 hours, but this may vary based on the circumstances.

### validUntil
```php
@return int
```
Unix timestamp the rules is valid until. This is normally up to 24 hours, but may vary based on the remote servers response.
