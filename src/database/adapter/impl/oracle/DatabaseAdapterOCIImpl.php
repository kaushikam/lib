<?php
/**
 * Created by PhpStorm.
 * User: kaushik
 * Date: 21/12/14
 * Time: 1:26 PM
 */

namespace kaushikam\lib\database\adapter\impl\oracle;

use kaushikam\lib\database\adapter\IDatabaseAdapter;
use kaushikam\lib\database\exception\DatabaseException;
use Psr\Log\LoggerInterface;

class DatabaseAdapterOCIImpl implements IDatabaseAdapter {

    /**
     * @var array
     */
    private $_dbConfig;

    /**
     * @var statement (Oracle statement handle)
     */
    private $_statement;

    /**
     * @var string
     */
    const DSN = "//%s:%u/%s";

    /**
     * @var LoggerInterface
     * @inject
     */
    protected $_logger;

    /**
     * @var bool
     */
    private $_connectionStatus;

    /**
     * @var resource (Oracle connection handle)
     */
    private $_connection;

    public function __construct($serviceName, $host, $port, $user, $password) {
        $this->_dbConfig = compact('serviceName', 'host', 'port', 'user', 'password');

        $this->_connectionStatus = false;
    }

    public function connect() {
        if ($this->_connection)
            return true;

        $this->getLogger()->debug("Trying to connect");
        $this->_connection = oci_connect($this->_dbConfig['user'],
                                         $this->_dbConfig['password'],
                                         $this->getConnectionString());

        if (!$this->_connection) {
            $e = oci_error();
            $this->_connectionStatus = false;
            $this->getLogger()->alert($e['message']);
            throw new DatabaseException($e['message'], $e['code']);
        }

        $this->getLogger()->debug("Connection successful");
        $this->_connectionStatus = true;
        return true;
    }

    /**
     * @return void
     */
    public function disconnect() {
        $this->getLogger()->debug("Going to disconnect from database");

        if ($this->_statement) {
            $this->getLogger()->debug("Statement exist, hence freeing it");
            ocifreestatement($this->_statement);
        }

        if ($this->isConnected()) {
            $this->getLogger()->debug("Now closing connection");
            oci_close($this->_connection);
        }
    }

    /**
     * @return bool
     */
    public function isConnected() {
        return $this->_connectionStatus;
    }


    public function prepare($sql, Array $options = array()) {
        $this->getLogger()->debug("Entering prepare method");
        $this->getLogger()->debug("SQL: " . $sql);
        $this->getLogger()->debug("Options: " . print_r($options, true));

        $this->_statement = oci_parse($this->getConnection(), $sql);

        if (!$this->_statement) {
            $e = oci_error($this->_connection);
            $this->getLogger()->alert($e['message']);
            throw new DatabaseException($e['message'], $e['code']);
        }

        $this->getLogger()->debug("Statement prepared");
        return $this;
    }


    /**
     * @return statement
     */
    public function getStatement() {
        return $this->_statement;
    }

    /**
     * @return LoggerInterface
     */
    public function getLogger() {
        return $this->_logger;
    }

    public function setLogger(LoggerInterface $logger) {
        $this->_logger = $logger;
    }

    /**
     * @return string
     */
    private function getConnectionString() {
        return sprintf(self::DSN, $this->_dbConfig['host'],
                        $this->_dbConfig['port'],
                        $this->_dbConfig['serviceName']);
    }

    private function getConnection() {
        $this->connect();

        return $this->_connection;
    }
} 