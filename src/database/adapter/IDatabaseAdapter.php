<?php
/**
 * Created by PhpStorm.
 * User: kaushik
 * Date: 20/12/14
 * Time: 11:54 AM
 */

namespace kaushikam\lib\database\adapter;


use kaushikam\lib\database\exception\DatabaseException;
use Psr\Log\LoggerInterface;

interface IDatabaseAdapter {
    /**
     * @return boolean
     */
    public function connect();

    /**
     * @return void
     */
    public function disconnect();

    /**
     * @param $sql
     * @param array $options
     * @throws DatabaseException
     * @return $this
     */
    public function prepare($sql, Array $options = array());

    /**
     * @param array $parameters
     * @throws DatabaseException
     * @return $this
     */
    public function execute(Array $parameters = array());

    public function getStatement();

    /**
     * @return LoggerInterface
     */
    public function getLogger();

    public function setLogger(LoggerInterface $logger);

    /**
     * @return bool
     */
    public function isConnected();
} 