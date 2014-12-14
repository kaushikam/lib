<?php
/**
 * Created by PhpStorm.
 * User: kaushik
 * Date: 14/12/14
 * Time: 2:43 PM
 */

namespace kaushikam\lib\config\impl;


use kaushikam\lib\config\IConfiguration;

abstract class AbstractConfiguration implements IConfiguration {
    /**
     * @var array
     */
    protected $_data = array();

    protected $_environment;

    public function __construct($environment = null) {
        if (is_null($environment)) {
            if (getenv('APPLICATION_ENV')) {
                $environment = getenv('APPLICATION_ENV');
            } else {
                $environment = 'development';
            }
        }

        $this->_environment = $environment;

        $this->{$this->_environment}();
    }

    public function __isset($name) {
        return isset($this->_data[$name]);
    }

    public function __get($name) {
        $parsed = explode('.', $name);

        $result = $this->_data;

        while ($parsed) {
            $next = array_shift($parsed);

            if (isset($result[$next])) {
                $result = $result[$next];
            } else {
                return null;
            }
        }

        return $result;
    }

    public function toArray() {
        return $this->_data;
    }

    public function getEnvironment() {
        return $this->_environment;
    }

    public function setEnvironment($environment) {
        $this->_environment = $environment;
        $this->{$this->_environment}();
    }
} 