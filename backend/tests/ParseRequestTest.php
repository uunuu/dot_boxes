<?php
require_once 'ParseRequest_class.php' ;
require_once 'table_user.php' ;
require_once 'table_session.php' ;
require_once 'table_pending_game.php' ;
require_once 'table_game.php' ;

class ParseRequestTest extends PHPUnit_Framework_TestCase
{

	public static function setUpBeforeClass()
	{
		user::create_table() ;
		session::create_table() ;
		pending_game::create_table() ;
		game::create_table() ;
	}

	public static function tearDownAfterClass()
	{
		session::drop_table() ;
		pending_game::drop_table() ;
		game::drop_table() ;
		user::drop_table() ;
	}

	protected function setUp()
	{
		session::clear_table() ;
		pending_game::clear_table() ;
		game::clear_table() ;
		user::clear_table() ;
	}

	protected function tearDown()
	{
		
	}

	public function test()
	{
		
		$obj = new XmlParseRequest() ;
		$xmlFile1 = file_get_contents("./files/invalidXmlFile1.xml") ;
		$xmlFile2 = file_get_contents("./files/invalidXmlFile2.xml") ;
		$this->assertFalse($obj->isValidXmlFile($xmlFile1),"isValidXmlFile") ;
		
		$xmlFile2 = file_get_contents("./files/invalidXmlFile2.xml") ;
		$this->assertFalse($obj->isValidXmlFile($xmlFile2),"isValidXmlFile") ;
		
		$xmlFile3 = file_get_contents("./files/validXmlFile.xml") ;
		$this->assertTrue($obj->isValidXmlFile($xmlFile3),"isValidXmlFile") ;
		
		$xmlDocument = simplexml_load_string($xmlFile3);
		$this->assertTrue($obj->isValidRequest($xmlDocument),"isvalidRequest") ;
		
		$obj->processRequest($xmlFile1) ;
		
		$xm1 = simplexml_load_string($obj->getResponse()) ;
		$this->assertTrue(!empty($xm1->xpath("/response/error")),"[processRequest]") ;
		
		$xmlFile4 = file_get_contents("./files/registerNewUserRequest.xml") ;
		$obj->processRequest($xmlFile4) ;
		$xmm = simplexml_load_string($xmlFile4) ;
		$xm2 = simplexml_load_string($obj->getResponse()) ;
		
		$this->assertTrue(!empty($xm2->xpath("/response/body/status")),"[processRequest]") ;
		$this->assertEquals("successful",$xm2->body->status,"[processRequest]") ;
		
		
		$u = (String) $xmm->body->username ;
		$email = (String) $xmm->body->email ;
		$this->assertTrue(user::doesUsernameExist($u),"register new user request[processRequest]") ;
		$user_info = user::getUserByUsername($u) ;
		$this->assertEquals($u,$user_info['username'],"useranme was not add correctly by the request[processRequest]") ;
		$this->assertEquals($email,$user_info['email'],"email was not add correctly by the request[processRequest]") ;
		
		
		
		//newSession()
		$username = "troy0" ;
		$password = "345667" ;
		$email = "troy1970@gmail.com" ;
		$gcm_id = "23546813DFGE56456" ;
		user::create_new_user($username,$password,$email) ;
		$xmlFile = file_get_contents("./files/newSessionRequest.xml") ;
		$p = simplexml_load_string($xmlFile) ;
		$p->body->username = $username ;
		$p->body->password = $password ;
		$p->body->gcm = $gcm_id ;
		$req = $p->asXML();
		$obj = new XmlParseRequest() ;
		$obj->processRequest($req) ;
		$response = $obj->getResponse() ;
		$pr = simplexml_load_string($response) ;
		$user_info = user::getUserByUsername($username) ;
		$session_info = session::get_last_session_for_user_id($user_info['id']) ;
		$this->assertEquals("successful",$pr->body->status,"[new session request]") ;
		$this->assertEquals($session_info['hash'],$pr->body->session,"[new session request]") ;
		$this->assertEquals($gcm_id,$user_info['gcmID'],"[new session request]") ;
		
		$p = simplexml_load_string($xmlFile) ;
		$p->body->password = $password."ddd" ;
		$req = $p->asXML();
		$obj = new XmlParseRequest() ;
		$obj->processRequest($req) ;
		$response = $obj->getResponse() ;
		$pr = simplexml_load_string($response) ;
		$this->assertEquals("failed",$pr->body->status,"[new session request]") ;
		
		
		//endSession()
		session::clear_table() ;
		user::clear_table() ;
		$username = "troy0" ;
		$password = "345667" ;
		$email = "troy1970@gmail.com" ;
		user::create_new_user($username,$password,$email) ;
		$user_info = user::getUserByUsername($username) ;
		$session = md5("dfgfds4543") ;
		session::add_new_session($user_info['id'],$session,"0") ;
		$xmlFile = file_get_contents("./files/endSessionRequest.xml") ;
		$p = simplexml_load_string($xmlFile) ;
		$p->body->session = $session ;
		$req = $p->asXML();
		$obj = new XmlParseRequest() ;
		$obj->processRequest($req) ;
		$response = $obj->getResponse() ;
		$pr = simplexml_load_string($response) ;
		$this->assertEquals("successful",$pr->body->status,"[end session request]") ;
		$this->assertEquals($session,$pr->body->session,"[end session request]") ;
		
		//give an invalid session id
		$obj->processRequest($xmlFile) ;
		$response = $obj->getResponse() ;
		$pr = simplexml_load_string($response) ;
		$this->assertTrue(!empty($pr->xpath("/response/body/reason")),"[end seesion request]") ;
		
		
		
		//registerGCM()
		session::clear_table() ;
		user::clear_table() ;
		$username = "troy0" ;
		$password = "345667" ;
		$email = "troy1970@gmail.com" ;
		
		$gcm = "SDFGa43534" ;
		user::create_new_user($username,$password,$email) ;
		$user_info = user::getUserByUsername($username) ;
		$session = md5("dfgfds4543") ;
		session::add_new_session($user_info['id'],$session,"0") ;
		$xmlFile = file_get_contents("./files/registerGCMRequest.xml") ;
		$p = simplexml_load_string($xmlFile) ;
		$p->body->session = $session ;
		$p->body->gcm = $gcm ;
		$req = $p->asXML();
		$obj = new XmlParseRequest() ;
		$obj->processRequest($req) ;
		$response = $obj->getResponse() ;
		$pr = simplexml_load_string($response) ;
		$this->assertEquals("successful",$pr->body->status,"[register GCM id request]") ;
		$this->assertEquals($session,$pr->body->session,"[register GCM id request]") ;
		$user_info = user::getUserByUsername($username) ;
		$this->assertEquals($gcm,$user_info['gcmID'],"[register GCM id request]") ;
		//test failure
		$p = simplexml_load_string($xmlFile) ;
		$p->body->session = md5("asdssfsfs");
		$req = $p->asXML();
		$obj = new XmlParseRequest() ;
		$obj->processRequest($req) ;
		$response = $obj->getResponse() ;
		$pr = simplexml_load_string($response) ;
		$this->assertEquals("failed",$pr->body->status,"[register GCM id request]") ;
		$this->assertEquals("1",$pr->body->error_code,"[register GCM id request]") ;
		
		
		
		
		
	}
	
	
	public function testNewGameRequest()
	{
		//startNewGame()
		$username = "troy" ;
		$password = "345667" ;
		$email = "troy1970@gmail.com" ;
		
		$username2 = "sandy" ;
		$password2 = "booha" ;
		$email2 = "sandy1111@gmail.com" ;	
		
		user::create_new_user($username,$password,$email) ;
		user::create_new_user($username2,$password2,$email2) ;
		$user_info = user::getUserByUsername($username) ;
		$user_info2 = user::getUserByUsername($username2) ;
		
		$session = md5("dfgfds4543") ;
		$session2 = md5("rtyertyerty") ;
		$this->assertTrue(session::add_new_session($user_info['id'],$session,"0")) ;
		$this->assertTrue(session::add_new_session($user_info2['id'],$session2,"0")) ;
		
		
		$xmlFile = file_get_contents("./files/newPendingGameRequest.xml") ;
		$p = simplexml_load_string($xmlFile) ;
		$p->body->session = $session ;
		$p->body->size = 6 ;
		$req = $p->asXML();
		
		$obj = new XmlParseRequest() ;
		$obj->processRequest($req) ;
		$response = $obj->getResponse() ;
		$pr = simplexml_load_string($response) ;
		$this->assertEquals("successful",$pr->body->status,"[new pending game]") ;
		$this->assertEquals("5",$pr->body->id,"[new pending game]") ;
		$this->assertEquals($session,$pr->body->session,"[new pending game]") ;
		$games = pending_game::get_all_pending_games_for_user_id($user_info['id']) ;
		$this->assertEquals(1,count($games),"[new pending game]") ;
		$this->assertEquals(6,$games[0]['size'],"[new pending game]") ;
		
		//send an invalid session hash
		$p->body->session = md5("invalid_session") ;
		$req = $p->asXML();
		$obj = new XmlParseRequest() ;
		$obj->processRequest($req) ;
		$response = $obj->getResponse() ;
		$pr = simplexml_load_string($response) ;
		$this->assertEquals("failed",$pr->body->status,"[new pending game]") ;
		$this->assertEquals("1",$pr->body->error_code,"error code is not correct (invalid session hash passed)[new pending game]") ;
		
		// add a second pending game fo the same user and check that the system dodn't match the games since they belong to the same user!
		$p->body->session = $session ;
		$req = $p->asXML();
		$obj = new XmlParseRequest() ;
		$obj->processRequest($req) ;
		$response = $obj->getResponse() ;
		$pr = simplexml_load_string($response) ;
		$this->assertEquals("successful",$pr->body->status,"[new pending game]") ;
		
		
		$p->body->session = $session ;
		$req = $p->asXML();
		$obj = new XmlParseRequest() ;
		$obj->processRequest($req) ;
		$response = $obj->getResponse() ;
		$pr = simplexml_load_string($response) ;
		$this->assertEquals("successful",$pr->body->status,"[new pending game]") ;
		
		$obj->processRequest($req) ;
		$response = $obj->getResponse() ;
		$pr = simplexml_load_string($response) ;
		$this->assertEquals("successful",$pr->body->status,"[new pending game]") ;
		
		
		$this->AssertEquals(0,game::getNumberOfGames(),"one pending games with the same size added[new pending game]") ;
		$p->body->session = $session2 ;
		$req = $p->asXML();
		$obj = new XmlParseRequest() ;
		$obj->processRequest($req) ;
		$response = $obj->getResponse() ;
		$pr = simplexml_load_string($response) ;
		$this->assertEquals("successful",$pr->body->status,"[new pending game]") ;
		$this->assertEquals("5",$pr->body->id,"[new pending game]") ;
		$this->assertEquals($session2,$pr->body->session,"[new pending game]") ;
		$this->AssertEquals(1,game::getNumberOfGames(),"two pending games with the same size added[new pending game]") ;
		
		$obj->processRequest($req) ;
		$response = $obj->getResponse() ;
		$pr = simplexml_load_string($response) ;
		$this->assertEquals("successful",$pr->body->status,"[new pending game]") ;
		$this->assertEquals("5",$pr->body->id,"[new pending game]") ;
		$this->assertEquals($session2,$pr->body->session,"[new pending game]") ;
		$this->AssertEquals(2,game::getNumberOfGames(),"two pending games with the same size added[new pending game]") ;
		
	}

}

?>
