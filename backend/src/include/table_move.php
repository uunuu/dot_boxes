<?php

include_once("database.php") ;
include_once("safe_input.php") ;
include_once("table_game.php") ;


class move
{



/*
	#######################################################
	################## MEMBER FUNCTIONS ###################
	#######################################################
	public static function create_table() ;
	public static function drop_table() ;
	public static function clear_table() ;
	public static function add_new_move($game_id,$x,$y,$edgePosition,$player_id) //retruns false if 1)invalid game_id 2)invalid player_id 3)inconsistent x,y coordinates 4)invalid edgePosition; THIS METHOD Switches the player turn in the game table and updates the last activity date
	public static function get_last_move_for_game_id($game_id) ;
	public static function count_moves($game_id)
	public static function get_all_moves_after_given_date($game_id,$timestamp) //returns an array of moves that happened after the given timestamp; returns null if not moves found
	public static function get_all_moves_for_game_id($game_id);//returns null if game_id doesn't have moves
	public static function delete_move($move_id)
	
	
	
	Feature:
	-Check consistency by checking that there are no two consecutive moves by the same player!
	-Check consistency by checking that there is only one move for every position
	#######################################################
	#######################################################

*/

	public static $last_inserted_id = -1 ;

	public static function create_table()
	{
		$query = "CREATE TABLE IF NOT EXISTS `move` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `gameID` int(10) unsigned NOT NULL,
  `x` int(10) unsigned NOT NULL,
  `y` int(10) unsigned NOT NULL,
  `date` varchar(20) NOT NULL,
  `edgePosition` varchar(10) NOT NULL,
  `playerID` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `gameID` (`gameID`),
  KEY `playerID` (`playerID`)
);" ;
		$db = new database() ;
		$s = True ;
		$s = $s && $db->query($query) ;

		$query = "ALTER TABLE `move`
  ADD CONSTRAINT `move_ibfk_2` FOREIGN KEY (`playerID`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `move_ibfk_1` FOREIGN KEY (`gameID`) REFERENCES `game` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;" ;
		return  $s && $db->query($query) ;
	}


	public static function drop_table()
	{
		$query = "DROP TABLE `move`" ;
		$db = new database() ;
		return $db->query($query) ;
	}
	
	public static function clear_table()
	{
		$query = "DELETE FROM `move`;" ;
		$db = new database() ;
		return $db->query($query) ;
	}
	
	
	public static function add_new_move($game_id,$x,$y,$edgePosition,$player_id)
	{
		$date = microtime(true) ;
		//TEST:
		//game_id is valid
		//player_id is valid
		//consistent $x and $y coordinates with the game size
		//valid edgePosition (it has to be one of the values: 1 (NORTH), 2 (SOUTH), 3 (EAST) , 4 (WEST) )
		
		//secure the input
		if(safe_input::is_number($game_id) && safe_input::is_number($x) && safe_input::is_number($y) && safe_input::is_number($edgePosition) && safe_input::is_number($player_id))
		{
			//validate game_id
			$game_info = game::getGameById($game_id) ;
			if($game_info == false)
			{
				return false ; //no game exists with the given id
			}
			
			$other_player_id = -1 ;
			//check if player_id is a player in the game
			if($player_id == $game_info['player1ID'])
			{
				$other_player_id = $game_info['player2ID'] ;
			}
			elseif($player_id == $game_info['player2ID'])
			{
				$other_player_id = $game_info['player1ID'] ;
			}
			else
			{
				return false ; //ERROR!! (player tring to play is not part of the game
			}
			
			//check if it is the player's turn
			if($player_id != $game_info['currentTurnPlayerID'])
			{
				return false; //it is not the player's turn!
			}

			
			//check coordinates consistency
			if($x > $game_info['size'] || $y > $game_info['size'] || $x < 1 || $y < 1)
			{
				return false ; //invalid move coordinates
			}
			
			
			//check edigePosition
			if($edgePosition != 1 && $edgePosition != 2 && $edgePosition != 3 && $edgePosition != 4)
			{
				return false ; //invalid edgePosition value!
			}
			
			
			//add the move
			$db = new database() ;
			$query[] = "INSERT INTO `move` (`gameID`, `date`, `x`, `y`, `edgePosition`, `playerID`) VALUES ( '$game_id', '$date', '$x', '$y', '$edgePosition' , '$player_id');" ;
			$query[] = "UPDATE  `game` SET  `currentTurnPlayerID` =  '$other_player_id' , `lastActivityDate` = '$date' WHERE  `id` = '$game_id'" ;
			$res = $db->execute_transaction($query) ;
			
			return $res ;
		}
		else
		{
			return false ; //ERROR!!!! (invalid input!!)
		}
	}
	
	
	
	
	public static function get_last_move_for_game_id($game_id)
	{
		if(safe_input::is_number($game_id))
		{
			$query = "SELECT * FROM `move` WHERE `gameID` = '$game_id' AND `date` >= (select max(`date`) from `move` WHERE `gameID` = '$game_id')" ;
			$db = new database() ;
			$db->query($query) ;
			if($db->number_of_rows() > 0)
			{
				if($db->number_of_rows() > 1)
				{
						return false ; // it should return only one row!
				}
				
				$move = $db->fetch_row() ;
				return $move ;
			}
			else
			{
				return false; //the game has not moves
			}
		}
		else
		{
			return false ;//invalid game_id
		}
	}
	
	
	public static function count_moves($game_id)
	{
		if(safe_input::is_number($game_id))
		{
			$query = "SELECT * FROM `move` WHERE `gameID` = '$game_id'" ;
			$db = new database() ;
			$db->query($query) ;
			return $db->number_of_rows() ;
		}
		else
		{
			return false ; //game_id should be a number !
		}
	}
	
	public static function get_all_moves_after_given_date($game_id,$timestamp)
	{
		if( safe_input::is_number($game_id) && safe_input::is_number_floating($timestamp) ) 
		{
			$query = "SELECT * FROM `move` WHERE `gameID` = '$game_id' AND `date` > '$timestamp'" ;
			$moves ;
			$db = new database() ;
			$db->query($query) ;
			if($db->number_of_rows() < 1)
			{
				return null ;
			}
			else
			{
				while($move = $db->fetch_row())
				{
					$moves[] = $move ;
				}
				
				return $moves ;
			}
		}
		else
		{
			return null ;//invalid input
		}
	}
	
	
	
	public static function get_all_moves_for_game_id($game_id)
	{
		if( safe_input::is_number($game_id)) 
		{
			$query = "SELECT * FROM `move` WHERE `gameID` = '$game_id'" ;
			$moves ;
			$db = new database() ;
			$db->query($query) ;
			if($db->number_of_rows() < 1)
			{
				return null ;
			}
			else
			{
				while($move = $db->fetch_row())
				{
					$moves[] = $move ;
				}
				
				return $moves ;
			}
		}
		else
		{
			return null ;//invalid input
		}
	}
	
	
	public static function delete_move($move_id)
	{
		if(safe_input::is_number($move_id))
		{
			$query = "DELETE FROM `move` WHERE `id` = '$move_id' ;" ;
			$db = new database() ;
			return $db->query($query) ;
		}
		else
		{
			return false ;
		}
	}
	
}//end of class



?>
