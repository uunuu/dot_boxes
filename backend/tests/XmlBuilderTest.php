<?php
require_once 'XmlBuilder.php' ;


class XmlBuilderTest extends PHPUnit_Framework_TestCase
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
		$v1 = XmlBuilder::general_error(50,"hello") ;
		$p1 = simplexml_load_string($v1) ;
		$this->assertTrue(!empty($p1->xpath("/response")),"[XmlBuilder::general_error()]") ;
		$this->assertTrue(!empty($p1->xpath("/response/error")),"[XmlBuilder::general_error()]") ;
		$this->assertTrue(!empty($p1->xpath("/response/error/code")),"[XmlBuilder::general_error()]") ;
		$this->assertTrue(!empty($p1->xpath("/response/error/message")),"[XmlBuilder::general_error()]") ;
		$this->assertEquals("50",$p1->error->code,"[XmlBuilder::general_error()]") ;
		$this->assertEquals("hello",$p1->error->message,"[XmlBuilder::general_error()]") ;
		
		
		$v2 = XmlBuilder::failed_response("plain","1","200","bla") ;
		$p2 = simplexml_load_string($v2) ;
		$this->assertTrue(!empty($p2->xpath("/response")),"[XmlBuilder::failed_response()]") ;
		$this->assertTrue(!empty($p2->xpath("/response/header")),"[XmlBuilder::failed_response()]") ;
		$this->assertTrue(!empty($p2->xpath("/response/header/version")),"[XmlBuilder::failed_response()]") ;
		$this->assertEquals("1",$p2->header->version,"[XmlBuilder::failed_response()]") ;
		$this->assertEquals("plain",$p2->header['mode'],"[XmlBuilder::failed_response()]") ;
		$this->assertTrue(!empty($p2->xpath("/response/body")),"[XmlBuilder::failed_response()]") ;
		$this->assertTrue(!empty($p2->xpath("/response/body/id")),"[XmlBuilder::failed_response()]") ;
		$this->assertEquals("1",$p2->body->id,"[XmlBuilder::failed_response()]") ;
		$this->assertTrue(!empty($p2->xpath("/response/body/status")),"[XmlBuilder::failed_response()]") ;
		$this->assertEquals("failed",$p2->body->status,"[XmlBuilder::failed_response()]") ;
		$this->assertTrue(!empty($p2->xpath("/response/body/error_code")),"[XmlBuilder::failed_response()]") ;
		$this->assertEquals("200",$p2->body->error_code,"[XmlBuilder::failed_response()]") ;
		$this->assertTrue(!empty($p2->xpath("/response/body/reason")),"[XmlBuilder::failed_response()]") ;
		$this->assertEquals("bla",$p2->body->reason,"[XmlBuilder::failed_response()]") ;
		
		
		
		//registerNewUserSuccessfullResponse($mode)
		$v3 = XmlBuilder::registerNewUserSuccessfullResponse("plain") ;
		$p3 = simplexml_load_string($v3) ;
		$this->assertTrue(!empty($p3->xpath("/response")),"[XmlBuilder::registerNewUserSuccessfullResponse()]") ;
		$this->assertTrue(!empty($p3->xpath("/response/header/version")),"[XmlBuilder::registerNewUserSuccessfullResponse()]") ;
		$this->assertEquals("1",$p3->header->version,"[XmlBuilder::registerNewUserSuccessfullResponse()]") ;
		$this->assertEquals("plain",$p3->header['mode'],"[XmlBuilder::registerNewUserSuccessfullResponse()]") ;
		$this->assertTrue(!empty($p3->xpath("/response/body")),"[XmlBuilder::registerNewUserSuccessfullResponse()]") ;
		$this->assertTrue(!empty($p3->xpath("/response/body/id")),"[XmlBuilder::registerNewUserSuccessfullResponse()]") ;
		$this->assertEquals("1",$p3->body->id,"[XmlBuilder::registerNewUserSuccessfullResponse()]") ;
		$this->assertTrue(!empty($p3->xpath("/response/body/status")),"[XmlBuilder::registerNewUserSuccessfullResponse()]") ;
		$this->assertEquals("successful",$p3->body->status,"[XmlBuilder::registerNewUserSuccessfullResponse()]") ;
		
		//newSessionSuccessfullResponse($mode,$hash)
		$hash = "whatever" ;
		$v3 = XmlBuilder::newSessionSuccessfullResponse("plain",$hash) ;
		$p3 = simplexml_load_string($v3) ;
		$this->assertTrue(!empty($p3->xpath("/response")),"[XmlBuilder::newSessionSuccessfullResponse()]") ;
		$this->assertTrue(!empty($p3->xpath("/response/header/version")),"[XmlBuilder::newSessionSuccessfullResponse()]") ;
		$this->assertEquals("1",$p3->header->version,"[XmlBuilder::newSessionSuccessfullResponse()]") ;
		$this->assertEquals("plain",$p3->header['mode'],"[XmlBuilder::newSessionSuccessfullResponse()]") ;
		$this->assertTrue(!empty($p3->xpath("/response/body")),"[XmlBuilder::newSessionSuccessfullResponse()]") ;
		$this->assertTrue(!empty($p3->xpath("/response/body/id")),"[XmlBuilder::newSessionSuccessfullResponse()]") ;
		$this->assertEquals("2",$p3->body->id,"[XmlBuilder::newSessionSuccessfullResponse()]") ;
		$this->assertTrue(!empty($p3->xpath("/response/body/status")),"[XmlBuilder::newSessionSuccessfullResponse()]") ;
		$this->assertEquals("successful",$p3->body->status,"[XmlBuilder::newSessionSuccessfullResponse()]") ;
		$this->assertTrue(!empty($p3->xpath("/response/body/session")),"[XmlBuilder::newSessionSuccessfullResponse()]") ;
		$this->assertEquals($hash,$p3->body->session,"[XmlBuilder::newSessionSuccessfullResponse()]") ;	
		
		
		//newSessionSuccessfullResponse($mode,$hash)
		$hash = "whatever" ;
		$v3 = XmlBuilder::endSessionSuccessfullResponse("plain",$hash) ;
		$p3 = simplexml_load_string($v3) ;
		$this->assertTrue(!empty($p3->xpath("/response")),"[XmlBuilder::newSessionSuccessfullResponse()]") ;
		$this->assertTrue(!empty($p3->xpath("/response/header/version")),"[XmlBuilder::newSessionSuccessfullResponse()]") ;
		$this->assertEquals("1",$p3->header->version,"[XmlBuilder::newSessionSuccessfullResponse()]") ;
		$this->assertEquals("plain",$p3->header['mode'],"[XmlBuilder::newSessionSuccessfullResponse()]") ;
		$this->assertTrue(!empty($p3->xpath("/response/body")),"[XmlBuilder::newSessionSuccessfullResponse()]") ;
		$this->assertTrue(!empty($p3->xpath("/response/body/id")),"[XmlBuilder::newSessionSuccessfullResponse()]") ;
		$this->assertEquals("3",$p3->body->id,"[XmlBuilder::newSessionSuccessfullResponse()]") ;
		$this->assertTrue(!empty($p3->xpath("/response/body/status")),"[XmlBuilder::newSessionSuccessfullResponse()]") ;
		$this->assertEquals("successful",$p3->body->status,"[XmlBuilder::newSessionSuccessfullResponse()]") ;
		$this->assertTrue(!empty($p3->xpath("/response/body/session")),"[XmlBuilder::newSessionSuccessfullResponse()]") ;
		$this->assertEquals($hash,$p3->body->session,"[XmlBuilder::newSessionSuccessfullResponse()]") ;	
		
		
		
		//registerGCMSuccessfullResponse($mode,$session)
		$hash = "whatever" ;
		$v3 = XmlBuilder::registerGCMSuccessfullResponse("plain",$hash) ;
		$p3 = simplexml_load_string($v3) ;
		$this->assertTrue(!empty($p3->xpath("/response")),"[XmlBuilder::registerGCMSuccessfullResponse()]") ;
		$this->assertTrue(!empty($p3->xpath("/response/header/version")),"[XmlBuilder::registerGCMSuccessfullResponse()]") ;
		$this->assertEquals("1",$p3->header->version,"[XmlBuilder::registerGCMSuccessfullResponse()]") ;
		$this->assertEquals("plain",$p3->header['mode'],"[XmlBuilder::registerGCMSuccessfullResponse()]") ;
		$this->assertTrue(!empty($p3->xpath("/response/body")),"[XmlBuilder::registerGCMSuccessfullResponse()]") ;
		$this->assertTrue(!empty($p3->xpath("/response/body/id")),"[XmlBuilder::registerGCMSuccessfullResponse()]") ;
		$this->assertEquals("4",$p3->body->id,"[XmlBuilder::registerGCMSuccessfullResponse()]") ;
		$this->assertTrue(!empty($p3->xpath("/response/body/status")),"[XmlBuilder::registerGCMSuccessfullResponse()]") ;
		$this->assertEquals("successful",$p3->body->status,"[XmlBuilder::registerGCMSuccessfullResponse()]") ;
		$this->assertTrue(!empty($p3->xpath("/response/body/session")),"[XmlBuilder::registerGCMSuccessfullResponse()]") ;
		$this->assertEquals($hash,$p3->body->session,"[XmlBuilder::registerGCMSuccessfullResponse()]") ;	
		
		
		
		//startNewPendingGameSuccessfullResponse($mode,$session)
		$hash = "whatever" ;
		$v3 = XmlBuilder::startNewPendingGameSuccessfullResponse("plain",$hash) ;
		$p3 = simplexml_load_string($v3) ;
		$this->assertTrue(!empty($p3->xpath("/response")),"[XmlBuilder::startNewPendingGameSuccessfullResponse()]") ;
		$this->assertTrue(!empty($p3->xpath("/response/header/version")),"[XmlBuilder::startNewPendingGameSuccessfullResponse()]") ;
		$this->assertEquals("1",$p3->header->version,"[XmlBuilder::startNewPendingGameSuccessfullResponse()]") ;
		$this->assertEquals("plain",$p3->header['mode'],"[XmlBuilder::startNewPendingGameSuccessfullResponse()]") ;
		$this->assertTrue(!empty($p3->xpath("/response/body")),"[XmlBuilder::startNewPendingGameSuccessfullResponse()]") ;
		$this->assertTrue(!empty($p3->xpath("/response/body/id")),"[XmlBuilder::startNewPendingGameSuccessfullResponse()]") ;
		$this->assertEquals("5",$p3->body->id,"[XmlBuilder::startNewPendingGameSuccessfullResponse()]") ;
		$this->assertTrue(!empty($p3->xpath("/response/body/status")),"[XmlBuilder::startNewPendingGameSuccessfullResponse()]") ;
		$this->assertEquals("successful",$p3->body->status,"[XmlBuilder::startNewPendingGameSuccessfullResponse()]") ;
		$this->assertTrue(!empty($p3->xpath("/response/body/session")),"[XmlBuilder::startNewPendingGameSuccessfullResponse()]") ;
		$this->assertEquals($hash,$p3->body->session,"[XmlBuilder::startNewPendingGameSuccessfullResponse()]") ;
	}

}

?>
