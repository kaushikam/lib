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
            'lib.test.db.db_name' => 'session',
            'lib.test.db.user' => 'root',
            'lib.test.db.password' => 'redhat',
            'lib.test.db.host' => 'localhost',
            'lib.test.di.config.path' => __DIR__ . '/../../di',
            'lib.test.di.config.filename' => 'config.php'
        );

        foreach ($data as $name => $value) {
            $this->set($name, $value);
        }
    }

    public function getDbName()
    {
        return $this->get('lib.test.db.db_name');
    }

    public function getDbUser()
    {
        return $this->get('lib.test.db.user');
    }

    public function getDbPasword()
    {
        return $this->get('lib.test.db.password');
    }

    public function getDbHost()
    {
        return $this->get('lib.test.db.host');
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
               $this->get('lib.test.di.config.filename');
    }
} 