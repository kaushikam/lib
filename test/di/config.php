<?php
/**
 * Created by PhpStorm.
 * User: kaushik
 * Date: 20/12/14
 * Time: 12:27 PM
 */

require __DIR__ . '/../../vendor/autoload.php';

use \kaushikam\lib\database\adapter\IDatabaseConfig;
use \kaushikam\lib\config\Config;

/**
 * @var IDatabaseConfig
 */
$config = Config::getConfig();

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
            'dbName' => $config->getMysqlDbName(),
            'host' => $config->getMysqlHost(),
            'user' => $config->getMysqlUser(),
            'password' => $config->getMysqlPasword()
        )
    ),

    'kaushikam\lib\mapper\IBaseMapper' => array(
        'class' => 'kaushikam\lib\test\mapper\impl\mysql\SessionMapper',
        'params' => array(
            'adapter' => array(
                'class' => 'kaushikam\lib\database\adapter\IDatabaseAdapter'
            )
        )
    )
);