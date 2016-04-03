<?php

//This class is used to make variables safe from XSS and SQL injuctions

class safe_input
{
	/*
	###############Memer functions ########################
	public static function xss($value)
	public static function sql_inj($value)
	public static function all($value)
	public static function html_output($value)
	public static function is_valid_username($username)
	public static function is_valid_password($password)
	public static function is_valid_email($email)
	public static function is_number($string)
	public static function is_valid_owner_id($owner_id)
	public static function is_valid_file_type($type) ;
	public static function is_number_floating($string) like is_number() but includes floating point numbers
	public static function is_valid_session_hash($hash) ;
	public static function is_valid_encryption_key($key) ;
	public static function is_valid_ip($ip); 
	public static function is_valid_uuid($uuid) ;
	public static function is_valid_gcm_id($gcm_id) ;
	#######################################################
	*/
	//it is used to make then $avlue safe from html tages (XSS attaks)
	public static function xss($value)
	{
		//$safe_var = addslashes($value) ;
		$safe_var = htmlspecialchars($value) ;
		
		return $safe_var ;
	}
	
	public static function sql_inj($value)
	{
		//$safe_var = mysql_real_escape_string($value) ;
		$safe_var = mysql_escape_string($value) ;
		//addcslashes($safe_var, '%_');
		
		return $safe_var ;
	}
	
	public static function all($value)
	{
		$v = safe_input::xss($value) ;
		$vv= safe_input::sql_inj($v) ;
		return $vv ;
	}

	//will be called each time a string needs to be printed
	public static function html_output($value)
	{
		return htmlspecialchars($value) ;
	}

	public static function is_valid_username($username)
	{
		//$empty_exp    = '/^\s*$/';
		$username_exp = '/^\w+(\w|[\'])*$/' ;
		return preg_match($username_exp,$username) ;
	}

	public static function is_valid_email($email)
	{
		$email_exp    =  '/^\w+@\w+[.]\w{2,}$/' ;
		return preg_match($email_exp,$email) ; 
	}

	public static function is_valid_password($password)
	{
		$password_exp = '/\S{6,}/' ;
		return preg_match($password_exp,$password) ;
	}

	public static function is_number($string)
	{
		$number_exp = '/^\d+$/' ;
		return preg_match($number_exp,$string) ;
	}
	
	
	public static function is_number_floating($string)
	{
		$number_exp = '/^\d+\.?\d*$/' ;
		return preg_match($number_exp,$string) ;
	}


	public static function is_valid_owner_id($owner_id)
	{
		$owner_id_exp = "/^\w+$/" ;
		return preg_match($owner_id_exp,$owner_id) ;
	}

	public static function is_valid_file_type($type)
	{
		$valid_types = array("application/octet-stream",
				     "text/plain", "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
				     "application/pdf",
				     "application/zip",
				     "image/jpeg",
				     "audio/mp3",
				     "audio/x-mp3");
		if( in_array($type, $valid_types) )
		{
			return true ;
		}
		else
		{
			return false ;
		}
		
	}

	public static function is_valid_file_extension($extension)
	{
		$valid_extensions = array("txt", "pdf", "rar", "jpeg", "docx" , "doc", "zip","mp3","jpg");
		if( in_array($extension, $valid_extensions) )
		{
			return true ;
		}
		else
		{
			return false ;
		}
	}

	public static function is_acceptable_file_size($size_in_kb)
	{
		return ($size_in_kb < 1000) ;
	}
	
	
	public static function is_valid_session_hash($hash)
	{
		$hash_exp = '/^[a-f0-9]{32}$/i' ;
		return preg_match($hash_exp,$hash) == 1 ;
	}
	
	public static function is_valid_encryption_key($key)
	{
		$key_exp = '/^[a-f0-9]+$/i' ;
		return preg_match($key_exp,$key) == 1 ;
	}
	
	public static function is_valid_ip($ip)
	{
		$ip_exp = '/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/' ;
		return preg_match($ip_exp,$ip) == 1 ;
	}
	
	public static function is_valid_uuid($uuid)
	{
		$uuid_exp = '/^[0-9a-z]+$/' ;
		return preg_match($uuid_exp,$uuid) == 1 ;
	}
	
	
	public static function is_valid_gcm_id($gcm_id)
	{
		$gcm_exp = '/^[0-9a-zA-Z\-_]+$/' ;
		return preg_match($gcm_exp,$gcm_id) == 1 ;
	}
}//end of class



?>
