<?php
/**
 * Created by PhpStorm.
 * User: kaushik
 * Date: 14/12/14
 * Time: 1:19 PM
 */

namespace kaushikam\lib\test\config;


use kaushikam\lib\config\Config;
use kaushikam\lib\test\BaseTestCase;
use kaushikam\lib\test\config\impl\TestConfigurationImpl;
use kaushikam\lib\test\config\impl\TestConfigurationImpl2;

class ConfigureTestCase extends BaseTestCase {
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