<?php
include_once 'Report.php' ;


/*
 This class builds the xml responses needed 
*/
class XmlBuilder
{
	//######################### Member Fucntions ###################################
	/*
		public static function general_error($code,$message)
		public static function failed_response($id,$code,$reason) ;
		public static function registerNewUserSuccessfullResponse($mode) ;
		public static function newSessionSuccessfullResponse($mode,$hash) ;
		public static function endSessionSuccessfullResponse($mode,$session) ;
		public static function registerGCMSuccessfullResponse($mode,$session) ;


		public static function startNewPendingGameSuccessfullResponse($mode,$session) ;
	*/
	//##############################################################################
	public static function general_error($code,$message)
	{
		$domtree = new DOMDocument('1.0', 'UTF-8');
		$response = $domtree->createElement("response") ;
		$domtree->appendChild($response) ;
		
		
		$errorNode = $domtree->createElement("error") ;
		$response->appendChild($errorNode) ;
		$messageNode = $domtree->createElement("message") ;
		$messageNode->appendChild($domtree->createTextNode($message)) ;
		
		
		$errorCode = $domtree->createElement("code") ;
		$errorCode->appendChild($domtree->createTextNode($code)) ;
		
		$errorNode->appendChild($errorCode) ;
		$errorNode->appendChild($messageNode) ;
		
		return $domtree->saveXML() ;
	}
	
	
	
	/*
	 <?xml version="1.0" encoding="UTF-8"?>
	<response>
	<header mode="[plain|encrypted]">
	<version>1</version>
	</header>
	<body>
	<id></id>
	<status>failed </status>
	<error_code></error_code>
	<reason>username already exists</reason> <!-- reason could be any error message →
	</body>
	</response> 
	*/
	public static function failed_response($mode,$id,$code,$reason)
	{
		$domtree = new DOMDocument('1.0', 'UTF-8');
		$response = $domtree->createElement("response") ;
		$domtree->appendChild($response) ;
		
		$header = $domtree->createElement("header") ;
		$header->setAttribute("mode",$mode) ;
		
		$version = $domtree->createElement("version") ;
		$version->appendChild($domtree->createTextNode("1")) ;
		$header->appendChild($version) ;
		$response->appendChild($header) ;
		
		$body = $domtree->createElement("body") ;
		
		$idNode = $domtree->createElement("id") ;
		$idNode->appendChild($domtree->createTextNode($id)) ;
		
		$status = $domtree->createElement("status") ;
		$status->appendChild($domtree->createTextNode("failed")) ;
		
		$error_code = $domtree->createElement("error_code") ;
		$error_code->appendChild($domtree->createTextNode($code)) ;
		
		$reasonNode = $domtree->createElement("reason") ;
		$reasonNode->appendChild($domtree->createTextNode($reason)) ;
		
		$body->appendChild($idNode) ;
		$body->appendChild($status) ;
		$body->appendChild($error_code) ;
		$body->appendChild($reasonNode) ;
		$response->appendChild($body) ;
		
		return $domtree->saveXML() ;
	}
	
	
	public static function registerNewUserSuccessfullResponse($mode)
	{
		$domtree = new DOMDocument('1.0', 'UTF-8');
		$response = $domtree->createElement("response") ;
		$domtree->appendChild($response) ;
		
		$header = $domtree->createElement("header") ;
		$header->setAttribute("mode",$mode) ;
		
		$version = $domtree->createElement("version") ;
		$version->appendChild($domtree->createTextNode("1")) ;
		$header->appendChild($version) ;
		$response->appendChild($header) ;
		
		$body = $domtree->createElement("body") ;
		
		$idNode = $domtree->createElement("id") ;
		$idNode->appendChild($domtree->createTextNode("1")) ;
		
		$status = $domtree->createElement("status") ;
		$status->appendChild($domtree->createTextNode("successful")) ;
		
		$body->appendChild($idNode) ;
		$body->appendChild($status) ;
		$response->appendChild($body) ;
		return $domtree->saveXML() ;
		
	}
	
	
	
