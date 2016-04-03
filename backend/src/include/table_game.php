<?php

include_once("database.php") ;
include_once("safe_input.php") ;


class game
{



/*
	#######################################################
	################## MEMBER FUNCTIONS ###################
	#######################################################
	public static function create_table() ;
	public static function drop_table() ;
	public static function clear_table() ;
	public static function add_new_game($current_turn_player_id,$size,$player1ID,$player2ID) ; //this method doesn't check if given player ids exist!
	public static function getGameById($game_id) // returns info in an array
	public static function getNumberOfGames() ; //return number of rows
	public static function setLastActivityDate($game_id) ; // sets last activity date to now
	public static function getNumberOfGamesForUserId($user_id) ; //returns the number of games associated with the user id
	public static function getAllGamesForUserId($user_id) ; //returns an array of the games  or null if no games exist
	public static function getOpponentId($game_id,$player_id) ; //returns the other player's id
	
	
	
	
	public static function doesWinnerExist($game_id) ; // returns true if winnerID is not null
	public static function switchCurrentTurn($game_id) ; //returns false if the game is over!
	public static function isGameOver($game_id) ; //either there is a winner or the number of moves is equivalent to the size*size*4-size
	#######################################################
	#######################################################

*/

	public static $last_inserted_id = 0 ;//holds the id of the last inserted row

	public static function create_table()
	{
		$query = "CREATE TABLE IF NOT EXISTS `game` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `winnerID` int(10) unsigned,
  `createDate` varchar(20) NOT NULL,
  `currentTurnPlayerID` int(10) unsigned NOT NULL,
  `size` int(11) NOT NULL,
  `lastActivityDate` varchar(20),
  `player1ID` int(10) unsigned NOT NULL,
  `player2ID` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `winnerID` (`winnerID`),
  KEY `currentTurnPlayerID` (`currentTurnPlayerID`),
  KEY `player1ID` (`player1ID`),
  KEY `player2ID` (`player2ID`)
) ;" ;
		$db = new database() ;
		$s = True ;
		$s = $s && $db->query($query) ;

		$query = "ALTER TABLE `game`
  ADD CONSTRAINT `game_ibfk_1` FOREIGN KEY (`winnerID`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `game_ibfk_2` FOREIGN KEY (`currentTurnPlayerID`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `game_ibfk_3` FOREIGN KEY (`player1ID`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `game_ibfk_4` FOREIGN KEY (`player2ID`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;" ;

		return  $s && $db->query($query) ;
	}


	public static function drop_table()
	{
		$query = "DROP TABLE `game`" ;
		$db = new database() ;
		return $db->query($query) ;
	}
	
	
	public static function clear_table()
	{
		$query = "DELETE FROM `game`;" ;
		$db = new database() ;
		return $db->query($query) ;
	}
	
	public static function add_new_game($current_turn_player_id,$size,$player1ID,$player2ID)
	{
		$date = time() ;
		if(safe_input::is_number($current_turn_player_id) && safe_input::is_number($size) && $size > 1  && safe_input::is_number($player1ID)  && safe_input::is_number($player2ID) && ($current_turn_player_id == $player1ID || $current_turn_player_id == $player2ID))
		{
			$db = new database() ;
			$query = "INSERT INTO `game` (`winnerID`, `createDate`, `currentTurnPlayerID`, `size`, `lastActivityDate`, `player1ID` , `player2ID`) VALUES ( null, '$date', '$current_turn_player_id', '$size', null , '$player1ID', '$player2ID');" ;
			$res = $db->query($query) ;
			game::$last_inserted_id = $db->insert_id() ;
			return $res ;
		}
		else
		{
			//echo "table_game::add_new_game: current turn or size or player id is not a number!" ;
			return false ;
		}
	}
	
	public static function getGameById($game_id)
	{
		if( safe_input::is_number($game_id))
		{
			$db = new database() ;
			$db->select("game","*","id= $game_id ") ;
			if($db->number_of_rows() > 0)
			{
				return $db->fetch_row() ;
			}
			else
			{
				return FALSE ;
			}
		}
		else
		{
			return false ;
		}
	}
	
	
	public static function getNumberOfGames()
	{
		$db = new database() ;
		$query = "SELECT * FROM `game`" ;
		$db->query($query) ;
		return $db->number_of_rows() ;
	}
	
	public static function setLastActivityDate($game_id)
	{
		if( safe_input::is_number($game_id))
		{
		$tt = time() ;
		$db = new database() ;
		$query = "UPDATE  `game` SET  `lastActivityDate` =  '$tt' WHERE  `id` = '$game_id'" ;
		return $db->query($query) ;
		}
		else
		{
			return false ;//ERROR, passed id is not a number
		}
	}
	
	
	public static function getNumberOfGamesForUserId($user_id) //returns the number of games associated with the user id
	{
		if( safe_input::is_number($user_id))
		{
			$db = new database() ;
			$query = "SELECT * FROM `game` where `player1ID` = '$user_id' or `player2ID` = '$user_id' " ;
			$db->query($query) ;
			return $db->number_of_rows() ;
		}
		else
		{
			return -1 ; //ERROR
		}
	}
	
	
	public static function getAllGamesForUserId($user_id) //returns an array of the games or null if no games exist
	{
		if( safe_input::is_number($user_id))
		{
			$db = new database() ;
			$query = "SELECT * FROM `game` where `player1ID` = '$user_id' or `player2ID` = '$user_id' " ;
			$db->query($query) ;
			if($db->number_of_rows() == 0)
			{
				return null ;//there are no games associated wiht this user id!
			}
			else
			{
				while($game = $db->fetch_row())
				{
					$allGames [] = $game ;
				}
				
				return $allGames ;
			}
		}
		else
		{
			return -1 ; //ERROR
		}
	}
	
	public static function getOpponentId($game_id,$player_id)
	{
		if(safe_input::is_number($game_id) && safe_input::is_number($player_id))
		{
			$game = game::getGameById($game_id) ;
			if($game == false)
			{
				//no game with that id
				return null ;
			}
			else
			{
				if($game['player1ID'] == $player_id)
				{
					return $game['player2ID'] ;
				}
				elseif ($game['player2ID'] == $player_id)
				{
					return $game['player1ID'] ;
				}
				else
				{
					return null ; //player id is not a player in the game with the id $game_id
				}
			}
		}
		else
		{
			return null ; //ERROR: either the game id or player id or both are not numbers! (invalid)
		} 
	}
	
}//end of class



?>
