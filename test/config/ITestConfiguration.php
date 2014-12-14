<?php
/**
 * Created by PhpStorm.
 * User: kaushik
 * Date: 14/12/14
 * Time: 12:43 PM
 */

namespace kaushikam\lib\test\config;


use kaushikam\lib\config\IConfiguration;

interface ITestConfiguration extends IConfiguration {
    public function getEndPoint();
} 