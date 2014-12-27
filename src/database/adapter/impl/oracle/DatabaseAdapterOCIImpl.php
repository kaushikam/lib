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
        $this->_connection = @oci_connect($this->_dbConfig['user'],
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
            @ocifreestatement($this->_statement);
        }

        if ($this->isConnected()) {
            $this->getLogger()->debug("Now closing connection");
            @oci_close($this->_connection);
        }
    }

    /**
     * @return bool
     */
    public function isConnected() {
        return $this->_connectionStatus;
    }

    /**
     * @param $sql
     * @param array $options
     * @throws DatabaseException
     * @return $this
     */
    public function prepare($sql, Array $options = array()) {
        $this->getLogger()->debug("Entering prepare method");
        $this->getLogger()->debug("SQL: " . $sql);
        $this->getLogger()->debug("Options: " . print_r($options, true));

        $this->_statement = @oci_parse($this->getConnection(), $sql);

        if (!$this->_statement) {
            $e = oci_error($this->_connection);
            $this->getLogger()->alert($e['message']);
            throw new DatabaseException($e['message'], $e['code']);
        }

        $this->getLogger()->debug("Statement prepared");
        return $this;
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

        foreach ($parameters as $name => $value) {
            if (is_array($value)) {
                $this->getLogger()->debug("Binding $name with " . print_r($value, true));
                @oci_bind_by_name($this->getStatement(),
                    $name,
                    $parameters[$name]['value'],
                    $parameters[$name]['length'],
                    $parameters[$name]['type']);
            } else {
                $this->getLogger()->debug("Binding $name with $value");
                @oci_bind_by_name($this->getStatement(), $name, $parameters[$name]);
            }


            if ($e = oci_error($this->getStatement())) {
                 $this->getLogger()->alert($e['message']);
                 throw new DatabaseException($e['message'], $e['code']);
            }
        }

        $this->getLogger()->debug("Trying to execute the statement");
        if (@oci_execute($this->getStatement())) {
            $this->getLogger()->debug("Statement executed successfully");
        } else {
            $this->getLogger()->info("Statement execution failed");
        }

        if ($e = oci_error($this->getStatement())) {
            $this->getLogger()->alert($e['message']);
            throw new DatabaseException($e['message'], $e['code']);
        }

        $this->getLogger()->debug("Returning adapter object");
        return $this;
    }

    /**
     * @return array|bool
     */
    public function fetch()
    {
        if (!$this->getStatement()) {
            $this->getLogger()->debug("There is no statement object");
            throw new DatabaseException("There is no statement object");
        }

        $result = @oci_fetch_assoc($this->getStatement());
        if($e = oci_error($this->getStatement())) {
            $this->getLogger()->alert($e['message']);
            throw new DatabaseException($e['message'], $e['code']);
        }

        $this->getLogger()->debug("Successfully returning resultant array");
        $this->getLogger()->debug("Result: " . print_r($result, true));
        return $result;
    }

    /**
     * @return array | bool
     */
    public function fetchAll()
    {
        if (!$this->getStatement()) {
            $this->getLogger()->debug("There is no statement object");
            throw new DatabaseException("There is no statement object");
        }

        $result = array();
        $numOfRows = @oci_fetch_all($this->getStatement(),
                            $result, 0, -1,
                            OCI_FETCHSTATEMENT_BY_ROW + OCI_ASSOC);
        if($e = oci_error($this->getStatement())) {
            $this->getLogger()->alert($e['message']);
            throw new DatabaseException($e['message'], $e['code']);
        }

        if ($numOfRows >= 0) {
            $this->getLogger()->debug("Successfully returning resultant array");
            $this->getLogger()->debug("Result: " . print_r($result, true));
            return $result;
        }

        return false;
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

        if (is_null($idType))
            $idType = SQLT_INT;
        $this->getLogger()->debug("ID type: $idType");

        foreach ($bind as $name => $value) {
            unset ($bind[$name]);
            $bind[':' . $name] = $value;
        }

        $sql = "INSERT INTO $table VALUES (" . implode(', ', array_keys($bind)) . ") RETURNING $id INTO :returnId";
        $bind[':returnId'] = array ('value' => null, 'length' => -1, 'type' => $idType);
        $this->prepare($sql)->execute($bind);

        $this->getLogger()->info("New row created with $id: " . $bind[':returnId']['value']);
        return $bind[':returnId']['value'];
    }

    /**
     * @param $table
     * @param array $bind
     * @param array $where
     * @param string $boolOperator
     * @return mixed
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
     * @param $table
     * @param array $needed
     * @param array $where
     * @param array $orderItems
     * @param string $boolOperator
     * @return array|void
     * @throws DatabaseException
     */
    public function select($table, Array $needed = array(), Array $where = array(),
                           Array $orderItems = array(), $boolOperator = ' AND ')
    {
        $this->getLogger()->debug("Table: $table");
        $this->getLogger()->debug("Columns: " . print_r($needed, true));
        $this->getLogger()->debug("Conditions: " . print_r($where, true));
        $this->getLogger()->debug("Order by columns: " . print_r($orderItems, true));
        $this->getLogger()->debug("Bool OP: $boolOperator");

        $conditions = array();
        if ($where) {
            foreach ($where as $lhs => $rhs) {
                unset($where[$lhs]);
                $where[':c'.$lhs] = $rhs['value'];
                $conditions[] = $lhs . " " . $rhs['op'] . " :c" . $lhs;
            }
        }

        $orders = array();
        if ($orderItems) {
            foreach ($orderItems as $name => $order) {
                $orders[] = $name . " " . $order;
            }
        }

        $sql = "SELECT " . implode(", ", $needed) . " FROM " .
            $table . ((!empty($where)) ? " WHERE " . implode($boolOperator, $conditions) : " ") .
            ((!empty($orders)) ? " ORDER BY " . implode(", ", $orders) : " ");

        $rows = $this->prepare($sql)->execute($where)->fetchAll();
        if ($rows)
            return $rows;
        else
            $this->getLogger()->info("SQL did not fetch data");
    }

    /**
     * @return int
     * @throws DatabaseException
     */
    private function numberOfAffectedRows() {
       $affectedRows = @oci_num_rows($this->getStatement());
        if ($e = oci_error($this->getStatement())) {
            $this->getLogger()->info($e['message']);
            throw new DatabaseException($e['message'], $e['code']);
        }
        return $affectedRows;
    }


    /**
     * @return resource
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