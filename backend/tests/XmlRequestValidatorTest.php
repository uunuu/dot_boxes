<?php
require_once 'XmlRequestValidator.php' ;


class XmlRequestValidatorTest extends PHPUnit_Framework_TestCase
{

	public static function setUpBeforeClass()
	{

	}

	public static function tearDownAfterClass()
	{

	}

	protected function setUp()
	{

	}

	protected function tearDown()
	{
		
	}

	public function test()
	{
		$xmlFile1 = file_get_contents("./files/registerNewUserRequest.xml") ;
		$p1 = simplexml_load_string($xmlFile1) ;
		
		//isValidReigsterNewUerRequest
		$this->assertTrue(XmlRequestValidator::isValidReigsterNewUerRequest($p1),"[isValidReigsterNewUerRequest]") ;
		unset($p1->body->email) ;
		$this->assertFalse(XmlRequestValidator::isValidReigsterNewUerRequest($p1),"[isValidReigsterNewUerRequest]") ;
		
		$p1 = simplexml_load_string($xmlFile1) ;
		$this->assertTrue(XmlRequestValidator::isValidReigsterNewUerRequest($p1),"[isValidReigsterNewUerRequest]") ;
		unset($p1->body->username) ;
		$this->assertFalse(XmlRequestValidator::isValidReigsterNewUerRequest($p1),"[isValidReigsterNewUerRequest]") ;
		$p1 = simplexml_load_string($xmlFile1) ;
		$this->assertTrue(XmlRequestValidator::isValidReigsterNewUerRequest($p1),"[isValidReigsterNewUerRequest]") ;
		unset($p1->body->password) ;
		$this->assertFalse(XmlRequestValidator::isValidReigsterNewUerRequest($p1),"[isValidReigsterNewUerRequest]") ;
		$p1 = simplexml_load_string($xmlFile1) ;
		$this->assertTrue(XmlRequestValidator::isValidReigsterNewUerRequest($p1),"[isValidReigsterNewUerRequest]") ;
		$p1->body->username = "" ;
		$this->assertFalse(XmlRequestValidator::isValidReigsterNewUerRequest($p1),"[isValidReigsterNewUerRequest]") ;
		$p1 = simplexml_load_string($xmlFile1) ;
		$this->assertTrue(XmlRequestValidator::isValidReigsterNewUerRequest($p1),"[isValidReigsterNewUerRequest]") ;
		$p1->body->password = "" ;
		$this->assertFalse(XmlRequestValidator::isValidReigsterNewUerRequest($p1),"[isValidReigsterNewUerRequest]") ;
		$p1 = simplexml_load_string($xmlFile1) ;
		$this->assertTrue(XmlRequestValidator::isValidReigsterNewUerRequest($p1),"[isValidReigsterNewUerRequest]") ;
		$p1->body->email = "" ;
		$this->assertFalse(XmlRequestValidator::isValidReigsterNewUerRequest($p1),"[isValidReigsterNewUerRequest]") ;
		
		//isValidNewSessionRequest($request)
		$xmlFile2 = file_get_contents("./files/newSessionRequest.xml") ;
		$p2 = simplexml_load_string($xmlFile2) ;
		$this->assertTrue(XmlRequestValidator::isValidNewSessionRequest($p2),"[isValidNewSessionRequest()]") ;
		unset($p2->body->username) ;
		$this->assertFalse(XmlRequestValidator::isValidNewSessionRequest($p2),"[isValidNewSessionRequest]") ;
		$p2 = simplexml_load_string($xmlFile2) ;
		$this->assertTrue(XmlRequestValidator::isValidNewSessionRequest($p2),"[isValidNewSessionRequest]") ;
		unset($p2->body->password) ;
		$this->assertFalse(XmlRequestValidator::isValidNewSessionRequest($p2),"[isValidNewSessionRequest]") ;
		$p2 = simplexml_load_string($xmlFile2) ;
		$this->assertTrue(XmlRequestValidator::isValidNewSessionRequest($p2),"[isValidNewSessionRequest]") ;
		$p2->body->username = "" ;
		$this->assertFalse(XmlRequestValidator::isValidNewSessionRequest($p2),"[isValidNewSessionRequest]") ;
		$p2 = simplexml_load_string($xmlFile2) ;
		$this->assertTrue(XmlRequestValidator::isValidNewSessionRequest($p2),"[isValidNewSessionRequest]") ;
		$p2->body->password = "" ;
		$this->assertFalse(XmlRequestValidator::isValidNewSessionRequest($p2),"[isValidNewSessionRequest]") ;
		$p2 = simplexml_load_string($xmlFile2) ;
		$this->assertTrue(XmlRequestValidator::isValidNewSessionRequest($p2),"[isValidNewSessionRequest]") ;
		unset($p2->body->gcm) ;
		$this->assertFalse(XmlRequestValidator::isValidNewSessionRequest($p2),"[isValidNewSessionRequest]") ;
		$p2 = simplexml_load_string($xmlFile2) ;
		$this->assertTrue(XmlRequestValidator::isValidNewSessionRequest($p2),"[isValidNewSessionRequest]") ;
		$p2->body->gcm = "" ;
		$this->assertFalse(XmlRequestValidator::isValidNewSessionRequest($p2),"[isValidNewSessionRequest]") ;
		
		
		//isValidEndSessionRequest($request)
		$xmlFile2 = file_get_contents("./files/endSessionRequest.xml") ;
		$p2 = simplexml_load_string($xmlFile2) ;
		$this->assertTrue(XmlRequestValidator::isValidEndSessionRequest($p2),"[isValidEndSessionRequest()]") ;
		unset($p2->body->session) ;
		$this->assertFalse(XmlRequestValidator::isValidEndSessionRequest($p2),"[isValidEndSessionRequest()]") ;
		
		//isValidregisterGCMRequest($request)
		$xmlFile2 = file_get_contents("./files/registerGCMRequest.xml") ;
		$p2 = simplexml_load_string($xmlFile2) ;
		$this->assertTrue(XmlRequestValidator::isValidregisterGCMRequest($p2),"[isValidregisterGCMRequest()]") ;
		unset($p2->body->session) ;
		$this->assertFalse(XmlRequestValidator::isValidregisterGCMRequest($p2),"[isValidregisterGCMRequest()]") ;
		$p2 = simplexml_load_string($xmlFile2) ;
		unset($p2->body->gcm) ;
		$this->assertFalse(XmlRequestValidator::isValidregisterGCMRequest($p2),"[isValidregisterGCMRequest()]") ;
		
		//isValidStartPendingGameRequest($request)
		$xmlFile2 = file_get_contents("./files/startPendingGameRequest.xml") ;
		$p2 = simplexml_load_string($xmlFile2) ;
		$this->assertTrue(XmlRequestValidator::isValidStartPendingGameRequest($p2),"[isValidStartPendingGameRequest()]") ;
		unset($p2->body->session) ;
		$this->assertFalse(XmlRequestValidator::isValidStartPendingGameRequest($p2),"[isValidStartPendingGameRequest()]") ;
		$p2 = simplexml_load_string($xmlFile2) ;
		unset($p2->body->size) ;
		$this->assertFalse(XmlRequestValidator::isValidStartPendingGameRequest($p2),"[isValidStartPendingGameRequest()]") ;
		
		
	}

}

?>
