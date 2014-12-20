<?php
/**
 * Created by PhpStorm.
 * User: kaushik
 * Date: 20/12/14
 * Time: 2:30 PM
 */

namespace kaushikam\lib\test\database\adapter\mysql;


use kaushikam\lib\database\adapter\IDatabaseAdapter;
use kaushikam\lib\test\BaseTestCase;

class DatabaseAdapterTestCase extends BaseTestCase {
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
        $this->assertTrue($this->_adapter->connect());
    }
}