<?php
/**
 * Created by PhpStorm.
 * User: kaushik
 * Date: 14/12/14
 * Time: 12:02 PM
 */

namespace kaushikam\lib\config;

use \InvalidArgumentException;

class Config {
    /**
     * @var IConfiguration
     */
    private static $configuration;

    public static function getConfig() {
        if (is_null(self::$configuration)) {
            throw new InvalidArgumentException("Configuration is null");
        }

        return self::$configuration;
    }

    public static function configureInstance(IConfiguration $configuration) {
        if (is_null(self::$configuration) && is_null($configuration))
                throw new InvalidArgumentException("Configuration is null");

        if (!$configuration instanceof IConfiguration)
            throw new InvalidArgumentException("Invalid configuration type");

        self::$configuration = $configuration;

        return self::$configuration;
    }
}