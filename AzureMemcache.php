<?php
/**
 * LICENSE: Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * PHP version 5
 *
 * @category  Microsoft
 * @package   WindowsAzure\Table
 * @version   1.0
 * @author    <sakurai@pnop.co.jp>
 * @copyright 2013 pnop.inc
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @link      http://www.pnop.co.jp/
 */
require_once 'vendor/autoload.php';
use WindowsAzure\Common\ServicesBuilder;
use WindowsAzure\Common\ServiceException;
use WindowsAzure\Table\Models\Entity;
use WindowsAzure\Table\Models\EdmType;

/**
 * This class provides Memcache interface for table
 * service layer.
 *
 * @category  Microsoft
 * @package   WindowsAzure\Table
 * @author    <sakurai@pnop.co.jp>
 * @copyright 2013 pnop.inc
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: @package_version@
 * @link      http://www.pnop.co.jp/
 */
class AzureMemcache
{
  /**
   * @var object : Table Service object
   */
  private $tableService;

  /**
   * @var string : Table Name
   */
  private $tableName;

  /**
   * @var string : Value Key Name
   */
  private $value;

  /**
   * @var array : memcache host name
   */
  private $hosts;

  /**
   * @var array : memcache port
   */
  private $ports;

  /**
   * @var error object
   */
  public $error;

  /**
   * constructor
   *
   * @param string $accountName account name for access table storage
   * @param string $accountKey key for access table storagfe
   * @param string $tableName table name to access
   * @param string $protocol protocol name for access table storage(http or https)
   */
  function __construct($accountName, $accountKey, $tableName, $protocol='http')
  {
    /* connection strings */
    $connectionString = 'DefaultEndpointsProtocol=' . $protocol . 
                        ';AccountName=' . $accountName . 
                        ';AccountKey=' . $accountKey;

    /* connection establish */
    $this->tableService = ServicesBuilder::getInstance()->createTableService($connectionString);

    /* set property */
    $this->tableName = $tableName;
    $this->value = "value";
    $this->hosts = array();
    $this->ports = array();
    $this->error = null;

    /* create table if not exist */
    try {
      $this->tableService->createTable($tableName);
    }
    catch (ServiceException $e) {
    }
  }

  /**
   * keep compatibility with Memcache
   *
   * @param string $host "host name"
   * @param string $port "port number"
   * @return bool
   */
  public function addServer($host, $port)
  {
    array_push($this->hosts, $host);
    array_push($this->ports, $port);
    return true;
  }

  /**
   * set value (always compress off)
   *
   * @param string $key key
   * @param string $value value
   * @param int $compress always "0"
   * @param int $timeout timeout (not impremented yet)
   * @return bool
   */
  public function set($key, $value, $compress=0, $timeout=0)
  {
    /* create entity */
    $entity = new Entity();
    $entity->setPartitionKey($key);
    $entity->setRowKey($key);

    /* serialize all value */
    $entity->addProperty($this->value, null, serialize($value));

    //Homework1: Convert Data Format To Set Expire 
    //$entity->addProperty("DueDate", EdmType::DATETIME, new DateTime($timeout));

    /* store entity */
    try {
      $this->tableService->insertOrReplaceEntity($this->tableName, $entity);
      return true;
    }
    catch (ServiceException $e) {
      $this->error = $e;
      return false;
    }
  }

  /**
   * get value
   *
   * @param string $key key
   * @return mix value
   */
  public function get($key)
  {
    /* get entity */
    try {
      $result = $this->tableService->getEntity($this->tableName, $key, $key);
    }
    catch (ServiceException $e) {
      return false;
    }

    if ($result) {

      try {
        /* get value */
        $entity = $result->getEntity();
        $value = $entity->getProperty($this->value)->getValue();

        /* return unserialized value */
        return unserialize($value);
      }
      catch (ServiceException $e) {
        $this->error = $e;
        return false;
      }
    } else {
      return false;
    }
  }

  /**
   * replace value (always compress off)
   *
   * @param string $key key
   * @param string $value value
   * @param int $compress always "0"
   * @param int $timeout timeout (not impremented yet)
   * @return bool
   */
  public function replace($key, $value, $compress=0, $timeout=0)
  {
    /* create entity */
    $entity = new Entity();
    $entity->setPartitionKey($key);
    $entity->setRowKey($key);

    /* serialize all value */
    $entity->addProperty($this->value, null, serialize($value));

    //Homework1: Convert Data Format To Set Expire
    //$entity->addProperty("DueDate", EdmType::DATETIME, new DateTime($timeout));

    /* replace entity */
    try {
      $this->tableService->updateEntity($this->tableName, $entity);
      return true;
    }
    catch (ServiceException $e) {
      $this->error = $e;
      return false;
    }
  }

  /**
   * delete value
   *
   * @param string $key
   * @return bool
   */
  function delete($key)
  {
    /* delete entity */
    try {
      $this->tableService->deleteEntity($this->tableName, $key, $key);
      return true;
    }
    catch (ServiceException $e) {
      $this->error = $e;
      return false;
    }
  }

  /**
   * connect (always return TRUE)
   *
   * @param string $host
   * @param int $port
   * @param int $timeout
   * @return bool
   */
  function connect($host, $port=0, $timeout=0)
  {
    return true;
  }

  /**
   * connect (always return TRUE)
   *
   * @param string $host
   * @param int $port
   * @param int $timeout
   * @return bool
   */
  function pconnect($host, $port=0, $timeout=0)
  {
    return true;
  }

  /**
   * close (always return TRUE)
   *
   * @return bool
   */
  function close()
  {
    return true;
  }

  /**
   * destructor
   *
   * @return void
   */
  function __destruct() {
  }

}
