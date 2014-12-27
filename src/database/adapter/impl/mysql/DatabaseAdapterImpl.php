<?php
/**
 * Created by PhpStorm.
 * User: kaushik
 * Date: 20/12/14
 * Time: 11:58 AM
 */

namespace kaushikam\lib\database\adapter\impl\mysql;

use kaushikam\lib\database\adapter\IDatabaseAdapter;
use kaushikam\lib\database\exception\DatabaseException;
use \PDO;
use \PDOException;
use Psr\Log\LoggerInterface;

class DatabaseAdapterImpl implements IDatabaseAdapter {
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
            $this->_connectionStatus = false;
            $this->getLogger()->alert($e->getMessage());
            throw new DatabaseException($e->getMessage());
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

        try {
            $this->_statement = $this->getConnection()->prepare($sql, $options);
            $this->getLogger()->debug("Statement prepared");
            return $this;
        } catch (PDOException $e) {
            $this->getLogger()->alert($e->getMessage());
            throw new DatabaseException($e->getMessage());
        }


    }

    /**
     * @param array $parameters
     * @throws DatabaseException
     * @return $this
     */
    public function execute(Array &$parameters = array()) {
        $this->getLogger()->debug("Bind params: " . print_r($parameters, true));

        if (!$this->getStatement()) {
            $this->getLogger()->alert("There is no statement object");
            throw new DatabaseException("There is no statement object");
        }

        try {
            foreach ($parameters as $name => $value) {
                $this->getLogger()->debug("Binding $name with $value");
                $this->getStatement()->bindValue($name, $value);
            }

            $this->getLogger()->debug("Trying to execute the statement");

            if ($this->getStatement()->execute()) {
                $this->getLogger()->debug("Statement executed successfully");
            } else {
                $this->getLogger()->info("Statement execution failed");
            }

            $this->getLogger()->debug("Returning adapter object");
            return $this;
        } catch (PDOException $e) {
            $this->getLogger()->alert($e->getMessage());
            throw new DatabaseException($e->getMessage());
        }
    }

    /**
     * @return array|bool
     */
    public function fetch()
    {
        if (!$this->getStatement()) {
            $this->getLogger()->debug("There is no statement object");
            throw new DatabaseException("There is no statment object");
        }

        try {
            $result = $this->getStatement()->fetch(PDO::FETCH_ASSOC);
            $this->getLogger()->debug("Successfully returning resultant array");
            $this->getLogger()->debug("Result: " . print_r($result, true));
            return $result;
        } catch (PDOException $e) {
            $this->getLogger()->alert($e->getMessage());
            throw new DatabaseException($e->getMessage());
        }
    }

    /**
     * @return array | bool
     */
    public function fetchAll()
    {
        if (!$this->getStatement()) {
            $this->getLogger()->debug("There is no statement object");
            throw new DatabaseException("There is no statment object");
        }

        try {
            $result = $this->getStatement()->fetchAll(PDO::FETCH_ASSOC);
            $this->getLogger()->debug("Successfully returning resultant array");
            $this->getLogger()->debug("Result: " . print_r($result, true));
            return $result;
        } catch (PDOException $e) {
            $this->getLogger()->alert($e->getMessage());
            throw new DatabaseException($e->getMessage());
        }
    }

    /**
     * @param $table
     * @param array $bind
     * @param string $id
     * @param null $idType
     * @return mixed
     */
    public function insert($table, Array $bind, $id = 'id', $idType = null)
    {
        $this->getLogger()->debug("Table name is: $table");
        $this->getLogger()->debug("Bind: " . print_r($bind, true));
        $this->getLogger()->debug("ID: $id");

        foreach ($bind as $name => $value) {
            unset ($bind[$name]);
            if ($name == $id)
                $bind['LAST_INSERT_ID(:' . $name . ')'] = $value;
            else
                $bind[':' . $name] = $value;
        }

        $sql = "INSERT INTO $table VALUES (" . implode(', ', array_keys($bind)) . ")";
        if (isset($bind['LAST_INSERT_ID(:' . $id . ')'])) {
            $idValue = $bind['LAST_INSERT_ID(:' . $id . ')'];
            unset($bind['LAST_INSERT_ID(:' . $id . ')']);
            $bind[':' . $id] = $idValue;
        }
        $this->prepare($sql)->execute($bind);

        $this->getLogger()->info("New row created with $id: " .
            $this->getConnection()->lastInsertId($id));
        return $this->getConnection()->lastInsertId($id);
    }

    /**
     * @param $table
     * @param array $bind
     * @param array $where
     * @param string $boolOperator
     * @return mixed|void
     * @throws DatabaseException
     */
    public function update($table, Array $bind, Array $where = array(), $boolOperator = ' AND ')
    {
        $this->getLogger()->debug("Table name is: $table");
        $this->getLogger()->debug("Bind: " . print_r($bind, true));
        $this->getLogger()->debug("Conditions: " . print_r($where, true));

        $set = array();
        foreach ($bind as $name => $value) {
            unset($bind[$name]);
            $bind[':' . $name] = $value;
            $set[] = $name . ' = :' . $name;
        }

        $conditions = array();
        if ($where) {
            foreach ($where as $lhs => $rhs) {
                unset($where[$lhs]);
                $where[':c'.$lhs] = $rhs['value'];
                $conditions[] = $lhs . " " . $rhs['op'] . " :c" . $lhs;
            }
        }

        $sql = "UPDATE $table SET " . implode(', ', $set) .
                ((!empty($conditions)) ? " WHERE " . implode($boolOperator, $conditions) : "");

        $bind = array_merge($bind, $where);
        $this->prepare($sql)->execute($bind);

        if (($affectedRows = $this->numberOfAffectedRows()) > 0) {
            $this->getLogger()->info("Update completed successfully by updating $affectedRows");
        } else {
            $this->getLogger()->info("Update did not update anything");
        }

        return $affectedRows;
    }

    /**
     * @param $table
     * @param array $where
     * @param string $boolOperator
     * @return bool
     * @throws DatabaseException
     */
    public function delete($table, Array $where = array(), $boolOperator = ' AND ')
    {
        $this->getLogger()->debug("Table: $table");
        $this->getLogger()->debug("Conditions: " . print_r($where, true));
        $this->getLogger()->debug("Bool Op: $boolOperator");

        $conditions = array();
        if ($where) {
            foreach ($where as $lhs => $rhs) {
                unset($where[$lhs]);
                $where[':c'.$lhs] = $rhs['value'];
                $conditions[] = $lhs . " " . $rhs['op'] . " :c" . $lhs;
            }
        }

        $sql = "DELETE FROM $table " . ((!empty($conditions)) ?
                " WHERE " . implode($boolOperator, $conditions) : "");

        $this->prepare($sql)->execute($where);

        if (($affectedRows = $this->numberOfAffectedRows()) > 0) {
            $this->getLogger()->info("Update completed successfully by updating $affectedRows");
        } else {
            $this->getLogger()->info("Update did not update anything");
        }

        return $affectedRows;
    }

    /**
     * @return int
     * @throws DatabaseException
     */
    private function numberOfAffectedRows() {
        try {
            return $this->getStatement()->rowCount();
        } catch (PDOException $e) {
            throw new DatabaseException($e->getMessage());
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

    private function getConnection() {
        $this->connect();

        return $this->_connection;
    }
} 