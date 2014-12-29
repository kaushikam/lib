<?php
/**
 * Created by PhpStorm.
 * User: kaushik
 * Date: 27/12/14
 * Time: 6:51 PM
 */

namespace kaushikam\lib\test\model\impl;


use kaushikam\lib\model\impl\AbstractEntity;

class Session extends AbstractEntity {

    public function __construct(Array $data = array()) {
        $this->_data = array(
            'id' => null,
            'data' => null,
            'last_accessed' => null
        );

        parent::__construct($data);
    }

    public function _setData(Array $data) {
        parent::_setData($data);

        $this->setLastAccessed($this->_data['last_accessed']);
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getLastAccessed()
    {
        return $this->last_accessed;
    }

    /**
     * @param mixed $lastAccessed
     */
    public function setLastAccessed($lastAccessed)
    {
        $date = new \DateTime($lastAccessed);
        $this->last_accessed = $date->format('d-m-Y');
    }

} 