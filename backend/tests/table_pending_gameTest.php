<?php
require_once 'table_pending_game.php' ;
require_once 'table_game.php' ;
require_once 'table_user.php' ;
require_once 'safe_input.php' ;

class table_pending_gameTest extends PHPUnit_Framework_TestCase
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
	
	
		//pending_game::add_new_pending_game($user_id,$size) ;
		//pending_game::get_pending_game_by_id($id) ;
		$this->assertFalse(pending_game::add_new_pending_game($user1ID,-1),"[pending_game::add_new_pending_game()]") ;
		$this->assertFalse(pending_game::add_new_pending_game("sdfdsf",3),"[pending_game::add_new_pending_game()]") ;
		$this->assertTrue(pending_game::add_new_pending_game($user1ID,3),"failed to add new pending_game[pending_game::add_new_pending_game()]") ;
		$pg_id = pending_game::$last_inserted_id ;
		$this->assertTrue(pending_game::add_new_pending_game($user2ID,4),"failed to add new pending_game[pending_game::add_new_pending_game()]") ;
		
		$pg_info = pending_game::get_pending_game_by_id($pg_id) ;
		$this->assertEquals(3,$pg_info['size'],'[pending_game::add_new_pending_game()]') ;
		$this->assertEquals($user1ID,$pg_info['userID'],'[pending_game::add_new_pending_game()]') ;
		
		//get_all_pending_games_for_user_id($user_id)
		$this->assertEquals(1,count(pending_game::get_all_pending_games_for_user_id($user1ID)),"[get_all_pending_games_for_user_id()]") ;
		$this->assertTrue(pending_game::add_new_pending_game($user1ID,3),"failed to add new pending_game[pending_game::add_new_pending_game()]") ;
		$this->assertEquals(2,count(pending_game::get_all_pending_games_for_user_id($user1ID)),"[get_all_pending_games_for_user_id()]") ;
		$this->assertNull(pending_game::get_all_pending_games_for_user_id("hhh"),"[get_all_pending_games_for_user_id()]") ;
		$this->assertNull(pending_game::get_all_pending_games_for_user_id($user3ID),"[get_all_pending_games_for_user_id()]") ;
		$this->assertEquals($user1ID,pending_game::get_all_pending_games_for_user_id($user1ID)[0]['userID'],"[get_all_pending_games_for_user_id()]") ;
		
		//get_all_pending_games_by_size($size)
		pending_game::clear_table() ;
		$this->assertTrue(pending_game::add_new_pending_game($user1ID,3),"failed to add new pending_game[pending_game::add_new_pending_game()]") ;
		$pg_id1 = pending_game::$last_inserted_id;
		$this->assertTrue(pending_game::add_new_pending_game($user2ID,3),"failed to add new pending_game[pending_game::add_new_pending_game()]") ;
		$this->assertTrue(pending_game::add_new_pending_game($user1ID,4),"failed to add new pending_game[pending_game::add_new_pending_game()]") ;
		$this->assertEquals(2,count(pending_game::get_all_pending_games_by_size(3)),"[get_all_pending_games_by_size()]") ;
		$this->assertEquals(1,count(pending_game::get_all_pending_games_by_size(4)),"[get_all_pending_games_by_size()]") ;
		$this->assertNull(pending_game::get_all_pending_games_by_size(5),"[get_all_pending_games_by_size()]") ;
		
		//get_all_pending_games()
		$this->assertEquals(3,count(pending_game::get_all_pending_games()),"[get_all_pending_games()]") ;
		//get_count_pending_games()
		$this->assertEquals(3,pending_game::get_count_pending_games(),"[get_count_pending_games()]") ;
		
		//get_count_pending_by_size($size)
		$this->assertEquals(0,pending_game::get_count_pending_by_size(10),"[get_count_pending_by_size()]") ;
		$this->assertEquals(0,pending_game::get_count_pending_by_size("sdfdf"),"[get_count_pending_by_size()]") ;
		$this->assertEquals(2,pending_game::get_count_pending_by_size(3),"[get_count_pending_by_size()]") ;
		$this->assertEquals(1,pending_game::get_count_pending_by_size(4),"[get_count_pending_by_size()]") ;
		
		//pending_game::delete_pending_game_by_id($id)
		$this->assertFalse(pending_game::delete_pending_game_by_id("sds"),"[pending_game::delete_pending_game_by_id()]") ;
		$this->assertEquals($pg_id1,pending_game::get_pending_game_by_id($pg_id1)['id'],"[delete_pending_game_by_id()]") ;
		$this->assertTrue(pending_game::delete_pending_game_by_id($pg_id1),"[pending_game::delete_pending_game_by_id()]") ;
		$this->assertNull(pending_game::get_pending_game_by_id($pg_id1),"[delete_pending_game_by_id()]") ;
		
		//pending_game::delete_all_pending_games_for_user_id($user_id)
		$this->assertFalse(pending_game::delete_all_pending_games_for_user_id("ddd"),"[pending_game::delete_all_pending_games_for_user_id()]") ;
		pending_game::clear_table() ;
		$this->assertTrue(pending_game::add_new_pending_game($user1ID,3),"failed to add new pending_game[pending_game::add_new_pending_game()]") ;
		$this->assertTrue(pending_game::add_new_pending_game($user2ID,3),"failed to add new pending_game[pending_game::add_new_pending_game()]") ;
		$this->assertTrue(pending_game::add_new_pending_game($user1ID,4),"failed to add new pending_game[pending_game::add_new_pending_game()]") ;
		$this->assertEquals(2,count(pending_game::get_all_pending_games_for_user_id($user1ID)),"[get_all_pending_games_for_user_id()]") ;
		$this->assertEquals(3,pending_game::get_count_pending_games(),"[get_count_pending_games()]") ;
		$this->assertTrue(pending_game::delete_all_pending_games_for_user_id($user1ID),"[pending_game::delete_all_pending_games_for_user_id()]") ;
		$this->assertEquals(1,pending_game::get_count_pending_games(),"[get_count_pending_games()]") ;
		$this->assertEquals(1,count(pending_game::get_all_pending_games_for_user_id($user2ID)),"[get_all_pending_games_for_user_id()]") ;
		$this->assertNull(pending_game::get_all_pending_games_for_user_id($user1ID),"[get_all_pending_games_for_user_id()]") ;
		
		
		
		//pending_game::match($game_id1,$game_id2)
		pending_game::clear_table() ;
		$this->assertTrue(pending_game::add_new_pending_game($user1ID,3),"failed to add new pending_game[pending_game::add_new_pending_game()]") ;
		$g1_id = pending_game::$last_inserted_id ;
		$this->assertTrue(pending_game::add_new_pending_game($user2ID,4),"failed to add new pending_game[pending_game::add_new_pending_game()]") ;
		$g2_id = pending_game::$last_inserted_id ;
		$this->assertTrue(pending_game::add_new_pending_game($user1ID,4),"failed to add new pending_game[pending_game::add_new_pending_game()]") ;
		$g3_id = pending_game::$last_inserted_id ;
		$this->assertFalse(pending_game::match($g1_id,$g2_id)) ;
		$this->assertFalse(pending_game::match("d",$g2_id)) ;
		$this->assertFalse(pending_game::match($g1_id,"d")) ;
		$this->assertEquals(0,game::getNumberOfGames(),"[pending_game::mathc()]") ;
		$this->assertEquals(2,pending_game::get_count_pending_by_size(4),"[pending_game::mathc()]") ;
		$this->assertTrue(pending_game::match($g3_id,$g2_id)) ;
		$this->assertEquals(1,game::getNumberOfGames(),"[pending_game::mathc()]") ;
		$this->assertEquals(0,pending_game::get_count_pending_by_size(4),"[pending_game::mathc()]") ;
		$this->assertTrue(!empty(game::getAllGamesForUserId($user1ID)),"[pending_game::mathc()]") ;
		$this->assertTrue(!empty(game::getAllGamesForUserId($user2ID)),"[pending_game::mathc()]") ;
		
		
	}

}

?>
