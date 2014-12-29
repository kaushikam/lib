<?php
/**
 * Created by PhpStorm.
 * User: kaushik
 * Date: 27/12/14
 * Time: 7:04 PM
 */

namespace kaushikam\lib\test\mapper;


use kaushikam\lib\mapper\IBaseMapper;
use kaushikam\lib\test\BaseMySQLPDOTestCase;
use kaushikam\lib\test\model\impl\Session;

class SessionMapperMySQLTestCase extends BaseMySQLPDOTestCase {

    /**
     * @var IBaseMapper
     */
    protected $_mapper;

    protected function setUp() {
        parent::setUp();

        $this->_mapper = $this->_dic->getInstanceOfClass('kaushikam\lib\mapper\IBaseMapper');
    }

    public function testSessionMapperFindsEntity() {
        $this->getLogger()->debug("******************" . __METHOD__ . "****************");

        $adapter = $this->_mapper->getAdapter();
        $bind = array(
            'id' => '1',
            'data' => 'kaushik is too good',
            'last_accessed' => '2014-12-24'
        );
        $adapter->insert('session', $bind);

        $expected = new Session($bind);

        $session = $this->_mapper->find('1');
        $this->assertInstanceOf('kaushikam\lib\test\model\impl\Session', $session);
        $this->assertEquals('1', $session->getId());
        $this->assertEquals($expected, $session);
    }

    protected function tearDown() {
        $stmt = $this->getConnection()->getConnection()->prepare("TRUNCATE TABLE session");
        $stmt->execute();
    }
} 