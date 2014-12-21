<?php
/**
 * Created by PhpStorm.
 * User: kaushik
 * Date: 21/12/14
 * Time: 6:30 PM
 */

namespace kaushikam\lib\test;


use kaushikam\lib\config\Config;
use kaushikam\lib\database\adapter\impl\oracle\DatabaseAdapterOCIImpl;
use kaushikam\lib\test\config\impl\TestLibraryConfiguration;
use PHPUnit_Extensions_Database_DataSet_IDataSet;
use PHPUnit_Extensions_Database_DB_IDatabaseConnection;
use rg\injektor\Configuration;
use rg\injektor\DependencyInjectionContainer;

abstract class BaseOracleTestCase extends BaseTestCase {

    private $_connection;

    /**
     * @var \PDO
     */
    private static $_pdo;

    protected function setUp() {
        Config::configureInstance(new TestLibraryConfiguration());
        $this->_config = Config::getConfig();

        $dicConf = new Configuration($this->getConfig()->getOracleDiConfig());
        $this->_dic = new DependencyInjectionContainer($dicConf);

        parent::setUp();
    }

    /**
     * Returns the test database connection.
     *
     * @return PHPUnit_Extensions_Database_DB_IDatabaseConnection
     */
    protected function getConnection()
    {
        if ($this->_connection === NULL) {
            if (self::$_pdo === NULL) {
                self::$_pdo = new \PDO(
                    sprintf( $this->getConfig()->getOracleDSN(), $this->getConfig()->getOracleHost(),
                        $this->getConfig()->getOraclePort(),
                        $this->getConfig()->getOracleServiceName()),
                    $this->_config->getOracleUser(),
                    $this->_config->getOraclePassword()
                );
            }

            $this->_connection = $this->createDefaultDBConnection(self::$_pdo);
        }

        return $this->_connection;
    }

    /**
     * Returns the test dataset.
     *
     * @return PHPUnit_Extensions_Database_DataSet_IDataSet
     */
    protected function getDataSet()
    {
        $dataSet = new \PHPUnit_Extensions_Database_DataSet_CsvDataSet("|");
        /*        $dataSet->addTable("consumer",
                    $this->_config->getDocumentRoot()."/Onlinepayment/Test/fixtures/consumer.csv");
                $dataSet->addTable("bm_bill",
                    $this->_config->getDocumentRoot()."/Onlinepayment/Test/fixtures/bm_bill.csv");*/
        return $dataSet;
    }

} 