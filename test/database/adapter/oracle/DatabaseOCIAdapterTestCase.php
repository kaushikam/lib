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
    }

    public function testFetchWorksOkay() {
        $this->getLogger()->debug("******************" . __METHOD__ . "****************");

        $sql = "INSERT INTO sessions (id, data, last_accessed) VALUES (:id, :data, :lastAccessed)";
        $bind = array(':id' => '1',
            ':data' => 'kaushik is good',
            ':lastAccessed' => '23-dec-2014');
        $this->_adapter->prepare($sql)->execute($bind);

        $sql = "SELECT * FROM sessions WHERE data like :data";
        $bind = array(":data" => '%kaushik%');
        $actual = $this->_adapter->prepare($sql)->execute($bind)->fetch();
        $stmt = $this->getConnection()->getConnection()->prepare("SELECT * FROM sessions WHERE data like :data");
        $stmt->execute($bind);
        $expected = $stmt->fetch(\PDO::FETCH_ASSOC);
        $this->assertEquals($expected, $actual);
    }

    public function testFetchReturnsFalseOnFailure() {
        $this->getLogger()->debug("******************" . __METHOD__ . "****************");

        $sql = "SELECT * FROM sessions WHERE data like :data";
        $bind = array(":data" => '%kaushik%');
        $actual = $this->_adapter->prepare($sql)->execute($bind)->fetch();
        $this->assertFalse($actual);
    }

    public function testFetchAllReturnsAllRows() {
        $this->getLogger()->debug("******************" . __METHOD__ . "****************");

        $sql = "INSERT INTO sessions (id, data, last_accessed) VALUES (:id, :data, :lastAccessed)";
        $bind = array(':id' => '1',
            ':data' => 'kaushik is good',
            ':lastAccessed' => '23-dec-2014');
        $this->_adapter->prepare($sql)->execute($bind);

        $sql = "INSERT INTO sessions (id, data, last_accessed) VALUES (:id, :data, :lastAccessed)";
        $bind = array(':id' => '2',
            ':data' => 'kaushik is too good',
            ':lastAccessed' => '23-dec-2014');
        $this->_adapter->prepare($sql)->execute($bind);

        $sql = "SELECT * FROM sessions";
        $actual = $this->_adapter->prepare($sql)->execute()->fetchAll();

        $stmt = $this->getConnection()->getConnection()->prepare($sql);
        $stmt->execute();
        $expected = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $this->assertEquals($expected, $actual);
    }

    public function testInsertWorksFineByReturningId() {
        $this->getLogger()->debug("******************" . __METHOD__ . "****************");

        $bind = array(
            'id' => '1',
            'data' => 'kaushik is too good',
            'last_accessed' => '24-dec-2014'
        );
        $result = $this->_adapter->insert('sessions', $bind, 'id', SQLT_CHR);
        $this->assertEquals("1", $result);
    }

    public function testUpdateWorksFine() {
        $this->getLogger()->debug("******************" . __METHOD__ . "****************");

        $bind = array(
            'id' => '1',
            'data' => 'kaushik is too good',
            'last_accessed' => '24-dec-2014'
        );
        $result = $this->_adapter->insert('sessions', $bind);

        $bind['data'] = 'Kaushik is tooooo goood';
        $bind['last_accessed'] = '25-dec-2014';

        $where = array(
            'id' => array('op' => '=', 'value' => '1'),
            'last_accessed' => array('op' => '=', 'value' => '24-dec-2014')
        );

        $affectedRows = $this->_adapter->update('sessions', $bind, $where);

        $sql = "SELECT * FROM sessions WHERE id = :id";
        $stmt = $this->getConnection()->getConnection()->prepare($sql);
        $bind = array(':id' => '1');
        $stmt->execute($bind);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        $this->assertEquals(1, $affectedRows);
        $this->assertEquals("Kaushik is tooooo goood", $row['DATA']);
    }

    protected function tearDown() {
        $stmt = $this->getConnection()->getConnection()->prepare("TRUNCATE TABLE sessions");
        $stmt->execute();

        if ($this->_adapter->isConnected())
            $this->_adapter->disconnect();
    }
} 