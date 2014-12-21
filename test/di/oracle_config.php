<?php
/**
 * Created by PhpStorm.
 * User: kaushik
 * Date: 21/12/14
 * Time: 6:18 PM
 */

use kaushikam\lib\config\Config;
use kaushikam\lib\test\config\ITestLibraryConfig;

$existing_array = require __DIR__ . '/config.php';

/**
 * @var ITestLibraryConfig
 */
$config = Config::getConfig();

$existing_array['kaushikam\lib\database\adapter\IDatabaseAdapter'] = array(
    'class' => 'kaushikam\lib\database\adapter\impl\oracle\DatabaseAdapterOCIImpl',
    'params' => array(
        'serviceName' => $config->getOracleServiceName(),
        'host' => $config->getOracleHost(),
        'port' => $config->getOraclePort(),
        'user' => $config->getOracleUser(),
        'password' => $config->getOraclePassword()
    )
);

return $existing_array;

