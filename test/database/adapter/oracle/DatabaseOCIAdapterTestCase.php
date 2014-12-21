<?php
/**
 * Created by PhpStorm.
 * User: kaushik
 * Date: 21/12/14
 * Time: 6:35 PM
 */

namespace kaushikam\lib\test\database\adapter\oracle;


use kaushikam\lib\database\adapter\IDatabaseAdapter;
use kaushikam\lib\test\BaseOracleTestCase;

class DatabaseOCIAdapterTestCase extends BaseOracleTestCase {

    /**
     * @var IDatabaseAdapter
     */
    private $_adapter;

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

        $sql = "SELECT * FROM online_consumer";
        $adapter = $this->_adapter->prepare($sql);
        $this->assertEquals($this->_adapter, $adapter);
        $this->assertNotNull($this->_adapter->getStatement());
    }

    public function testIsConnectWorksFine() {
        $this->getLogger()->debug("******************" . __METHOD__ . "****************");

        $this->_adapter->connect();
        $this->assertTrue($this->_adapter->isConnected());
    }

    public function testExecuteWorksProperly() {
        $this->getLogger()->debug("******************" . __METHOD__ . "****************");

        $sql = "SELECT * FROM online_consumer WHERE consumer_id = :consumerId";
        $bind = array(':consumerId' => 2114101166);
        $adapter = $this->_adapter->prepare($sql)->execute($bind);
        $this->assertEquals($this->_adapter, $adapter);

        $this->_adapter->disconnect();

        $this->_adapter->connect();

        $sql = "SELECT * FROM online_consumer WHERE cons_pwd like :password";
        $bind = array(":password" => '%kaushikistoo916good%');
        $adapter = $this->_adapter->prepare($sql)->execute($bind);
        $this->assertEquals($this->_adapter, $adapter);
    }

    protected function tearDown() {
        if ($this->_adapter->isConnected())
            $this->_adapter->disconnect();
    }
} 