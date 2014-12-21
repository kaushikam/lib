<?php
/**
 * Created by PhpStorm.
 * User: kaushik
 * Date: 20/12/14
 * Time: 1:36 PM
 */

namespace kaushikam\lib\database\adapter;


use kaushikam\lib\config\IConfiguration;

interface IDatabaseConfig extends IConfiguration {
    public function getMysqlDbName();
    public function getMysqlUser();
    public function getMysqlPasword();
    public function getMysqlHost();
    public function getLogDirectory();
    public function getLogLevel();
} 