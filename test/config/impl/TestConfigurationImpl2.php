<?php
/**
 * Created by PhpStorm.
 * User: kaushik
 * Date: 14/12/14
 * Time: 1:59 PM
 */

namespace kaushikam\lib\test\config\impl;


use kaushikam\lib\config\impl\AbstractConfiguration;
use kaushikam\lib\test\config\ITestConfiguration;
use kaushikam\lib\test\config\ITestConfiguration2;

class TestConfigurationImpl2 extends AbstractConfiguration implements ITestConfiguration, ITestConfiguration2 {

    public function getEndPoint()
    {
        return $this->get('endPoint');
    }

    public function getValidPort()
    {
        return $this->get('validPort');
    }

    protected function development() {
        $this->_data = array(
            'endPoint' => 'onlineservices.kwa.org',
            'validPort' => '3060',
        );
    }

} 