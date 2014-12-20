<?php
/**
 * Created by PhpStorm.
 * User: kaushik
 * Date: 20/12/14
 * Time: 2:06 PM
 */

namespace kaushikam\lib\test\config;


use kaushikam\lib\database\adapter\IDatabaseConfig;

interface ITestLibraryConfig extends IDatabaseConfig {
    public function getDiConfig();
} 