<?php
/**
 * Created by PhpStorm.
 * User: kaushik
 * Date: 27/12/14
 * Time: 6:52 PM
 */

namespace kaushikam\lib\test\model;


use kaushikam\lib\test\model\impl\Session;

class EntityTestCase extends \PHPUnit_Framework_TestCase {

    /**
     * @var Session
     */
    protected $_session;

    public function testConstructorSetsData() {
        $data = array(
            'id' => '1',
            'data' => 'Kaushik is too good',
            'last_accessed' => '24-12-2014'
        );


        $this->_session = new Session($data);

        $this->assertEquals('1', $this->_session->getId());
        $this->assertEquals($data, $this->_session->toArray());
    }
} 