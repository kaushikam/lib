<?php
/**
 * Created by PhpStorm.
 * User: kaushik
 * Date: 14/12/14
 * Time: 1:19 PM
 */

namespace kaushikam\lib\test\config;


use kaushikam\lib\config\Config;
use kaushikam\lib\test\config\impl\TestConfigurationImpl2;

class ConfigureTestCase extends \PHPUnit_Framework_TestCase {
    /**
     * @var ITestConfiguration
     */

    protected $_configuration;

    protected function setUp() {
        Config::configureInstance(new TestConfigurationImpl2());
        $this->_configuration = Config::getConfig();

        parent::setUp();
    }

    public function testGettingValueFromConfig() {
        $this->assertEquals('onlineservices.kwa.org', $this->_configuration->getEndPoint());
    }
} 