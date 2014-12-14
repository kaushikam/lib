<?php
/**
 * Created by PhpStorm.
 * User: kaushik
 * Date: 14/12/14
 * Time: 12:44 PM
 */

namespace kaushikam\lib\test\config\impl;


use kaushikam\lib\config\impl\AbstractConfiguration;
use kaushikam\lib\test\config\ITestConfiguration;

class TestConfigurationImpl extends AbstractConfiguration implements ITestConfiguration {

    public function getEndPoint()
    {
        return $this->endPoint;
    }

    protected function development() {
        $this->_data = array(
            'endPoint' => 'onlineservices.kwa.org'
        );
    }
} 