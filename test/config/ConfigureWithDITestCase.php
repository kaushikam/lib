<?php
/**
 * Created by PhpStorm.
 * User: kaushik
 * Date: 17/12/14
 * Time: 4:57 PM
 */

namespace kaushikam\lib\test\config;


use kaushikam\lib\test\BaseTestCase;
use rg\injektor\Configuration;
use rg\injektor\DependencyInjectionContainer;

class ConfigureWithDITestCase extends \PHPUnit_Framework_TestCase {

    protected $_configuration;

    protected $_dic;

    public function setUp() {
        $conf = new Configuration(__DIR__ . '/config.php');
        $this->_dic = new DependencyInjectionContainer($conf);

        $this->_configuration = $this->_dic->getInstanceOfClass('kaushikam\lib\config\IConfiguration');
    }

    public function testGettingValueFromConfig() {
        $this->assertEquals('onlineservices.kwa.org', $this->_configuration->getEndPoint());
    }
} 