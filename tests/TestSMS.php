<?php

/**	
 * Test the SMS Mo API
 * Best to run with the custom phpunit.xml file
 * phpunit TestSMS --configuration="phpunit.xml"
 */

require '../web/functions.php';

class SMSTest extends PHPUnit_Extensions_Database_TestCase
{
	
	public $fixtures = array(
			'posts',
			'postmeta',
			'options'
	);
	
	static private $pdo = null;
	
	private $conn = null;
	
	protected function setUp()
	{
		$this->object = new SMS;
	}
	
    /**
     * @return PHPUnit_Extensions_Database_DB_IDatabaseConnection
     */

    protected function getConnection()
    {
        if ($this->conn === null) {
            if (self::$pdo == null) {
                self::$pdo = new PDO($GLOBALS['DB_DSN'], $GLOBALS['DB_USER'], $GLOBALS['DB_PASSWD']);
            }
            $this->conn = $this->createDefaultDBConnection(self::$pdo, 'eliot_testing');
        }
        return $this->conn;
    }
	

    /**
     * @return PHPUnit_Extensions_Database_DataSet_IDataSet
     */
    protected function getDataSet()
    {
        return $this->createMySQLXMLDataSet('./test.xml');
    }
    
    
    /**
     * Test Database Connection
     */
    
    public function testDataBaseConnection()
    {
    
    	$this->getConnection()->createDataSet(array('mo'));
    	$prod = $this->getDataSet();
    	$queryTable = $this->getConnection()->createQueryTable(
    			'mo', "SELECT * FROM mo WHERE id IN (999999,10004498) LIMIT 2"
    	);
    	$expectedTable = $this->getDataSet()->getTable('mo');
    	//Here we check that the table in the database matches the data in the XML file
    	$this->assertTablesEqual($expectedTable, $queryTable);
    	echo PHP_EOL.'Test 1: (Test Database Connection): {"status": "ok"}'.PHP_EOL;
    }
    
    /**	
     * Test Save Function
     */
    
    public function testSave() {
        $sms = new SMS();
        $token = $sms->get_auth_token();
        $date  = date('Y-m-d H:i:s');
        $sms->save('00001111','1','2','testing',$date,$token);

        $resultingTable = $this->getConnection()
            ->createQueryTable("mo",
            "SELECT msisdn,operatorid,shortcodeid,text FROM mo WHERE msisdn = '00001111' and text = 'testing' LIMIT 1");
        
        $expectedTable = $this->createXmlDataSet(
            "expected_data.xml")
            ->getTable("mo");
        $this->assertTablesEqual($expectedTable,
            $resultingTable);   
        echo 'Test 2: (Saving to Database): {"status": "ok"}'.PHP_EOL;
    }
    
    /**
     * Test Unprocessed Remove Function
     */    
    
    public function testUnprocessed() {
    	$sms = new SMS();
    	$sms->unprocessed();
    	echo 'Test 3: (Unprocessed MO): {"status": "ok"}'.PHP_EOL;
    }    
    
    public function testUnprocessedRemove() {
    	$sms = new SMS();
    	$sms->unprocessed_remove();
    	echo 'Test 4: (Removing Unprocessed MO from Database): {"status": "ok"}'.PHP_EOL;
    }    
}
?>