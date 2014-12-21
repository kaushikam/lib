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
        $this->assertTrue($this->_adapter->connect());
    }

    public function testPrepareWorksFine() {
        $sql = "SELECT * FROM session";
        $adapter = $this->_adapter->prepare($sql);
        $this->assertEquals($this->_adapter, $adapter);
        $this->assertNotNull($this->_adapter->getStatement());
    }

    public function testIsConnectWorksFine() {
        $this->_adapter->connect();
        $this->assertTrue($this->_adapter->isConnected());
    }

    protected function tearDown() {
        if ($this->_adapter->isConnected())
            $this->_adapter->disconnect();
    }
} 