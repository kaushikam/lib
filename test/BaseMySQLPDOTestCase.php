<?php
/**
 * Created by PhpStorm.
 * User: kaushik
 * Date: 21/12/14
 * Time: 5:59 PM
 */

namespace kaushikam\lib\test;


use kaushikam\lib\config\Config;
use kaushikam\lib\database\adapter\impl\mysql\DatabaseAdapterImpl;
use kaushikam\lib\test\config\impl\TestLibraryConfiguration;
use rg\injektor\Configuration;
use rg\injektor\DependencyInjectionContainer;

abstract class BaseMySQLPDOTestCase extends BaseTestCase {

    private $_connection;

    /**
     * @var \PDO
     */
    private static $_pdo;

    protected function setUp() {
        $this->_config = Config::configureInstance(new TestLibraryConfiguration());

        $dicConf = new Configuration($this->getConfig()->getDiConfig());
        $this->_dic = new DependencyInjectionContainer($dicConf);

        parent::setUp();
    }

    public final function getConnection() {
        if ($this->_connection === NULL) {
            if (self::$_pdo === NULL) {
                self::$_pdo = new \PDO(
                    sprintf(DatabaseAdapterImpl::DSN, $this->getConfig()->getMysqlDbName(), $this->getConfig()->getMysqlHost()),
                    $this->_config->getMysqlUser(),
                    $this->_config->getMysqlPasword()
                );
            }

            $this->_connection = $this->createDefaultDBConnection(self::$_pdo);
        }

        return $this->_connection;
    }

    public function getDataset() {
        $dataSet = new \PHPUnit_Extensions_Database_DataSet_CsvDataSet("|");
        /*        $dataSet->addTable("consumer",
                    $this->_config->getDocumentRoot()."/Onlinepayment/Test/fixtures/consumer.csv");
                $dataSet->addTable("bm_bill",
                    $this->_config->getDocumentRoot()."/Onlinepayment/Test/fixtures/bm_bill.csv");*/
        return $dataSet;
    }
} 