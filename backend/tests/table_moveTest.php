<?php
require_once 'table_move.php' ;
require_once 'table_game.php' ;
require_once 'table_user.php' ;

class table_moveTest extends PHPUnit_Framework_TestCase
{

	public static function setUpBeforeClass()
	{
		user::create_table() ;
		game::create_table() ;
		move::create_table() ;
	}

	public static function tearDownAfterClass()
	{
		move::drop_table() ;
		game::drop_table() ;
		user::drop_table() ;
	}

	protected function setUp()
	{
		move::clear_table() ;
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
		
		$game_size = 6 ;
		$this->assertTrue(game::add_new_game($user1ID,$game_size,$user1ID,$user2ID),"failed to add a new game") ;
		$game_id1 = game::$last_inserted_id ;
		$this->assertFalse(move::add_new_move(100,1,1,1,$user1ID),"accepted invalid game_id [move::add_new_move]") ;
		$this->assertFalse(move::add_new_move($game_id1,-1,1,1,$user1ID),"accepted invalid x coordinate [move::add_new_move]") ;
		$this->assertFalse(move::add_new_move($game_id1,1,-5,1,$user1ID),"accepted invalid y coordinate [move::add_new_move]") ;
		$this->assertFalse(move::add_new_move($game_id1,8,1,1,$user1ID),"accepted invalid x coordinate (larger than the size) [move::add_new_move]") ;
		$this->assertFalse(move::add_new_move($game_id1,1,8,1,$user1ID),"accepted invalid y coordinate (larger than the size) [move::add_new_move]") ;
		$this->assertFalse(move::add_new_move($game_id1,1,1,5,$user1ID),"accepted invalid edge position  [move::add_new_move]") ;
		$this->assertFalse(move::add_new_move($game_id1,1,1,2,90),"accepted invalid playerID (ID doesn't even exist)  [move::add_new_move]") ;
		$this->assertFalse(move::add_new_move($game_id1,1,1,2,$user2ID),"accepted invalid playerID (not the player turn)  [move::add_new_move]") ;
		$game_info = game::getGameById($game_id1) ;
		$this->assertNull($game_info['lastActivityDate'],"when move fails the transaction is not rolledback![move::add_new_move]") ;
		
		$this->assertTrue(move::add_new_move($game_id1,1,1,1,$user1ID),"failed to add new move") ;
		$game_info = game::getGameById($game_id1) ;
		$this->assertTrue( ($game_info['currentTurnPlayerID'] == $user2ID) ,"when adding a new move, method failed to change the player's turn") ;
		
		
		
		//get_last_move_for_game_id($game_id)
		$move = move::get_last_move_for_game_id($game_id1) ;
		$this->assertEquals($game_id1,$move['gameID'],"inccorect last move [get_last_move_for_game_id()]") ;
		$this->assertEquals(1,$move['x'],"inccorect last move [get_last_move_for_game_id()]") ;
		$this->assertEquals(1,$move['y'],"inccorect last move [get_last_move_for_game_id()]") ;
		$this->assertEquals(1,$move['edgePosition'],"inccorect last move [get_last_move_for_game_id()]") ;
		$this->assertEquals($user1ID,$move['playerID'],"inccorect last move [get_last_move_for_game_id()]") ;
		
		
		$this->assertEquals($game_info['lastActivityDate'],$move['date'],"the last activity date in the game table is not consistent with the move table! [move::add_new_move]") ;
		$this->assertTrue(move::add_new_move($game_id1,2,3,4,$user2ID),"failed to add new move") ;
		$move2 = move::get_last_move_for_game_id($game_id1) ;
		$this->assertEquals($user2ID,$move2['playerID'],"inccorect last move [get_last_move_for_game_id()]") ;
		$this->assertEquals(2,$move2['x'],"inccorect last move [get_last_move_for_game_id()]") ;
		$this->assertEquals(3,$move2['y'],"inccorect last move [get_last_move_for_game_id()]") ;
		$this->assertEquals(4,$move2['edgePosition'],"inccorect last move [get_last_move_for_game_id()]") ;
		
		move::add_new_move($game_id1,2,1,4,$user1ID) ;
		move::add_new_move($game_id1,2,2,4,$user2ID);
		$move3 = move::get_last_move_for_game_id($game_id1) ;
		$this->assertEquals($user2ID,$move3['playerID'],"inccorect last move [get_last_move_for_game_id()]") ;
		$this->assertEquals(2,$move3['y'],"inccorect last move [get_last_move_for_game_id()]") ;
		move::clear_table() ;
		$this->assertFalse(move::get_last_move_for_game_id($game_id1),"inccorect last move [get_last_move_for_game_id()]") ;
		
		
		//count_moves($game_id)
		$this->assertEquals(0,move::count_moves($game_id1),"number of moves should be ZERO [count_moves()]") ;
		$this->assertTrue(move::add_new_move($game_id1,2,3,4,$user1ID),"failed to add new move") ;
		$this->assertEquals(1,move::count_moves($game_id1),"number of moves should be ONE [count_moves()]") ;
		
		//get_all_moves_after_given_date($game_id,$timestamp)
		$move4 = move::get_last_move_for_game_id($game_id1) ;
		$this->assertNull(move::get_all_moves_after_given_date($game_id1,$move4['date']),"[move::get_all_moves_after_given_date()]") ;
		$this->assertTrue(move::add_new_move($game_id1,4,3,1,$user2ID),"failed to add new move") ;
		$this->assertEquals(2,move::count_moves($game_id1),"number of moves should be two [count_moves()]") ;
		$move5 = move::get_all_moves_after_given_date($game_id1,$move4['date']) ;
		$this->assertEquals(1,count($move5),"number of moves returned should be one [move::get_all_moves_after_given_date()]") ;
		$this->assertEquals($game_id1,$move5[0]['gameID'],"[move::get_all_moves_after_given_date()]") ;
		$this->assertTrue(move::add_new_move($game_id1,5,5,2,$user1ID),"failed to add new move") ;
		$this->assertEquals(2,count(move::get_all_moves_after_given_date($game_id1,$move4['date'])),"number of moves returned should be two [move::get_all_moves_after_given_date()]") ;
		
		//move::delete_move($move_id)
		$move_id = $move5[0]['gameID'] ;
		move::delete_move($move_id) ;
		$this->assertEquals(1,count(move::get_all_moves_after_given_date($game_id1,$move5[0]['date'])),"[move::delet_move()]") ;
		
		//move::get_all_moves_for_game_id($game_id)
		$this->assertEquals(move::count_moves($game_id1),count(move::get_all_moves_for_game_id($game_id1)),"[move::get_all_moves_for_game_id()]") ;
		$this->assertNull(move::get_all_moves_for_game_id("bla"),"[move::get_all_moves_for_game_id()]") ;
		$this->assertNull(move::get_all_moves_for_game_id("0"),"[move::get_all_moves_for_game_id()]") ;
		
	}

}

?>
