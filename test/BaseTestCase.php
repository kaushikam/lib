<?php
/**
 * Created by PhpStorm.
 * User: kaushik
 * Date: 14/12/14
 * Time: 1:17 PM
 */

namespace kaushikam\lib\test;


use kaushikam\lib\config\Config;
use kaushikam\lib\database\adapter\impl\mysql\DatabaseAdapterImpl;
use kaushikam\lib\test\config\impl\TestLibraryConfiguration;
use kaushikam\lib\test\config\ITestLibraryConfig;
use Psr\Log\LoggerInterface;
use rg\injektor\Configuration;
use rg\injektor\DependencyInjectionContainer;

abstract class BaseTestCase extends \PHPUnit_Extensions_Database_TestCase {

    /**
     * @var DependencyInjectionContainer
     */
    protected $_dic;

    /**
     * @var LoggerInterface
     */
    protected $_logger;

    /**
     * @var ITestLibraryConfig
     */
    protected $_config;

    private $_connection;

    /**
     * @var \PDO
     */
    private static $_pdo;

    protected function setUp() {
        Config::configureInstance(new TestLibraryConfiguration());
        $this->_config = Config::getConfig();

        $dicConf = new Configuration($this->getConfig()->getDiConfig());
        $this->_dic = new DependencyInjectionContainer($dicConf);

        $this->_logger = $this->getDic()->getInstanceOfClass('Psr\Log\LoggerInterface');

        parent::setUp();
    }

    public function getDic() {
        return $this->_dic;
    }

    /**
     * @return ITestLibraryConfig
     */
    public function getConfig() {
        return $this->_config;
    }

    public function getLogger() {
        return $this->_logger;
    }

    public final function getConnection() {
        if ($this->_connection === NULL) {
            if (self::$_pdo === NULL) {
                self::$_pdo = new \PDO(
                    sprintf(DatabaseAdapterImpl::DSN, $this->getConfig()->getDbName(), $this->getConfig()->getDbHost()),
                    $this->_config->getDbUser(),
                    $this->_config->getDbPasword()
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