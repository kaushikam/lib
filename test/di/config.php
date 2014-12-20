<?php
/**
 * Created by PhpStorm.
 * User: kaushik
 * Date: 20/12/14
 * Time: 12:27 PM
 */

require __DIR__ . '/../../vendor/autoload.php';

/**
 * @var \kaushikam\lib\database\adapter\IDatabaseConfig
 */
$config = \kaushikam\lib\config\Config::getConfig();

return array(
    'Psr\Log\LoggerInterface' => array(
        'class' => 'kaushikam\lib\test\logger\KaushikamLogger',
        'params' => array(
            'logDirectory' => $config->getLogDirectory(),
            'logLevelThreshold' => $config->getLogLevel()
        )
    ),

    'kaushikam\lib\database\adapter\IDatabaseAdapter' => array(
        'class' => 'kaushikam\lib\database\adapter\impl\mysql\DatabaseAdapterImpl',
        'params' => array(
            'dbName' => $config->getDbName(),
            'host' => $config->getDbHost(),
            'user' => $config->getDbUser(),
            'password' => $config->getDbPasword()
        )
    )
);