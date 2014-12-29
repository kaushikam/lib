<?php
/**
 * Created by PhpStorm.
 * User: kaushik
 * Date: 27/12/14
 * Time: 5:24 PM
 */

namespace kaushikam\lib\mapper;


use kaushikam\lib\database\adapter\IDatabaseAdapter;
use Psr\Log\LoggerInterface;

interface IBaseMapper {
    public function find($id);
    public function findAll();
    public function map(Array $data);

    /**
     * @return LoggerInterface
     */
    public function getLogger();

    /**
     * @return IDatabaseAdapter
     */
    public function getAdapter();

} 