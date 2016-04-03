<?php
require_once 'table_log.php' ;
require_once 'table_user.php' ;
require_once 'safe_input.php' ;

class table_logTest extends PHPUnit_Framework_TestCase
{

	public static function setUpBeforeClass()
	{
		user::create_table() ;
		log::create_table() ;
	}

	public static function tearDownAfterClass()
	{
		
		log::drop_table() ;
		user::drop_table() ;
		
		
	}
	
	
	protected function setUp()
	{
		log::clear_table() ;
		user::clear_table() ;
	}

	protected function tearDown()
	{
		
	}

	public function test()
	{
		$username = "howdy" ;
		$password = "123456" ;
		$email    = "gg@gmail.com";
		
		$username2 = "bla2" ;
		$password2 = "pass2" ;
		$email2 = "gr@gmmail.com" ;
		
		$username3 = "gue" ;
		$password3 = "pass3" ;
		$email3  = "eee@gmail.com" ;

		user::create_new_user($username,$password,$email) ;
		$this->assertEquals(1,user::getNumberOfUsers(),"number of users is not correct after adding a new user") ;
		user::create_new_user($username2,$password2,$email2) ;
		$this->assertEquals(2,user::getNumberOfUsers(),"number of users is not correct after adding a new user") ;
		user::create_new_user($username3,$password3,$email3) ;
		$this->assertEquals(3,user::getNumberOfUsers(),"number of users is not correct after adding a new user") ;
		
		$user1ID = user::getUserByUsername($username)['id'] ;
		$user2ID = user::getUserByUsername($username2)['id'] ;
		$user3ID = user::getUserByUsername($username3)['id'] ;

		$uuid1 = "352584060201362" ;
		$this->assertTrue(safe_input::is_valid_uuid($uuid1),"safe_input::is_valid_uuid()") ;
		$ip1 = '196.168.2.16' ;
		$this->assertTrue(safe_input::is_valid_ip($ip1),"safe_input::is_valid_ip()") ;
		
		
		$this->assertEquals(0,log::get_logs_count(),"[get_logs_count()]") ;
		
		$this->assertTrue(log::addNewLog($user1ID,$ip1,$uuid1),"[log::addNewLog()]") ;
		$this->assertEquals(1,log::get_logs_count(),"[get_logs_count()]") ;
		$this->assertTrue(log::addNewLog($user1ID,$ip1,$uuid1),"[log::addNewLog()]") ;
		$this->assertEquals(2,log::get_logs_count(),"[get_logs_count()]") ;
		
		//$this->assertTrue(log::deleteSimilarLogs(),"[deleteSimilarLogs()]") ;
		//$this->assertEquals(1,log::get_logs_count(),"[get_logs_count()]") ;
	}

}

?>
