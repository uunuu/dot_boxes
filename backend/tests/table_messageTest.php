<?php
require_once 'table_message.php' ;
require_once 'table_game.php' ;
require_once 'table_user.php' ;

class table_messageTest extends PHPUnit_Framework_TestCase
{

	public static function setUpBeforeClass()
	{
		user::create_table() ;
		game::create_table() ;
		message::create_table() ;
	}

	public static function tearDownAfterClass()
	{
		message::drop_table() ;
		game::drop_table() ;
		user::drop_table() ;
	}

	protected function setUp()
	{
		message::clear_table() ;
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

		$this->assertTrue(game::add_new_game($user2ID,$game_size,$user2ID,$user1ID),"failed to add a new game") ;
		$game_id2 = game::$last_inserted_id ;
		
		//$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$
		//message::get_all_messages_for_game_id($game_id)
		//message::add_new_message($game_id,$user_id,$message)
		$message = "helllo" ;
		
		$this->assertNull(message::get_all_messages_for_game_id($game_id1),"null should be returned because no messages added for the given game_id [get_all_messages_for_game_id()]") ;
		$this->assertNull(message::get_all_messages_for_game_id("rr"),"null should be returned because an invalid game_id was passed [get_all_messages_for_game_id()]") ;
		$this->assertFalse(message::add_new_message("hhh",$user1ID,$message),"false should be reutnred because an invalid game_id was passed [add_new_message()]") ;
		$this->assertFalse(message::add_new_message($game_id1,"blla",$message),"false should be reutnred because an invalid user_id was passed [add_new_message()]") ;
		$this->assertTrue(message::add_new_message($game_id1,$user1ID,$message),"falied to add new message [add_new_message()]") ;
		$this->assertEquals(1,count(message::get_all_messages_for_game_id($game_id1)),"expecting to get an array with one message [get_all_messages_for_game_id()]") ;
		$messages = message::get_all_messages_for_game_id($game_id1) ;
		$this->assertEquals($message,$messages[0]['message_text'],"[add_new_message()]") ;
		$this->assertEquals($game_id1,$messages[0]['gameID'],"[add_new_message()]") ;
		$this->assertEquals($user1ID,$messages[0]['userID'],"[add_new_message()]") ;
		
		$this->assertTrue(message::add_new_message($game_id2,$user1ID,"yolooo"),"falied to add new message [add_new_message()]") ;
		$message2 = "where are you" ;
		$this->assertTrue(message::add_new_message($game_id1,$user2ID,$message2),"falied to add new message [add_new_message()]") ;
		$this->assertEquals(2,count(message::get_all_messages_for_game_id($game_id1)),"expecting to get an array with two messages [get_all_messages_for_game_id()]") ;
		
		//message::get_message_count($game_id)
		$this->assertEquals(2,message::get_message_count($game_id1),"[message::get_message_count()]") ;
		$this->assertEquals(-1,message::get_message_count("asdasdasd"),"providing an invalid game_id so it should be rejected[message::get_message_count()]") ;
		message::clear_table() ;
		$this->assertEquals(0,message::get_message_count($game_id1),"[message::get_message_count()]") ;
		
		
		$this->assertTrue(message::add_new_message($game_id1,$user1ID,$message),"falied to add new message [add_new_message()]") ;
		$this->assertTrue(message::add_new_message($game_id1,$user2ID,$message2),"falied to add new message [add_new_message()]") ;
		$this->assertTrue(message::add_new_message($game_id2,$user1ID,"yolooo"),"falied to add new message [add_new_message()]") ;
		$this->assertEquals(1,message::get_message_count($game_id2),"[message::get_message_count()]") ;
		
		$messages = message::get_all_messages_for_game_id($game_id1) ;
		
		
		//message::get_all_messages_after_given_date($game_id,$date)
		$this->assertNull(message::get_all_messages_after_given_date(0,0),"get_all_messages_after_given_date()") ;
		$this->assertNull(message::get_all_messages_after_given_date($messages[1]['gameID'],$messages[1]['date']),"get_all_messages_after_given_date()") ;
		$this->assertEquals(1,count(message::get_all_messages_after_given_date($messages[0]['gameID'],$messages[0]['date'])),"get_all_messages_after_given_date()") ;
		$this->assertEquals(2,count(message::get_all_messages_after_given_date($messages[0]['gameID'],$messages[0]['date']-0.1)),"get_all_messages_after_given_date()") ;
		
		//message::delete_message($message_id)
		$this->assertFalse(message::delete_message("dddd"),"[delete_message()]") ;
		$this->assertTrue(message::delete_message($messages[0]['id']),"[delete_message()]") ;
		$this->assertEquals(1,count(message::get_all_messages_after_given_date($messages[0]['gameID'],$messages[0]['date']-0.1)),"get_all_messages_after_given_date()") ;
		//$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$
		
	}

}

?>
