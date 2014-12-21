<?php
/**
 * Created by PhpStorm.
 * User: kaushik
 * Date: 14/12/14
 * Time: 1:17 PM
 */

namespace kaushikam\lib\test;

use kaushikam\lib\test\config\ITestLibraryConfig;
use Psr\Log\LoggerInterface;
use rg\injektor\DependencyInjectionContainer;

abstract class BaseTestCase extends \PHPUnit_Extensions_Database_TestCase {

    /**
     * @var DependencyInjectionContainer
     */
    protected $_dic;

    /**
     * @var LoggerInterface
     */
    protected $_logger;

    /**
     * @var ITestLibraryConfig
     */
    protected $_config;

    protected function setUp() {

        $this->_logger = $this->getDic()->getInstanceOfClass('Psr\Log\LoggerInterface');

        parent::setUp();
    }

    public function getDic() {
        return $this->_dic;
    }

    /**
     * @return ITestLibraryConfig
     */
    public function getConfig() {
        return $this->_config;
    }

    public function getLogger() {
        return $this->_logger;
    }
} 