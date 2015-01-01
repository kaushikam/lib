<?php
/**
 * Created by PhpStorm.
 * User: kaushik
 * Date: 27/12/14
 * Time: 6:31 PM
 */

namespace kaushikam\lib\model\impl;

use kaushikam\lib\model\IEntity;
use \InvalidArgumentException;

abstract class AbstractEntity implements IEntity {
    /**
     * @var array
     */
    protected $_data;

    public function __construct(Array $data = array()) {
        if (!empty($data))
            $this->_setData($data);
    }

    /**
     * @param $name
     * @param $value
     * @return void
     * @throws InvalidArgumentException
     */
    public function __set($name, $value) {
        if (array_key_exists($name, $this->_data))
            $this->_data[$name] = $value;
        else
            throw new InvalidArgumentException("There is no such methods");
    }

    /**
     * @param $name
     * @return mixed|void
     * @throws InvalidArgumentException
     */
    public function __get($name) {
        if (array_key_exists($name, $this->_data))
            return $this->_data[$name];
        else
            throw new InvalidArgumentException("There is no such method");
    }

    /**
     * @param $name
     * @return bool
     */
    public function __isset($name) {
        return isset($this->_data[$name]);
    }

    /**
     * @param $name
     * @return void
     * @throws InvalidArgumentException
     */
    public function __unset($name) {
        if (array_key_exists($name, $this->_data))
            unset($this->_data[$name]);
        else
            throw new InvalidArgumentException("There is no such method");
    }

    public function _setData(Array $data) {
        $data = array_change_key_case($data, CASE_LOWER);
        foreach ($data as $name => $value) {
           if (isset($this->_data[$name]))
               $this->{$name} = $value;
        }
    }

    /**
     * @return array
     */
    public function toArray() {
        return $this->_data;
    }
} 