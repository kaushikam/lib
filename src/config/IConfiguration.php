<?php
/**
 * Created by PhpStorm.
 * User: kaushik
 * Date: 14/12/14
 * Time: 12:19 PM
 */

namespace kaushikam\lib\config;


interface IConfiguration {
    public function toArray();
    public function getEnvironment();
    public function setEnvironment($environment);
} 