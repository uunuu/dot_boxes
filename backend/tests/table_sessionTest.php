<?php
require_once 'table_session.php' ;
require_once 'table_user.php' ;
require_once 'safe_input.php' ;

class table_sessionTest extends PHPUnit_Framework_TestCase
{

	public static function setUpBeforeClass()
	{
		user::create_table() ;
		session::create_table() ;
	}

	public static function tearDownAfterClass()
	{
		session::drop_table() ;
		user::drop_table() ;
	}

	protected function setUp()
	{
		session::clear_table() ;
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
	
		$this->assertTrue(safe_input::is_valid_session_hash(md5("$3dfsd43^^%")),"safe_input::is_valid_session_hash()") ;
		$this->assertFalse(safe_input::is_valid_session_hash("x = 2 "),"safe_input::is_valid_session_hash()") ;
		
		//get_last_session_for_user_id($user_id)
		//add_new_session($user_id,$hash,$encryption_key)
		$this->assertFalse(session::add_new_session("hh",md5("bla"),md5("bla2")),"[add_new_session()]") ;
		$this->assertTrue(session::add_new_session($user1ID,md5("bla"),md5("bla2")),"failed to add new session[add_new_session()]") ;
		$s1 = session::$last_inserted_id ;
		$this->assertFalse(session::is_unique_hash(md5("bla")),"[session::is_unique_hash()]") ;
		$this->assertTrue(session::is_unique_hash(md5("bddla")),"[session::is_unique_hash()]") ;
		//$this->assertNull(session::add_new_session($user2ID,md5("bla"),md5("mmm")),"Hash has to be unique! [add_new_session()]") ;
		$this->assertTrue(session::add_new_session($user2ID,md5("hash2"),md5("bla2")),"failed to add new session[add_new_session()]") ;
		$this->assertTrue(session::add_new_session($user1ID,md5("hash2sss"),md5("blssssa2")),"failed to add new session[add_new_session()]") ;
		$s2 = session::$last_inserted_id ;
		
		$s_info = session::get_last_session_for_user_id($user1ID) ;
		$this->assertEquals($s_info['encryptionKey'],md5("blssssa2"),"[get_last_session_for_user_id()]") ;
		
		$this->assertFalse(session::is_unique_hash($s_info['hash']),"[is_unique_hash()]") ;
		$this->assertTrue(session::is_unique_hash(md5("asdfasefds")),"[is_unique_hash()]") ;
		
		$this->assertTrue(session::does_user_have_session($user2ID),"[does_user_have_session()]") ;
		$this->assertFalse(session::does_user_have_session($user3ID),"[does_user_have_session()]") ;
		
		//delete_all_sessions_for_user_id($user_id)
		$this->assertFalse(session::delete_all_sessions_for_user_id("sadsadsad"),"[delete_all_sessions_for_user_id()]") ;
		$this->assertTrue(session::delete_all_sessions_for_user_id($user2ID),"[delete_all_sessions_for_user_id()]") ;
		$this->assertFalse(session::does_user_have_session($user2ID),"[does_user_have_session()]") ;
		$this->assertTrue(session::does_user_have_session($user1ID),"[does_user_have_session()]") ;
		
		//session::delete_session_by_id($session_id)
		$s_info = session::get_last_session_for_user_id($user1ID) ;
		$this->assertTrue(session::add_new_session($user2ID,md5("hash2"),md5("bla2")),"failed to add new session[add_new_session()]") ;
		$s2_info = session::get_last_session_for_user_id($user2ID) ;
		$this->assertTrue(session::delete_session_by_id($s2_info['id']),"[delete_session_by_id()]") ;
		$this->assertFalse(session::does_user_have_session($user2ID),"[does_user_have_session()]") ;
		$this->assertTrue(session::does_user_have_session($user1ID),"[does_user_have_session()]") ;
		
		
		
		//session::delete_session_by_hash($hash)
		$s_info = session::get_last_session_for_user_id($user1ID) ;
		$this->assertTrue(session::add_new_session($user2ID,md5("hash2"),md5("bla2")),"failed to add new session[add_new_session()]") ;
		$s2_info = session::get_last_session_for_user_id($user2ID) ;
		$this->assertTrue(session::delete_session_by_hash($s2_info['hash']),"[delete_session_by_id()]") ;
		$this->assertFalse(session::does_user_have_session($user2ID),"[does_user_have_session()]") ;
		$this->assertTrue(session::does_user_have_session($user1ID),"[does_user_have_session()]") ;
		
		
		
		//session::get_session_by_hash($hash)
		$s_infos = session::get_session_by_hash($s_info['hash']) ;
		$this->assertEquals($s_info['id'],$s_infos['id'],"session::get_session_by_hash()") ;
		
		
		
	}

}

?>