	public static function newSessionSuccessfullResponse($mode,$hash)
	{
		$domtree = new DOMDocument('1.0', 'UTF-8');
		$response = $domtree->createElement("response") ;
		$domtree->appendChild($response) ;
		
		$header = $domtree->createElement("header") ;
		$header->setAttribute("mode",$mode) ;
		
		$version = $domtree->createElement("version") ;
		$version->appendChild($domtree->createTextNode("1")) ;
		$header->appendChild($version) ;
		$response->appendChild($header) ;
		
		$body = $domtree->createElement("body") ;
		
		$idNode = $domtree->createElement("id") ;
		$idNode->appendChild($domtree->createTextNode("2")) ;
		
		$status = $domtree->createElement("status") ;
		$status->appendChild($domtree->createTextNode("successful")) ;
		
		$session = $domtree->createElement("session") ;
		$session->appendChild($domtree->createTextNode($hash)) ;
		
		
		$body->appendChild($idNode) ;
		$body->appendChild($status) ;
		$body->appendChild($session) ;
		$response->appendChild($body) ;
		return $domtree->saveXML() ;
	}
	
	
	
	public static function endSessionSuccessfullResponse($mode,$session)
	{
		$domtree = new DOMDocument('1.0', 'UTF-8');
		$response = $domtree->createElement("response") ;
		$domtree->appendChild($response) ;
		
		$header = $domtree->createElement("header") ;
		$header->setAttribute("mode",$mode) ;
		
		$version = $domtree->createElement("version") ;
		$version->appendChild($domtree->createTextNode("1")) ;
		$header->appendChild($version) ;
		$response->appendChild($header) ;
		
		$body = $domtree->createElement("body") ;
		
		$idNode = $domtree->createElement("id") ;
		$idNode->appendChild($domtree->createTextNode("3")) ;
		
		$status = $domtree->createElement("status") ;
		$status->appendChild($domtree->createTextNode("successful")) ;
		
		$sessiont = $domtree->createElement("session") ;
		$sessiont->appendChild($domtree->createTextNode($session)) ;
		
		
		$body->appendChild($idNode) ;
		$body->appendChild($status) ;
		$body->appendChild($sessiont) ;
		$response->appendChild($body) ;
		return $domtree->saveXML() ;
	}
	
	
	public static function registerGCMSuccessfullResponse($mode,$session)
	{
		$domtree = new DOMDocument('1.0', 'UTF-8');
		$response = $domtree->createElement("response") ;
		$domtree->appendChild($response) ;
		
		$header = $domtree->createElement("header") ;
		$header->setAttribute("mode",$mode) ;
		
		$version = $domtree->createElement("version") ;
		$version->appendChild($domtree->createTextNode("1")) ;
		$header->appendChild($version) ;
		$response->appendChild($header) ;
		
		$body = $domtree->createElement("body") ;
		
		$idNode = $domtree->createElement("id") ;
		$idNode->appendChild($domtree->createTextNode("4")) ;
		
		$status = $domtree->createElement("status") ;
		$status->appendChild($domtree->createTextNode("successful")) ;
		
		$sessiont = $domtree->createElement("session") ;
		$sessiont->appendChild($domtree->createTextNode($session)) ;
		
		
		$body->appendChild($idNode) ;
		$body->appendChild($status) ;
		$body->appendChild($sessiont) ;
		$response->appendChild($body) ;
		return $domtree->saveXML() ;
	}
	
	
	
	public static function startNewPendingGameSuccessfullResponse($mode,$session)
	{
		$domtree = new DOMDocument('1.0', 'UTF-8');
		$response = $domtree->createElement("response") ;
		$domtree->appendChild($response) ;
		
		$header = $domtree->createElement("header") ;
		$header->setAttribute("mode",$mode) ;
		
		$version = $domtree->createElement("version") ;
		$version->appendChild($domtree->createTextNode("1")) ;
		$header->appendChild($version) ;
		$response->appendChild($header) ;
		
		$body = $domtree->createElement("body") ;
		
		$idNode = $domtree->createElement("id") ;
		$idNode->appendChild($domtree->createTextNode("5")) ;
		
		$status = $domtree->createElement("status") ;
		$status->appendChild($domtree->createTextNode("successful")) ;
		
		$sessiont = $domtree->createElement("session") ;
		$sessiont->appendChild($domtree->createTextNode($session)) ;
		
		
		$body->appendChild($idNode) ;
		$body->appendChild($status) ;
		$body->appendChild($sessiont) ;
		$response->appendChild($body) ;
		return $domtree->saveXML() ;
	}
	
}

?>
