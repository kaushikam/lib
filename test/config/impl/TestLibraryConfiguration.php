<?php
/**
 * Created by PhpStorm.
 * User: kaushik
 * Date: 20/12/14
 * Time: 12:37 PM
 */

namespace kaushikam\lib\test\config\impl;


use kaushikam\lib\config\impl\AbstractConfiguration;
use kaushikam\lib\test\config\ITestLibraryConfig;
use Psr\Log\LogLevel;

class TestLibraryConfiguration extends AbstractConfiguration implements ITestLibraryConfig {
    public function development() {
        ini_set('display_errors', '1');
        ini_set('error_reporting', E_ALL);

        $data = array(
            'lib.test.log.directory' => __DIR__ . '/../../log',
            'lib.test.log.level'  => LogLevel::DEBUG,
            'lib.test.db.mysql.dbName' => 'session',
            'lib.test.db.mysql.user' => 'root',
            'lib.test.db.mysql.password' => 'redhat',
            'lib.test.db.mysql.host' => 'localhost',
            'lib.test.db.mysql.dsn' => "mysql:dbname=%s;host=%s",
            'lib.test.db.oracle.user' => 'kaushik',
            'lib.test.db.oracle.password' => 'redhat',
            'lib.test.db.oracle.host' => 'localhost',
            'lib.test.db.oracle.port' => '1521',
            'lib.test.db.oracle.serviceName' => 'xe',
            'lib.test.db.oracle.dsn' => "oci:dbname=//%s:%u/%s",
            'lib.test.db.oracle.connectionString' => "//%s:%u/%s",
            'lib.test.di.config.oracle.path' => __DIR__ . '/../../di',
            'lib.test.di.config.oracle.fileName' => 'oracle_config.php',
            'lib.test.di.config.path' => __DIR__ . '/../../di',
            'lib.test.di.config.fileName' => 'config.php'
        );

        foreach ($data as $name => $value) {
            $this->set($name, $value);
        }
    }

    public function getMysqlDbName()
    {
        return $this->get('lib.test.db.mysql.dbName');
    }

    public function getMysqlUser()
    {
        return $this->get('lib.test.db.mysql.user');
    }

    public function getMysqlPasword()
    {
        return $this->get('lib.test.db.mysql.password');
    }

    public function getMysqlHost()
    {
        return $this->get('lib.test.db.mysql.host');
    }

    public function getOracleHost()
    {
        return $this->get('lib.test.db.oracle.host');
    }

    public function getOraclePort()
    {
        return $this->get('lib.test.db.oracle.port');
    }

    public function getOracleUser()
    {
        return $this->get('lib.test.db.oracle.user');
    }

    public function getOraclePassword()
    {
        return $this->get('lib.test.db.oracle.password');
    }

    public function getOracleServiceName()
    {
        return $this->get('lib.test.db.oracle.serviceName');
    }

    public function getMysqlDSN()
    {
        return $this->get('lib.test.db.mysql.dsn');
    }

    public function getOracleDSN()
    {
        return $this->get('lib.test.db.oracle.dsn');
    }

    public function getOracleConnectionString()
    {
        return $this->get('lib.test.db.oracle.connectionString');
    }


    public function getLogDirectory()
    {
        return $this->get('lib.test.log.directory');
    }

    public function getLogLevel()
    {
        return $this->get('lib.test.log.level');
    }

    public function getDiConfig() {
        return $this->get('lib.test.di.config.path') . '/' .
               $this->get('lib.test.di.config.fileName');
    }

    public function getOracleDiConfig() {
        return $this->get('lib.test.di.config.oracle.path') . '/' .
               $this->get('lib.test.di.config.oracle.fileName');
    }
} 