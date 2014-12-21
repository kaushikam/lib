<?php
/**
 * Created by PhpStorm.
 * User: kaushik
 * Date: 20/12/14
 * Time: 2:06 PM
 */

namespace kaushikam\lib\test\config;


use kaushikam\lib\config\IConfiguration;


interface ITestLibraryConfig extends IConfiguration {
    public function getMysqlDSN();
    public function getMysqlDbName();
    public function getMysqlUser();
    public function getMysqlPasword();
    public function getMysqlHost();

    public function getLogDirectory();
    public function getLogLevel();

    public function getDiConfig();
    public function getOracleDSN();
    public function getOracleConnectionString();
    public function getOracleDiConfig();

    public function getOracleHost();
    public function getOraclePort();
    public function getOracleUser();
    public function getOraclePassword();
    public function getOracleServiceName();
} 