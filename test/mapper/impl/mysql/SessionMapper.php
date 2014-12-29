<?php
/**
 * Created by PhpStorm.
 * User: kaushik
 * Date: 27/12/14
 * Time: 6:26 PM
 */

namespace kaushikam\lib\test\mapper\impl\mysql;

use kaushikam\lib\database\adapter\IDatabaseAdapter;
use kaushikam\lib\mapper\impl\AbstractMapper;
use kaushikam\lib\test\model\impl\Session;

class SessionMapper extends AbstractMapper {

    public function __construct(IDatabaseAdapter $adapter) {
        $this->_entityTable = 'session';
        $this->_id = 'id';
        $this->_idType = \PDO::PARAM_STR;

        parent::__construct($adapter);
    }

    public function map(Array $data) {
        $session = new Session($data);
        return $session;
    }
} 