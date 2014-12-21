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

        $sql = "SELECT * FROM session WHERE id = :id";
        $bind = array(':id' => 1);
        $adapter = $this->_adapter->prepare($sql)->execute($bind);
        $this->assertEquals($this->_adapter, $adapter);
    }

    protected function tearDown() {
        if ($this->_adapter->isConnected())
            $this->_adapter->disconnect();
    }
}