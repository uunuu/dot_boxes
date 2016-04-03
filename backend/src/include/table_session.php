<?php

include_once("database.php") ;
include_once("safe_input.php") ;


class session
{



/*
	#######################################################
	################## MEMBER FUNCTIONS ###################
	#######################################################
	public static function create_table() ;
	public static function drop_table() ;
	public static function clear_table() ;
	public static function add_new_session($user_id,$hash,$encryption_key); //WARNING: method doesn't check if user_id exists
	public static function get_last_session_for_user_id($user_id) ; //get the session with the bigger date field or null if nothing
	public static function is_unique_hash($hash) //true or false
	public static function does_user_have_session($user_id); //returns true or false
	public static function delete_all_sessions_for_user_id($user_id) ;
	public static function delete_session_by_id($session_id) ;
	public static function delete_session_by_hash($hash)
	public static function get_session_by_hash($hash) ;

	#######################################################
	#######################################################

*/

	public static $last_inserted_id = -1 ;

	public static function create_table()
	{
		$s = True ;

		$query = "CREATE TABLE IF NOT EXISTS `session` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userID` int(10) unsigned NOT NULL,
  `hash` varchar(200) NOT NULL,
  `encryptionKey` varchar(200) NOT NULL,
  `date` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `hash` (`hash`),
  KEY `userID` (`userID`)
) ;" ;
		$db = new database() ;
		$s = $s && $db->query($query) ;

		$query = "ALTER TABLE `session`
  ADD CONSTRAINT `session_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;" ;
		return $s && $db->query($query) ;
	}


	public static function drop_table()
	{
		$query = "DROP TABLE `session`" ;
		$db = new database() ;
		return $db->query($query) ;
	}
	
	public static function clear_table()
	{
		$query = "DELETE FROM `session`;" ;
		$db = new database() ;
		return $db->query($query) ;
	}
	
	public static function add_new_session($user_id,$hash,$encryption_key)
	{
		if(safe_input::is_number($user_id) && safe_input::is_valid_session_hash($hash) && safe_input::is_valid_encryption_key($encryption_key))
		{
			$date = microtime(true) ;
			$safe_hash = safe_input::sql_inj($hash) ;
			$safe_encryption_key = safe_input::sql_inj($encryption_key) ;
			
			$query = "INSERT INTO `session` (`userID`, `date`, `hash`, `encryptionKey`) VALUES ( '$user_id', '$date', '$safe_hash', '$safe_encryption_key');" ;
			$db = new database() ;
			$res = $db->query($query) ;
			session::$last_inserted_id = $db->insert_id() ;
			
			return $res ;
			
		}
		else
		{
			return false ;//invalid input
		}
	}
	
	public static function get_last_session_for_user_id($user_id)
	{
		if(safe_input::is_number($user_id))
		{
			$query = "SELECT * FROM `session` WHERE `userID` = '$user_id' AND `date` >= (select max(`date`) from `session` WHERE `userID` = '$user_id') " ;
			$db = new database() ;
			$db->query($query) ;
			if( $db->number_of_rows() > 0)
			{
				return $db->fetch_row() ;
			}
			else
			{
				return null ;
			}
			
		}
		else
		{
			return null ;//invalid user_id
		}
	}
	
	
	public static function is_unique_hash($hash)
	{
		if(safe_input::is_valid_session_hash($hash))
		{
			$safe_hash = safe_input::sql_inj($hash) ;
			$query = "SELECT * FROM `session` WHERE `hash` = '$safe_hash'" ;
			$db = new database() ;
			$res = $db->query($query) ;
			if( $db->number_of_rows() == 0)
			{
				return true ;
			}
			else
			{
				return false ;
			}
		}
		else
		{
			return false ;
		}
	}
	
	public static function does_user_have_session($user_id)
	{
		if(safe_input::is_number($user_id))
		{
			$query = "SELECT * FROM `session` WHERE `userID` = '$user_id'" ;
			$db = new database() ;
			$db->query($query) ;
			if( $db->number_of_rows() > 0)
			{
				return true ;
			}
			else
			{
				return false ;
			}
			
		}
		else
		{
			return false ;//invalid user_id
		}
	}
	
	
	public static function delete_all_sessions_for_user_id($user_id)
	{
		if(safe_input::is_number($user_id))
		{
			$query = "DELETE FROM `session` WHERE `userID` = '$user_id'" ;
			$db = new database() ;
			return $db->query($query) ;
		}
		else
		{
			return false ;//invalid user_id
		}
	}
	
	public static function delete_session_by_id($session_id)
	{
		if(safe_input::is_number($session_id))
		{
			$query = "DELETE FROM `session` WHERE `id` = '$session_id'" ;
			$db = new database() ;
			return $db->query($query) ;
		}
		else
		{
			return false ;//invalid user_id
		}
	}
	
	public static function delete_session_by_hash($hash)
	{
		if(safe_input::is_valid_session_hash($hash))
		{
			$safe_hash = safe_input::sql_inj($hash) ;
			$query = "DELETE FROM `session` WHERE `hash` = '$safe_hash'" ;
			$db = new database() ;
			return $db->query($query) ;
		}
		else
		{
			return false ;//invalid user_id
		}
	}
	
	
	public static function get_session_by_hash($hash)
	{
		if(safe_input::is_valid_session_hash($hash))
		{
			$safe_hash = safe_input::sql_inj($hash) ;
			$query = "SELECT * FROM `session` WHERE `hash` = '$safe_hash'" ;
			$db = new database() ;
			$db->query($query) ;
			if( $db->number_of_rows() > 0)
			{
				return $db->fetch_row() ;
			}
			else
			{
				return null ;
			}
		}
		else
		{
			return null ;//invalid hash
		}
	}
	
}//end of class



?>
