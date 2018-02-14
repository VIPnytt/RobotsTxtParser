# Class Import
```php
@package vipnytt\RobotsTxtParser
```

Import an array earlier exported by the [`TxtClient`](TxtClient.md#export).

## Public functions
- [__construct](#__construct)
- [parent::TxtClient](#parenttxtclient)
- [getIgnoredImportData](#getignoredimportdata)

### __construct
```php
@param array $export
@param string $baseUri
```

### parent::TxtClient
The `Import` class extends the [`TxtClient`](TxtClient.md) class, every single public function from that class class, is also available here.

See [`TxtClient`](TxtClient.md) for more information.

### getIgnoredImportData
```php
@return array
```
Returns the difference between the input array and the computed exportable array. Normally this should return an empty array, but in cases where invalid/unreadable/unsupported data is given, that data is returned.

Perfect for debugging purposes, eg. when importing an custom or modified array.
