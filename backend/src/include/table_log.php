<?php

include_once("database.php") ;
include_once("safe_input.php") ;


class log
{



/*
	#######################################################
	################## MEMBER FUNCTIONS ###################
	#######################################################
	public static function create_table() ;
	public static function drop_table() ;
	
	public static function addNewLog($user_id,$ip,$uuid) ;
	public static function get_logs_count() ;
	
	
	public static function deleteSimilarLogs() ;
	
	#######################################################
	#######################################################

*/

	public static $last_inserted_id = -1 ;

	public static function create_table()
	{
		$query = "CREATE TABLE IF NOT EXISTS `log` (
  `userID` int(10) unsigned NOT NULL,
  `ipAddress` varchar(30) NOT NULL,
  `uuid` varchar(30) NOT NULL,
  `date` varchar(20) NOT NULL,
  KEY `userID` (`userID`)
) ;" ;
		$db = new database() ;
		$s = True ;
		$s = $s && $db->query($query) ;

		$query = "ALTER TABLE `log`
  ADD CONSTRAINT `ip_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;" ;
		return  $s && $db->query($query) ;
	}


	public static function drop_table()
	{
		$query = "DROP TABLE `log`" ;
		$db = new database() ;
		return $db->query($query) ;
	}
	
	public static function clear_table()
	{
		$query = "DELETE FROM `log`;" ;
		$db = new database() ;
		return $db->query($query) ;
	}
	
	
	public static function addNewLog($user_id,$ip,$uuid)
	{
		if( safe_input::is_number($user_id) && safe_input::is_valid_ip($ip) && safe_input::is_valid_uuid($uuid) )
		{
			$date = microtime(true) ;
			$query = "INSERT INTO `log` (`userID`, `date`, `uuid`, `ipAddress`) VALUES ( '$user_id', '$date', '$uuid', '$ip');" ;
			$db = new database() ;
			$res = $db->query($query) ;
			log::$last_inserted_id = $db->insert_id() ;
			
			return $res ;
		}
		else
		{
			return false ; //invalid input
		}
	}
	
	public static function get_logs_count()
	{
			$query = "SELECT * FROM `log`" ;
			$db = new database() ;
			$res = $db->query($query) ;
			return $db->number_of_rows() ;
	}
	
	public static function deleteSimilarLogs()
	{
			$query = "DELETE FROM `log` as `R` where EXISTS (SELECT * FROM `log` as `M` where `R.ipAddress` = `M.ipAddress` AND `R.userID` = `M.userID` AND `R.uuid` = `M.uuid` AND R.date <> M.date)" ;
			$db = new database() ;
			return $db->query($query) ;
	}
	
	
	
	
	
}//end of class



?>
