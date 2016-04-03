<?php

include_once("database.php") ;
include_once("safe_input.php") ;


class message
{



/*
	#######################################################
	################## MEMBER FUNCTIONS ###################
	#######################################################
	public static function create_table() ;
	public static function drop_table() ;
	public static function clear_table() ;
	public static function add_new_message($game_id,$user_id,$message) ; //WARNING: method doesn't verify the game id nor the player id!
	public static function get_all_messages_for_game_id($game_id) ;//returns an array of messages or null if none exists
	public static function get_message_count($game_id) ;
	public static function get_all_messages_after_given_date($game_id,$date); //returns an array of messages or null if none exists
	public static function delete_message($message_id) ;
	
	
	#######################################################
	#######################################################

*/

	public static $last_inserted_id = -1;

	public static function create_table()
	{
		$query = "CREATE TABLE IF NOT EXISTS `message` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date` varchar(20) NOT NULL,
  `userID` int(10) unsigned NOT NULL,
  `gameID` int(10) unsigned NOT NULL,
  `message_text` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `userID` (`userID`),
  KEY `gameID` (`gameID`)
) ;" ;
		$db = new database() ;
		$s = True ;
		$s = $s && $db->query($query) ;


		$query = "ALTER TABLE `message`
  ADD CONSTRAINT `message_ibfk_2` FOREIGN KEY (`gameID`) REFERENCES `game` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `message_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;" ;
  
		return  $s && $db->query($query) ;
	}


	public static function drop_table()
	{
		$query = "DROP TABLE `message`" ;
		$db = new database() ;
		return $db->query($query) ;
	}
	
	
	public static function clear_table()
	{
		$query = "DELETE FROM `message`;" ;
		$db = new database() ;
		return $db->query($query) ;
	}
	
	
	public static function add_new_message($game_id,$user_id,$message)
	{
		$safe_message = safe_input::sql_inj($message) ;
		if(safe_input::is_number($game_id) && safe_input::is_number($user_id))
		{
			$date = microtime(true) ;
			$db = new database() ;
			$query = "INSERT INTO `message` (`gameID`, `userID`, `date`, `message_text`) VALUES ( '$game_id', '$user_id', '$date', '$safe_message');" ;
			$res = $db->query($query) ;
			message::$last_inserted_id = $db->insert_id() ;
			return $res ;
		}
		else
		{
			return false ; //invalud input
		}
	}
	
	
	public static function get_all_messages_for_game_id($game_id)
	{
		if(safe_input::is_number($game_id))
		{
			$db = new database() ;
			$db->select("message","*","gameID= $game_id ") ;
			if($db->number_of_rows() > 0)
			{
				$messages ;
				while($message = $db->fetch_row())
				{
					$messages[] = $message ;
				}
				return $messages ;
			}
			else
			{
				return null ;//no messages for the game_id or game_id doesn't exist
			}
		}
		else
		{
			return null; //invalid $game_id
		}
	}
	
	
	public static function get_message_count($game_id)
	{
		if(safe_input::is_number($game_id))
		{
			$db = new database() ;
			$db->select("message","*","gameID= $game_id ") ;
			return $db->number_of_rows() ;
		}
		else
		{
			return -1 ;//invalid game_id
		}
	}
	
	
	public static function get_all_messages_after_given_date($game_id,$date)
	{
		if(safe_input::is_number($game_id) && safe_input::is_number_floating($date))
		{
			$query = "SELECT * FROM `message` where `gameID` = '$game_id' AND `date` > '$date' " ;
			$db = new database() ;
			$res = $db->query($query) ;
			if($db->number_of_rows() <1)
			{
				return null ;//no results to return
			}
			else
			{
				$messages ;
				while($message = $db->fetch_row())
				{
					$messages[] = $message ;
				}
				
				return $messages ;
			}
		}
		else
		{
			return null;//invalid input
		}
	}
	
	
	public static function delete_message($message_id)
	{
		if(safe_input::is_number($message_id))
		{
			$query = "DELETE FROM `message` WHERE `id` = '$message_id' ;" ;
			$db = new database() ;
			$res = $db->query($query) ;
			return $res ;
		}
		else
		{
			return false ;//invalid $message_id
		}
	}
	
}//end of class



?>
