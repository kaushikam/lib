<?php
/**
 * Created by PhpStorm.
 * User: kaushik
 * Date: 27/12/14
 * Time: 6:28 PM
 */

namespace kaushikam\lib\model;


use Zend\Code\Generator\Exception\InvalidArgumentException;

interface IEntity {
    /**
     * @param array $data
     * @return void
     */
    public function _setData(Array $data);

    /**
     * @param $name
     * @param $value
     * @return void
     * @throws InvalidArgumentException
     */
    public function __set($name, $value);

    /**
     * @param $name
     * @return mixed
     * @throws InvalidArgumentException
     */
    public function __get($name);

    /**
     * @param $name
     * @return bool
     */
    public function __isset($name);

    /**
     * @param $name
     * @return void
     * @throws InvalidArgumentException
     */
    public function __unset($name);

    /**
     * @return array
     */
    public function toArray();
} 