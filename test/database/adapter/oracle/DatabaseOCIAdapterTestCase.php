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

        $sql = "SELECT * FROM sessions";
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

        $sql = 'INSERT INTO sessions (id, data, last_accessed) VALUES (:id, :data, :lastAccessed)';
        $bind = array(':id' => '1',
                      ':data' => 'kaushik is good',
                      ':lastAccessed' => '23-dec-2014');
        $this->_adapter->prepare($sql)->execute($bind);
        $stmt = $this->getConnection()->getConnection()->prepare("SELECT * FROM sessions");
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        $this->assertEquals($result['ID'], $bind[':id']);

        $this->_adapter->disconnect();

        $this->_adapter->connect();

        $sql = "SELECT * FROM sessions WHERE data like :data";
        $bind = array(":data" => '%kaushik%');
        $actual = $this->_adapter->prepare($sql)->execute($bind)->fetch();
        $stmt = $this->getConnection()->getConnection()->prepare("SELECT * FROM sessions WHERE data like :data");
        $stmt->execute($bind);
        $expected = $stmt->fetch(\PDO::FETCH_ASSOC);
        $this->assertEquals($expected, $actual);
    }

    protected function tearDown() {
        $stmt = $this->getConnection()->getConnection()->prepare("TRUNCATE TABLE sessions");
        $stmt->execute();

        if ($this->_adapter->isConnected())
            $this->_adapter->disconnect();
    }
} 