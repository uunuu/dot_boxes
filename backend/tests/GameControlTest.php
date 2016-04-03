<?php
require_once 'table_pending_game.php' ;
require_once 'table_game.php' ;
require_once 'table_user.php' ;
require_once 'safe_input.php' ;
require_once 'GameControl.php' ;

class GameControlTest extends PHPUnit_Framework_TestCase
{

	public static function setUpBeforeClass()
	{
		user::create_table() ;
		pending_game::create_table() ;
		game::create_table() ;
	}

	public static function tearDownAfterClass()
	{
		game::drop_table() ;
		pending_game::drop_table() ;
		user::drop_table() ;
	}

	protected function setUp()
	{
		game::clear_table() ;
		pending_game::clear_table() ;
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
	
	
		//GameControl::matchPendingGames() ;
		$this->assertTrue(pending_game::add_new_pending_game($user1ID,3),"failed to add new pending_game[pending_game::add_new_pending_game()]") ;
		$g1_id = pending_game::$last_inserted_id ;
		$this->assertTrue(pending_game::add_new_pending_game($user2ID,4),"failed to add new pending_game[pending_game::add_new_pending_game()]") ;
		$g2_id = pending_game::$last_inserted_id ;
		$this->assertTrue(pending_game::add_new_pending_game($user1ID,4),"failed to add new pending_game[pending_game::add_new_pending_game()]") ;
		$g3_id = pending_game::$last_inserted_id ;

		$this->assertEquals(0,game::getNumberOfGames(),"[pending_game::mathc()]") ;
		$this->assertEquals(2,pending_game::get_count_pending_by_size(4),"[pending_game::mathc()]") ;
		GameControl::matchPendingGames() ;
		$this->assertEquals(1,game::getNumberOfGames(),"[pending_game::mathc()]") ;
		$this->assertEquals(1,pending_game::get_count_pending_by_size(3),"[pending_game::mathc()]") ;
		$this->assertEquals(0,pending_game::get_count_pending_by_size(4),"[pending_game::mathc()]") ;
		
		

		$this->assertTrue(pending_game::add_new_pending_game($user2ID,4),"failed to add new pending_game[pending_game::add_new_pending_game()]") ;
		$g4_id = pending_game::$last_inserted_id ;
		$this->assertTrue(pending_game::add_new_pending_game($user1ID,4),"failed to add new pending_game[pending_game::add_new_pending_game()]") ;
		$g5_id = pending_game::$last_inserted_id ;
		$this->assertTrue(pending_game::add_new_pending_game($user2ID,4),"failed to add new pending_game[pending_game::add_new_pending_game()]") ;
		$g6_id = pending_game::$last_inserted_id ;
		$this->assertTrue(pending_game::add_new_pending_game($user1ID,4),"failed to add new pending_game[pending_game::add_new_pending_game()]") ;
		$g7_id = pending_game::$last_inserted_id ;
		
		$this->assertEquals(4,pending_game::get_count_pending_by_size(4),"[pending_game::mathc()]") ;
		GameControl::matchPendingGames() ;
		$this->assertEquals(0,pending_game::get_count_pending_by_size(4),"[pending_game::mathc()]") ;
		$this->assertEquals(3,game::getNumberOfGames(),"[pending_game::mathc()]") ;

		$this->assertTrue(pending_game::add_new_pending_game($user2ID,4),"failed to add new pending_game[pending_game::add_new_pending_game()]") ;
		$g8_id = pending_game::$last_inserted_id ;
		$this->assertTrue(pending_game::add_new_pending_game($user1ID,4),"failed to add new pending_game[pending_game::add_new_pending_game()]") ;
		$g9_id = pending_game::$last_inserted_id ;
		$this->assertTrue(pending_game::add_new_pending_game($user2ID,4),"failed to add new pending_game[pending_game::add_new_pending_game()]") ;
		$g10_id = pending_game::$last_inserted_id ;
		$this->assertTrue(pending_game::add_new_pending_game($user1ID,4),"failed to add new pending_game[pending_game::add_new_pending_game()]") ;
		$g11_id = pending_game::$last_inserted_id ;
		
		$this->assertTrue(pending_game::add_new_pending_game($user2ID,5),"failed to add new pending_game[pending_game::add_new_pending_game()]") ;
		$g12_id = pending_game::$last_inserted_id ;
		$this->assertTrue(pending_game::add_new_pending_game($user1ID,5),"failed to add new pending_game[pending_game::add_new_pending_game()]") ;
		$g13_id = pending_game::$last_inserted_id ;
		$this->assertTrue(pending_game::add_new_pending_game($user2ID,7),"failed to add new pending_game[pending_game::add_new_pending_game()]") ;
		$g14_id = pending_game::$last_inserted_id ;
		$this->assertTrue(pending_game::add_new_pending_game($user1ID,7),"failed to add new pending_game[pending_game::add_new_pending_game()]") ;
		$g15_id = pending_game::$last_inserted_id ;
		$this->assertEquals(3,game::getNumberOfGames(),"[pending_game::mathc()]") ;
		GameControl::matchPendingGames() ;
		$this->assertEquals(7,game::getNumberOfGames(),"[pending_game::mathc()]") ;
		
		
	}

}

?>
