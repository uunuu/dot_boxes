<?php


//this class validates the xml requests. This means that the checks that the required tags exist in the request
class XmlRequestValidator
{
	/*################## member functions #####################
	public static function isValidReigsterNewUerRequest($request) ;
	public static function isValidNewSessionRequest($request) ;
	public static function isValidEndSessionRequest($request); 
	public static function isValidregisterGCMRequest($request) ;
	
	public static function isValidStartPendingGameRequest($request) ;
	
	//#########################################################
	*/

	public static function isValidReigsterNewUerRequest($request)
	{
		//empty($xm1->xpath("/response/error"))
		$isValid = true ;
		$isValid = $isValid && !empty($request->xpath("/request/body/username")) ;
		
		if($isValid)
		{
			$isValid = $isValid && !empty($request->body->username) ;
		}
		
		$isValid = $isValid && !empty($request->xpath("/request/body/password")) ;
		if($isValid)
		{
			$isValid = $isValid && !empty($request->body->password) ;
		}
		
		$isValid = $isValid && !empty($request->xpath("/request/body/email")) ;
		if($isValid)
		{
			$isValid = $isValid && !empty($request->body->email) ;
		}
		
		return $isValid ;
		
	}
	
	public static function isValidNewSessionRequest($request)
	{
		$isValid = true ;
		
		$isValid = $isValid && !empty($request->xpath("/request/body/username")) ;
		if($isValid)
		{
			$isValid = $isValid && !empty($request->body->username) ;
		}
		
		$isValid = $isValid && !empty($request->xpath("/request/body/password")) ;
		if($isValid)
		{
			$isValid = $isValid && !empty($request->body->password) ;
		}
		
		$isValid = $isValid && !empty($request->xpath("/request/body/gcm")) ;
		if($isValid)
		{
			$isValid = $isValid && !empty($request->body->gcm) ;
		}	
		
		return $isValid ;
	}
	
	
	public static function isValidEndSessionRequest($request)
	{
		$isValid = true ;
		
		$isValid = $isValid && !empty($request->xpath("/request/body/session")) ;
		if($isValid)
		{
			$isValid = $isValid && !empty($request->body->session) ;
		}	
		
		return $isValid ;
	}
	
	
	public static function isValidregisterGCMRequest($request)
	{
		$isValid = true ;
		
		$isValid = $isValid && !empty($request->xpath("/request/body/session")) ;
		if($isValid)
		{
			$isValid = $isValid && !empty($request->body->session) ;
		}	
		
		
		$isValid = $isValid && !empty($request->xpath("/request/body/gcm")) ;
		if($isValid)
		{
			$isValid = $isValid && !empty($request->body->gcm) ;
		}	
		return $isValid ;
	}
	
	
	public static function isValidStartPendingGameRequest($request)
	{
		$isValid = true ;
		
		$isValid = $isValid && !empty($request->xpath("/request/body/session")) ;
		if($isValid)
		{
			$isValid = $isValid && !empty($request->body->session) ;
		}	
		
		
		$isValid = $isValid && !empty($request->xpath("/request/body/size")) ;
		if($isValid)
		{
			$isValid = $isValid && !empty($request->body->size) ;
		}
		
		return $isValid ;
	}
	
	
}//end of class


?>
