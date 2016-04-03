<?php

include_once("safe_input.php") ;
include_once("table_game.php") ;
include_once("table_user.php") ;
include_once("table_session.php") ;
include_once("general.php") ;
include_once("GameControl.php") ;

class Execute
{
	/*################## member functions #####################
	  public static function registerNewUser($username,$password,$email)
	  public static function startNewSession($username,$password,$gcm_id) ;
	  public static function endSession($session) ;
	  public static function registerGCM($session,$gcm)
	  
	  public static function newPendingGame($session,$size) ;
	
	//#########################################################
	*/
	
	public static $lastErrorMessage = "" ;
	
	public static function registerNewUser($username,$password,$email)
	{
		return user::create_new_user($username,$password,$email) ;
	}
	
	
	public static function startNewSession($username,$password,$gcm_id)
	{
		$success = true ;
		//test if the username and password are correct
		if(user::isLogin($username,$password))
		{
			//retrieve user info
			$user_info = user::getUserByUsername($username) ;
			$e = user::setGCM($user_info['id'],$gcm_id) ;
			$success = $success && $e ;

			//check if user has existing session:
			if(session::does_user_have_session($user_info['id']))
			{
				//remove the session
				$session_info = session::get_last_session_for_user_id($user_info['id']) ;
				session::delete_session_by_id($session_info['id']) ;
			}
			
			//generate a unique hash
			$newHash = md5(random::generateString(10)) ;
			while(!session::is_unique_hash($newHash))
			{
				$newHash = md5(random::generateString(10)) ;
			}
			//create a session
			$res =  session::add_new_session($user_info['id'],$newHash,"0") ;
			$success = $success && $res ;
			if(!$success)
			{
				Execute::$lastErrorMessage = "failed to add new changes to database" ;
				Report::error( __METHOD__.",".__LINE__,"failed to new cahnges to database") ;
			}
			return $success ;
			
		}
		else
		{
			Execute::$lastErrorMessage = "trying to login with an incorrect username or password" ;
			Report::warning( __METHOD__.",".__LINE__,"trying to login with an incorrect username or password") ;
			return false ;//trying to log in with an incorrect username or password
		}
	}
	
	
	public static function endSession($session)
	{
		return session::delete_session_by_hash($session) ;
	}
	
	
	public static function registerGCM($session,$gcm)
	{
		$session_info = session::get_session_by_hash($session) ;
		return user::setGCM($session_info['userID'],$gcm) ;
	}
	
	
	public static function newPendingGame($session,$size)
	{
		$session_info = session::get_session_by_hash($session) ;
		if($session_info != null && safe_input::is_number($size) && $size > 1 && $size < 21)
		{
			$add_result = pending_game::add_new_pending_game($session_info['userID'],$size) ;
			GameControl::matchPendingGames() ;
			return $add_result ;
		}
		else
		{
			Report::warning( __METHOD__.",".__LINE__,"trying to create a pending game with an invalid size:".$size) ;
			return false ;
		}
	}
	
}//end of class


?>
