<?php

include_once("database.php") ;
include_once("safe_input.php") ;


class pending_game
{



/*
	#######################################################
	################## MEMBER FUNCTIONS ###################
	#######################################################
	public static function create_table() ;
	public static function drop_table() ;
	public static function clear_table() ;
	public static function add_new_pending_game($user_id,$size) ;
	public static function get_pending_game_by_id($id) ;
	public static function get_all_pending_games_for_user_id($user_id) ;
	public static function get_all_pending_games_by_size($size) ; //returns an array
	public static function get_all_pending_games() ;
	public static function get_count_pending_games() ;
	public static function get_count_pending_by_size($size) ;
	public static function delete_pending_game_by_id($id) ;
	public static function delete_all_pending_games_for_user_id($user_id) ;
	public static function match($game_id1,$game_id2) ;//this method takes two pending games ids and removes them and creates a game record
	#######################################################
	#######################################################

*/

	public static $last_inserted_id = -1 ;

	public static function create_table()
	{
		$query = "CREATE TABLE IF NOT EXISTS `pending_game` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userID` int(10) unsigned NOT NULL,
  `size` int(10) unsigned NOT NULL,
  `date` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `userID` (`userID`)
) ;" ;
		$db = new database() ;

		$s = True ;
		$s = $s && $db->query($query) ;

		$query = "ALTER TABLE `pending_game`
  ADD CONSTRAINT `pending_game_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;" ;
		return  $s && $db->query($query) ;
	}


	public static function drop_table()
	{
		$query = "DROP TABLE `pending_game`" ;
		$db = new database() ;
		return $db->query($query) ;
	}
	
	
	public static function clear_table()
	{
		$query = "DELETE FROM `pending_game`;" ;
		$db = new database() ;
		return $db->query($query) ;
	}
	
	public static function add_new_pending_game($user_id,$size)
	{
		if(safe_input::is_number($user_id) && safe_input::is_number($size) && $size > 1)
		{
			$date = microtime(true) ;
			$query = "INSERT INTO `pending_game` (`userID`, `date`, `size`) VALUES ( '$user_id', '$date', '$size');" ;
			$db = new database() ;
			$res = $db->query($query) ;
			pending_game::$last_inserted_id = $db->insert_id() ;
			
			return $res ;
		}
		else
		{
			return false ;
		}
	}
	
	public static function get_pending_game_by_id($id)
	{
		if(safe_input::is_number($id))
		{
			$query = "SELECT * FROM `pending_game` WHERE `id` = '$id' " ;
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
			return null ;
		}
	}
	
	public static function get_all_pending_games_for_user_id($user_id)
	{
		if(safe_input::is_number($user_id))
		{
			$query = "SELECT * FROM `pending_game` WHERE `userID` = '$user_id' " ;
			$db = new database() ;
			$db->query($query) ;
			if( $db->number_of_rows() > 0)
			{
				while($pgame = $db->fetch_row())
				{
					$pgames[] = $pgame ;
				}
				
				return $pgames ;
			}
			else
			{
				return null ;
			}
		}
		else
		{
			return null ;
		}
	}
	
	
	public static function get_all_pending_games_by_size($size)
	{
		if(safe_input::is_number($size))
		{
			$query = "SELECT * FROM `pending_game` WHERE `size` = '$size' ORDER BY `date` ASC" ;
			$db = new database() ;
			$db->query($query) ;
			if( $db->number_of_rows() > 0)
			{
				while($pgame = $db->fetch_row())
				{
					$pgames[] = $pgame ;
				}
				
				return $pgames ;
			}
			else
			{
				return null ;
			}
		}
		else
		{
			return null ;
		}
	}
	
	
	public static function get_all_pending_games()
	{
		$query = "SELECT * FROM `pending_game`" ;
		$db = new database() ;
		$db->query($query) ;
		if( $db->number_of_rows() > 0)
		{
			while($pgame = $db->fetch_row())
			{
				$pgames[] = $pgame ;
			}
			
			return $pgames ;
		}
		else
		{
			return null ;
		}
	}
	
	public static function get_count_pending_games()
	{
		$query = "SELECT * FROM `pending_game`" ;
		$db = new database() ;
		$db->query($query) ;
		return $db->number_of_rows() ;
	}
	
	
	public static function get_count_pending_by_size($size)
	{
		if(safe_input::is_number($size))
		{
			$query = "SELECT * FROM `pending_game` WHERE `size` = '$size' " ;
			$db = new database() ;
			$db->query($query) ;
			return $db->number_of_rows() ;
		}
		else
		{
			return 0 ;
		}
	}
	
	
	public static function delete_pending_game_by_id($id)
	{
		if(safe_input::is_number($id))
		{
			$query = "DELETE FROM `pending_game` WHERE `id` = '$id' " ;
			$db = new database() ;
			return $db->query($query) ;
		}
		else
		{
			return false ;//invalid id
		}
	}
	
	public static function delete_all_pending_games_for_user_id($user_id)
	{
		if(safe_input::is_number($user_id))
		{
			$query = "DELETE FROM `pending_game` WHERE `userID` = '$user_id' " ;
			$db = new database() ;
			return $db->query($query) ;
		}
		else
		{
			return false ;//invalid id
		}
	}
	
	
	
	public static function match($game_id1,$game_id2)
	{
		$game1 = pending_game::get_pending_game_by_id($game_id1) ;
		$game2 = pending_game::get_pending_game_by_id($game_id2) ;
		
		if(safe_input::is_number($game_id1) && safe_input::is_number($game_id2) && $game1 != null && $game2 != null && $game1['size'] == $game2['size'])
		{
			$date = time() ;
			$player1_id = $game1['userID'] ;
			$player2_id = $game2['userID'] ;
			if($player1_id == $player2_id)
			{
					return false ;
			}
			
			$size = $game1['size'] ;
			$g1_id = $game1['id'] ;
			$g2_id = $game2['id'] ;
			
			$db = new database() ;
			$query[] = "INSERT INTO `game` (`winnerID`, `createDate`, `currentTurnPlayerID`, `size`, `lastActivityDate`, `player1ID` , `player2ID`) VALUES ( null, '$date', '$player1_id', '$size', null , '$player1_id', '$player2_id');" ;
			$query[] = "DELETE FROM `pending_game` WHERE `id` = '$g1_id' or `id` = '$g2_id'" ;
			$res = $db->execute_transaction($query) ;
			
			return $res ;
		}
		else
		{
			return false ;
		}
			
	}
}//end of class



?>
