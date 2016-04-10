<?php

include_once 'CriticalSection.php' ;
include_once 'table_pending_game.php' ;

class GameControl
{
	//################# Member Functions #########################
	/*
	public static function matchPendingGames() ; //checks if there are pending games that need to be matched
	
	public static function matchPendingGamesBySize($size) ;//matches games with the given size in a FIFO order
	*/
	//############################################################
	
	public static $key = "key" ;
	
	public static function matchPendingGames()
	{
		$sizes ;
		/** @var $i is the size of the board. it can't be smaller than 2. #0 is set by default as the biggest */
		for($i=2;$i<30;$i++)
		{
			if(pending_game::get_count_pending_by_size($i) > 1)
			{
				$sizes[] = $i ;
			}
		}
		
		if(isset($sizes))
		{
			foreach($sizes as $s)
			{
				GameControl::matchPendingGamesBySize($s) ;
			}
		}
	}
	
	
	public static function matchPendingGamesBySize($size)
	{
		//$critical = new CriticalSection(GameControl::$key) ;
		
		//$critical->lock() ;
		
		$games = pending_game::get_all_pending_games_by_size($size) ;
		$counter = count($games) ;//counter used to avoid an endless while loop when more than 1 pending game for the same user are consecutively follow each other in the array of games 
		while(count($games) > 1 && $counter > 0)
		{
			
			if(pending_game::match($games[0]['id'],$games[1]['id']))
			{
				$games = pending_game::get_all_pending_games_by_size($size) ;
			}
			
			$counter = count($games) ;
			while(count($games) > 1 && $games[0]['userID'] == $games[1]['userID'] && $counter  > 0)
			{
				$tmp = array_shift($games);
				$games[] = $tmp ;
				$counter-- ;
			}
		}
		
		
		//$critical->unlock() ;
	}
	
	
	
	
}

?>
