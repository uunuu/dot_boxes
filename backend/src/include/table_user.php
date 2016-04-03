<?php

include_once("database.php") ;
include_once("safe_input.php") ;
include_once("general.php") ;

class user
{



/*
	#######################################################
	################## MEMBER FUNCTIONS ###################
	#######################################################
	public static function create_table() ;
	public static function drop_table() ;
	public static function create_new_user($username,$password,$email) ;
	public static function clear_table() ; //delete all the records
	public static function getUserByUsername($username) ; //return info in an array
	public static function getUserById($user_id) ; //return info in an array
	public static function getNumberOfUsers() ;
	public static function doesUsernameExist($username) ;
	public static function doesEmailExist($email) ;
	public static function resetPassword($username,$newPassword) ;
	public static function setEmail($username,$newEmail) ;
	public static function deleteUserByUsername($username) ;
	public static function isLogin($username,$password) ; //true or false
	public static function setGCM($user_id,$gcm_id) ;
	#######################################################
	#######################################################

*/

	public static $last_inserted_id = -1 ;

	public static function create_table()
	{
		$query = "CREATE TABLE IF NOT EXISTS `user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(70) NOT NULL,
  `password` varchar(70) NOT NULL,
  `salt` varchar(20) NOT NULL,
  `email` varchar(200) NOT NULL,
  `joinDate` varchar(20) NOT NULL,
  `lastLoginDate` varchar(20) NOT NULL,
  `gcmID` varchar(250) ,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  KEY `id` (`id`)
);" ;
		$db = new database() ;
		return $db->query($query) ;
	}


	public static function drop_table()
	{
		$query = "DROP TABLE `user`" ;
		$db = new database() ;
		return $db->query($query) ;
	}


	public static function create_new_user($username,$password,$email)
	{
		$date = time() ;
		$salt = random::salt() ;
		
		$safe_username = safe_input::all($username) ;
		$md5_password  = md5(md5($password).md5($salt)) ;
		$safe_email    = safe_input::all($email) ;

		$db = new database() ;
		$query = "INSERT INTO `user` (`username`, `password`, `salt`, `email`, `joinDate`, `lastLoginDate` , `gcmID`) VALUES ( '$safe_username', '$md5_password', '$salt', '$safe_email', '$date', '$date', 'null');" ;
		
		$res = $db->query($query) ;
		session::$last_inserted_id = $db->insert_id() ;
		return $res ;
	}

	public static function clear_table()
	{
		$query = "DELETE FROM `user`;" ;
		$db = new database() ;
		return $db->query($query) ;
	}

	public static function getUserByUsername($username)
	{
		$safe_username = safe_input::all($username) ;
		if( safe_input::is_valid_username($safe_username))
		{
			$db = new database() ;
			$db->select("user","*","username= $safe_username ") ;
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
	
	
	public static function getUserById($user_id)
	{
		if( safe_input::is_number($user_id))
		{
			$db = new database() ;
			$db->select("user","*","id= $user_id ") ;
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

	public static function getNumberOfUsers()
	{
		$db = new database() ;
		$query = "SELECT * FROM `user`" ;
		$db->query($query) ;
		return $db->number_of_rows() ;
	}

	public static function doesUsernameExist($username)
	{
		$safe_username = safe_input::all($username) ;
		if( safe_input::is_valid_username($safe_username))
		{
			$db = new database() ;
			$db->select("user","*","username= $safe_username ") ;
			if($db->number_of_rows() > 0)
			{
				return true ;
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

	public static function doesEmailExist($email)
	{
		$safe_email = safe_input::all($email) ;
		if( safe_input::is_valid_email($safe_email))
		{
			$db = new database() ;
			$db->select("user","*","email= $safe_email ") ;
			if($db->number_of_rows() > 0)
			{
				return true ;
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


	public static function resetPassword($username,$newPassword)
	{
		$safe_username = safe_input::all($username) ;
		if(user::doesUsernameExist($safe_username) && !empty($newPassword) )
		{
			$salt = random::salt() ;
			$md5_password  = md5(md5($newPassword).md5($salt)) ;
			$db = new database() ;
			$query = "UPDATE  `user` SET  `password` =  '$md5_password', `salt` = '$salt' WHERE  `username` = '$safe_username'" ;
			return $db->query($query) ;
		}
		else
		{
			return false ;
		}
	}

	public static function setEmail($username,$newEmail)
	{
		$safe_username = safe_input::all($username) ;
		$safe_email = safe_input::all($newEmail) ;
		if(user::doesUsernameExist($safe_username) && safe_input::is_valid_email($newEmail) )
		{
			$db = new database() ;
			$query = "UPDATE  `user` SET  `email` =  '$safe_email' WHERE  `username` = '$safe_username'" ;
			return $db->query($query) ;
		}
		else
		{
			return false ;
		}	
	}

	public static function deleteUserByUsername($username)
	{
		$safe_username = safe_input::all($username) ;
		$query = "DELETE FROM `user` WHERE `username` = '$safe_username'  ;" ;
		$db = new database() ;
		return $db->query($query) ;
	}
	
	
	public static function isLogin($username,$password)
	{
		$safe_username = safe_input::all($username) ;
		if(user::doesUsernameExist($username))
		{
			$user_info = user::getUserByUsername($safe_username) ;
			$hashed_password = md5(md5($password).md5($user_info['salt'])) ;
			if(strcmp($hashed_password,$user_info['password']) == 0)
			{
				return true ;
			}
			else
			{
				return false ;//incorrect password
			}
		}
		else
		{
			return false ; //username doesn't exist
		}
	}
	
	public static function setGCM($user_id,$gcm_id)
	{
		if( safe_input::is_number($user_id) && safe_input::is_valid_gcm_id($gcm_id) )
		{
			$db = new database() ;
			$query = "UPDATE  `user` SET  `gcmID` =  '$gcm_id' WHERE  `id` = '$user_id'" ;
			return $db->query($query) ;
		}
		else
		{
			return false ;
		}
	}
	
}//end of class



?>
