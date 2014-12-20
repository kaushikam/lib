<?php
/**
 * Created by PhpStorm.
 * User: kaushik
 * Date: 20/12/14
 * Time: 11:58 AM
 */

namespace kaushikam\lib\database\adapter\impl\mysql;


use kaushikam\lib\config\IConfiguration;
use kaushikam\lib\database\adapter\IDatabaseAdapter;
use kaushikam\lib\database\exception\DatabaseException;
use \PDO;
use \PDOException;
use Psr\Log\LoggerInterface;

class DatabaseAdapterImpl implements IDatabaseAdapter {

    /**
     * @var IConfiguration
     */
    protected $_config;

    /**
     * @var array
     */
    protected $_dbConfig;

    /**
     * @var PDO
     */
    protected $_connection;

    /**
     * @var LoggerInterface
     * @inject
     */
    protected $_logger;

    const DSN = "mysql:dbname=%s;host=%s";

    public function __construct($dbName, $host, $user, $password, Array $driverOptions = array()) {
        $this->_dbConfig = compact('dbName', 'host', 'user', 'password', 'driverOptions');
    }

    /**
     * @return bool
     * @throws DatabaseException
     */
    public function connect() {
        $result = true;
        try {
            $this->getLogger()->debug("Trying to connect");
            $this->_connection = new \PDO(sprintf(self::DSN, $this->_dbConfig['dbName'],
                                          $this->_dbConfig['host']), $this->_dbConfig['user'],
                                          $this->_dbConfig['password'], $this->_dbConfig['driverOptions']);
            $this->_connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->_connection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            $this->getLogger()->debug("Connection successful");
        } catch (PDOException $e) {
            $this->getLogger()->alert($e->getMessage());
            throw new DatabaseException($e->getMessage(), $e->getCode());
        }

        return $result;
    }

    /**
     * @return LoggerInterface
     */
    public function getLogger() {
        return $this->_logger;
    }

    /**
     * @param LoggerInterface $logger
     * @return void
     */
    public function setLogger(LoggerInterface $logger) {
        $this->_logger = $logger;
    }

    /**
     * @return void
     */
    public function disconnect() {
        $this->getLogger()->debug("Going to disconnect from database");
        $this->_connection = null;
    }
} 