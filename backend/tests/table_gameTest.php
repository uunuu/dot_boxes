<?php
require_once 'table_game.php' ;
require_once 'table_user.php' ;

class table_gameTest extends PHPUnit_Framework_TestCase
{

	public static function setUpBeforeClass()
	{
		user::create_table() ;
		game::create_table() ;
	}

	public static function tearDownAfterClass()
	{
		game::drop_table() ;
		user::drop_table() ;
	}

	protected function setUp()
	{
		game::clear_table() ;
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

		user::create_new_user($username,$password,$email) ;
		$this->assertEquals(1,user::getNumberOfUsers(),"number of users is not correct after adding a new user") ;
		user::create_new_user($username2,$password2,$email2) ;
		$this->assertEquals(2,user::getNumberOfUsers(),"number of users is not correct after adding a new user") ;
		
		$user1ID = user::getUserByUsername($username)['id'] ;
		$user2ID = user::getUserByUsername($username2)['id'] ;
		
		$gmae_size = 6 ;
		$this->assertFalse(game::add_new_game($user1ID,"z",$user1ID,$user2ID),"invalid size went through OK!") ;
		$this->assertFalse(game::add_new_game("s",$gmae_size,$user1ID,$user2ID),"invalid curent player id went through OK!") ;
		$this->assertFalse(game::add_new_game($user1ID,$gmae_size,"x",$user2ID),"invalid player 1 id went through OK!") ;
		$this->assertFalse(game::add_new_game($user1ID,$gmae_size,$user1ID,"d"),"invalid player 2 id went through OK!") ;
		$this->assertFalse(game::add_new_game(20,$gmae_size,$user1ID,$user2ID),"current turn id was different from p1 and p2 and it when through!") ;
		
		$this->assertTrue(game::add_new_game($user1ID,$gmae_size,$user1ID,$user2ID),"failed to add a new game") ;
		
		
		$this->assertEquals($gmae_size,game::getGameById(1)['size'],"incorrect game size") ;
		$this->assertEquals($user1ID,game::getGameById(1)['player1ID'],"incorrect player 1 id ") ;
		$this->assertEquals($user2ID,game::getGameById(1)['player2ID'],"incorrect player 2 id ") ;
		$this->assertNull(game::getGameById(1)['winnerID'],"winnder ID should be null") ;
		$this->assertNull(game::getGameById(1)['lastActivityDate'],"lastActivityDate should be null") ;
		
		
		$this->assertEquals(1,game::getNumberOfGames(),"number of games should be equal to 1") ;
		game::clear_table() ;
		$this->assertEquals(0,game::getNumberOfGames(),"number of games should be equal to 0") ;
		
		
		game::add_new_game($user1ID,$gmae_size,$user1ID,$user2ID) ;
		$game_id = game::$last_inserted_id ;
		$this->assertTrue(game::setLastActivityDate($game_id),"setLastActivityDate is not working") ;
		$this->assertEquals(time()/60 % 60,  game::getGameById($game_id)['lastActivityDate']/60 % 60) ;
		
		game::clear_table() ;
		//security test: passing a user id that is not a number
		$this->assertEquals(-1,game::getNumberOfGamesForUserId("ff"),"getNumberOfGamesForUserId() should accept only numbers") ;
		//the user shouldn't have any games
		$this->assertEquals(0,game::getNumberOfGamesForUserId($user2ID),"The player shouldn't have any games [getNumberOfGamesForUserId()]") ;
		game::add_new_game($user1ID,$gmae_size,$user1ID,$user2ID) ;
		$game_id1 = game::$last_inserted_id ;
		$this->assertEquals(1,game::getNumberOfGamesForUserId($user1ID),"player should have 1 game [getNumberOfGamesForUserId()]") ;
		
		$username3 = "bla3" ;
		$password3 = "pass3" ;
		$email3 = "bla3@gmmail.com" ;
		user::create_new_user($username3,$password3,$email3) ;
		$user3ID = user::getUserByUsername($username3)['id'] ;
		$this->assertEquals(3,user::getNumberOfUsers(),"number of users is not correct after adding a new user") ;
		$this->assertTrue(game::add_new_game($user3ID,$gmae_size,$user3ID,$user1ID),"failed to add a new game") ;
		$game_id2 = game::$last_inserted_id ;
		$this->assertEquals(2,game::getNumberOfGames(),"number of games should be equal to 2") ;
		$this->assertEquals(2,game::getNumberOfGamesForUserId($user1ID),"player should have two games [getNumberOfGamesForUserId()]") ;
		$this->assertEquals(1,game::getNumberOfGamesForUserId($user2ID),"player should have one game [getNumberOfGamesForUserId()]") ;
		$this->assertEquals(1,game::getNumberOfGamesForUserId($user3ID),"player should have one game [getNumberOfGamesForUserId()]") ;
		
		$this->assertEquals(2,count(game::getAllGamesForUserId($user1ID)),"two games should be returned [game::getAllGamesForUserId()]") ;
		$this->assertEquals($user1ID,game::getAllGamesForUserId($user1ID)[0]['player1ID'],"[game::getAllGamesForUserId()]") ;
		$this->assertEquals($user1ID,game::getAllGamesForUserId($user1ID)[1]['player2ID'],"[game::getAllGamesForUserId()]") ;
		$this->assertNull(game::getAllGamesForUserId(88),"should return null because player doesn't have games [game::getAllGamesForUserId()]") ;
		
		$this->assertNull(game::getOpponentId(1000,1000),"[game::getOpponentId]") ;
		$this->assertNull(game::getOpponentId(1000,1000),"[game::getOpponentId]") ;
		$this->assertNull(game::getOpponentId($game_id1,1000),"[game::getOpponentId]") ;
		$this->assertNull(game::getOpponentId(1000,$user1ID),"[game::getOpponentId]") ;
		$this->assertEquals($user2ID,game::getOpponentId($game_id1,$user1ID),"[game::getOpponentId]") ;
		$this->assertEquals($user1ID,game::getOpponentId($game_id1,$user2ID),"[game::getOpponentId]") ;
		$this->assertEquals($user1ID,game::getOpponentId($game_id2,$user3ID),"[game::getOpponentId]") ;
		$this->assertEquals($user3ID,game::getOpponentId($game_id2,$user1ID),"[game::getOpponentId]") ;
		$this->assertNull(game::getOpponentId($game_id2,$user2ID),"[game::getOpponentId]") ;
		
		
		
		
	}

}

?>
