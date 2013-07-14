php-azure-table-memcache
==================
Memcache interface of Windows Azure Table Storage for PHP . 
Extends "Windows Azure SDK for PHP" . see <https://github.com/WindowsAzure/azure-sdk-for-php> . 
Compatible with Pear::Memcache . 

Features
==================
* methods 
 * addServer($host, $port) 
 * set($key, $value, $compress=0, $timeout=0) 
 * get($key) 
 * replace($key, $value, $compress=0, $timeout=0) 
 * delete($key) 
 * connect($host, $port=0, $timeout=0) 
 * pconnect($host, $port=0, $timeout=0) 
 * close() 

Getting Started
==================

Download and Setting
------------------
Download AzureTableMemcache.php and put anywahere you want . 
Open this file to edit SDK lib path like below . 

`require_once 'vendor/autoload.php';` 

Usage
==================

Getting Started
------------------

* First, include this script:  

```PHP
 require_once "AzureTableMemcache.php";
```

* For accessing a live storage service (table storage):  

```PHP
 $account = "Azure Storage Account Name"; 
 $accessKey = "Azure Storage Access Key"; 
 $tableName = "Azure Storage Table Name"; 
```

* Create Pear::Memcache compatible object:  

```PHP
 $memcache = new AzureTableMemcache($account, $accessKey, $tableName, "http");
```

* Just Call Pear::Memcache methods.  

