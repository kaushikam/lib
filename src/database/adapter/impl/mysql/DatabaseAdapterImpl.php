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

    /**
     * @var \PDOStatement
     */
    protected $_statement;

    /**
     * @var string
     */
    const DSN = "mysql:dbname=%s;host=%s";

    /**
     * @var bool
     */
    protected $_connectionStatus;

    public function __construct($dbName, $host, $user, $password, Array $driverOptions = array()) {
        $this->_connectionStatus = false;
        $this->_dbConfig = compact('dbName', 'host', 'user', 'password', 'driverOptions');
    }

    /**
     * @return bool
     * @throws DatabaseException
     */
    public function connect() {
        $result = true;

        if ($this->_connection) {
            $this->_connectionStatus = true;
            return $result;
        }


        try {
            $this->getLogger()->debug("Trying to connect");
            $this->_connection = new \PDO(sprintf(self::DSN, $this->_dbConfig['dbName'],
                                          $this->_dbConfig['host']), $this->_dbConfig['user'],
                                          $this->_dbConfig['password'], $this->_dbConfig['driverOptions']);
            $this->_connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->_connection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

            $this->_connectionStatus = true;

            $this->getLogger()->debug("Connection successful");
        } catch (PDOException $e) {
            $this->getLogger()->alert($e->getMessage());
            throw new DatabaseException($e->getMessage(), $e->getCode());
        }

        return $result;
    }

    /**
     * @return void
     */
    public function disconnect() {
        $this->getLogger()->debug("Going to disconnect from database");
        $this->_connection = null;
    }

    /**
     * @param $sql
     * @param array $options
     * @return $this
     * @throws DatabaseException
     */
    public function prepare($sql, Array $options = array()) {
        $this->getLogger()->debug("Entering prepare method");
        $this->getLogger()->debug("SQL: " . $sql);
        $this->getLogger()->debug("Options: " . print_r($options, true));

        if ($this->connect()) {
            try {
                $this->_statement = $this->_connection->prepare($sql, $options);
                $this->getLogger()->debug("Statement prepared");
                return $this;
            } catch (PDOException $e) {
                $this->getLogger()->alert($e->getMessage());
                throw new DatabaseException($e->getMessage(), $e->getCode());
            }
        }
    }

    public function getStatement() {
        return $this->_statement;
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

    public function isConnected() {
        return $this->_connectionStatus;
    }
} 