<?php
/**
 * Created by PhpStorm.
 * User: kaushik
 * Date: 14/12/14
 * Time: 1:58 PM
 */

namespace kaushikam\lib\test\config;


use kaushikam\lib\config\IConfiguration;

interface ITestConfiguration2 extends IConfiguration {
    public function getValidPort();
} 