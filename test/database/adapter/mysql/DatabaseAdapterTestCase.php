<?php
/**
 * Created by PhpStorm.
 * User: kaushik
 * Date: 20/12/14
 * Time: 2:30 PM
 */

namespace kaushikam\lib\test\database\adapter\mysql;


use kaushikam\lib\database\adapter\IDatabaseAdapter;
use kaushikam\lib\test\BaseMySQLPDOTestCase;
use kaushikam\lib\test\BaseTestCase;

class DatabaseAdapterTestCase extends BaseMySQLPDOTestCase {
    /**
     * @var IDatabaseAdapter
     */
    protected $_adapter;

    protected function setUp() {
        parent::setUp();

        $this->_adapter = $this->getDic()
                               ->getInstanceOfClass('kaushikam\lib\database\adapter\IDatabaseAdapter');
    }

    public function testConnectionWorksProperly() {
        $this->getLogger()->debug("******************" . __METHOD__ . "****************");
        $this->assertTrue($this->_adapter->connect());
    }

    public function testPrepareWorksFine() {
        $this->getLogger()->debug("******************" . __METHOD__ . "****************");

        $sql = "SELECT * FROM session";
        $adapter = $this->_adapter->prepare($sql);
        $this->assertEquals($this->_adapter, $adapter);
        $this->assertInstanceOf('\PDOStatement', $adapter->getStatement());
    }

    public function testIsConnectWorksFine() {
        $this->getLogger()->debug("******************" . __METHOD__ . "****************");

        $this->_adapter->connect();
        $this->assertTrue($this->_adapter->isConnected());
    }

    public function testExecuteWorksFine() {
        $this->getLogger()->debug("******************" . __METHOD__ . "****************");

        $sql = "INSERT INTO session (id, data, last_accessed) VALUES (:id, :data, :lastAccessed)";
        $bind = array(':id' => '1',
            ':data' => 'kaushik is good',
            ':lastAccessed' => '23-dec-2014');
        $this->_adapter->prepare($sql)->execute($bind);
        $stmt = $this->getConnection()->getConnection()->prepare("SELECT * FROM session");
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        $this->assertEquals($result['id'], $bind[':id']);

        $sql = "SELECT * FROM session WHERE data like :data";
        $bind = array(":data" => '%kaushik%');
        $actual = $this->_adapter->prepare($sql)->execute($bind)->fetch();
        $stmt = $this->getConnection()->getConnection()->prepare("SELECT * FROM session WHERE data like :data");
        $stmt->execute($bind);
        $expected = $stmt->fetch(\PDO::FETCH_ASSOC);
        $this->assertEquals($expected, $actual);
    }

    protected function tearDown() {
        $stmt = $this->getConnection()->getConnection()->prepare("TRUNCATE TABLE session");
        $stmt->execute();

        if ($this->_adapter->isConnected())
            $this->_adapter->disconnect();
    }
}