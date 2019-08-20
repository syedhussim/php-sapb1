# SAPb1
A simple and easy to use PHP library for SAP Business One Service Layer API.

## Usage
Create an array to store your SAP Business One Service Layer configuration details. 

```php
$config = [
    'https' => true,
    'host' => 'IP or Hostname',
    'port' => 50000,
    'sslOptions' => [
        "cafile" => "path/to/certificate.crt",
        "verify_peer" => true,
        "verify_peer_name" => true,
    ],
    'version' => 2
];
```

Create a new Service Layer session.

```php
$sap = SAPClient::createSession($config, 'SAP UserName', 'SAP Password', 'Company');
```

The static `createSession()` method will return a new instance of `SAPClient`. The SAPClient object provides a `service($name)` method which returns a new instance of Service with the specified name. Using this Service object you can perform CRUD actions.

### Querying A Service

The `queryBuilder()` method of the Service class returns a new instance of Query. The Query class allows you to use chainable methods to filter the requested service.

The following code sample shows how to filter Sales Orders using the Orders service.

```php
$sap = SAPClient::createSession($config, 'SAP UserName', 'SAP Password', 'Company');
$orders = $sap->getService('Orders');

$result = $orders->queryBuilder()
    ->select('DocEntry,DocNum')
    ->orderBy('DocNum', 'asc')
    ->limit(5)
    ->findAll(); 
```
The `findAll()` method will return a collection of records that match the search criteria. To return a specific record using an `id` use the `find($id)` method.

```php
...
$orders = $sap->getService('Orders');

$result = $orders->queryBuilder()
    ->select('DocEntry,DocNum')
    ->find(123456); // DocEntry value
```
Depending on the service, `$id` may be a numeric value or a string. If you want to know which field is used as the id for a service, call the `getMetaData()` method on the Service object as shown below.

```php
...
$meta = $orders->getMetaData();
```
