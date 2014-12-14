<?php
/**
 * Created by PhpStorm.
 * User: kaushik
 * Date: 14/12/14
 * Time: 12:02 PM
 */

namespace kaushikam\lib\config;


class Config {
    private static $me;

    private function __construct() {

    }

    public static function getConfig() {
        if (is_null(self::$me)) {
            self::$me = new Config();
        }

        return self::$me;
    }
} 