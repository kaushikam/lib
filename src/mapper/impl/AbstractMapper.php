<?php
/**
 * Created by PhpStorm.
 * User: kaushik
 * Date: 27/12/14
 * Time: 5:26 PM
 */

namespace kaushikam\lib\mapper\impl;


use kaushikam\lib\database\adapter\IDatabaseAdapter;
use kaushikam\lib\mapper\IBaseMapper;
use Psr\Log\LoggerInterface;

abstract class AbstractMapper implements IBaseMapper {

    /**
     * @var IDatabaseAdapter
     */
    protected $_adapter;

    /**
     * @var LoggerInterface
     * @inject
     */
    protected $_logger;

    /**
     * @var string
     */
    protected $_entityTable;

    /**
     * @var string
     */
    protected $_id;

    /**
     * @var integer
     */
    protected $_idType;

    /**
     * @var IDatabaseAdapter
     * @inject
     */
    public function __construct(IDatabaseAdapter $adapter) {
        $this->_adapter = $adapter;
    }

    public function find($id) {
        $this->getLogger()->debug("Finding entity in " . $this->_entityTable .
                                  " with " . $this->_id . ": " . $id);
        $rows = $this->getAdapter()->select($this->_entityTable, array(),
            array($this->_id => array('op' => '=', 'value' => $id)));

        $this->getAdapter()->disconnect();

        if ($rows) {
            return $this->map($rows[0]);
        } else {
            return null;
        }
    }

    public function findAll() {
        $this->getLogger()->debug("Finding all rows in the table " . $this->_entityTable);

        $rows = $this->getAdapter()->select($this->_entityTable);

        $this->getAdapter()->disconnect();

        if ($rows) {
            $objectArray = array();
            foreach ($rows as $row) {
                $objectArray[] = $this->map($row);
            }
            return $objectArray;
        } else {
            return null;
        }
    }

    /**
     * @return LoggerInterface
     */
    public function getLogger() {
        return $this->_logger;
    }

    /**
     * @return IDatabaseAdapter
     */
    public function getAdapter() {
        return $this->_adapter;
    }
} 