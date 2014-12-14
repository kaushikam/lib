<?php
/**
 * Created by PhpStorm.
 * User: kaushik
 * Date: 14/12/14
 * Time: 12:02 PM
 */

namespace kaushikam\lib\config;


class Config {
    /**
     * @var IConfiguration
     */
    private static $configuration;

    public static function getConfig(IConfiguration $configuration) {
        if (is_null(self::$configuration)) {
            self::$configuration = $configuration;
        }

        return self::$configuration;
    }

    public function setConfiguration(IConfiguration $configuration) {
        self::$configuration = $configuration;
    }
}