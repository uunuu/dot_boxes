<?php
include_once 'Report.php' ;
include_once 'Execute.php' ;
/*
 This class is responsible for parsing requests  
*/
interface ParseRequest
{
	
	//this method validates the reuqest then returns true if it is valid or false otherwise
	public function isValidRequest($request) ;
	//this methd reads the request id and passes the request to the appropriate method to be processed
	public function processRequest($request) ;
	
	
	public function registerNewUser() ; //1
	public function newSession() ;//2
	public function endSession() ;//3
	public function registerGCM() ;//4
	public function startNewGame() ;//5
	public function retrievePendingGamesList() ;//6
	public function retrieveGamesList() ;//7
	public function retrieveAllGameMovesAfterGivenDate() ;//8
	public function retrieveAllMessagesAfterGivenDate() ;//9
	public function playTurn() ;//10
	public function withdrawFromGame() ;//11
	public function cancelPendingGame() ;//12
	public function reportError() ;//13
	
}





class XmlParseRequest implements ParseRequest
{
	private $requestData ; 
	private $response ;

	
	public static $DATABASE_ERROR = 2 ;

	
	public function getResponse()
	{
		return $this->response ;
	}
	
	
	public function isValidXmlFile($file)
	{
		libxml_use_internal_errors(true);
		$xmlDocument = simplexml_load_string($file);
		$xml = explode("\n", $xmlDocument);

		if (!$xmlDocument) {
			$errors = libxml_get_errors();

			foreach ($errors as $error) {
				//echo display_xml_error($error, $xml);
				Report::error("ParseRequest_class, isValidRequest","invalid xml file!") ;
			}

			libxml_clear_errors();
			return false ;
		}
		else
		{
			return true ;
		}
	}
	
	//validates:
	//1-existence of request root node
	//2-existence of header node with mode attribuet and a version node in it
	//3-existence of body node with an id node in it
	//if all above is true then ti returns true
	public function isValidRequest($request)
	{
		if(empty($request->xpath("/request")))
		{
			return false ;
		}
		
		if(empty($request->xpath("/request/header")))
		{
			return false ;
		}
		else
		{
			if(!isset($request->header['mode']))
			{
				return false ;
			}
			
			if(empty($request->xpath("/request/header/version")))
			{
				return false ;
			}
			
			if(empty($request->header->version))
			{
				return false ;
			}
		}
		
		if(empty($request->xpath("/request/body")))
		{
			return false ;
		}
		else
		{
			if(empty($request->xpath("/request/body/id")))
			{
				return false ;
			}
			
			if(empty($request->body->id))
			{
				return false ;
			}
		}
		
		
		return true ;
	}
	
	//this method reads the request id and passes the request to the appropriate method to be processed
	public function processRequest($request)
	{
		if(!$this->isValidXmlFile($request))
		{
			Report::error("ParseRequest","request is not a valid xml format") ;
			$this->invalidRequest() ;
			return ; 
		}
		
		$this->requestData = simplexml_load_string($request) ;
		
		if(!$this->isValidRequest($this->requestData))
		{
			Report::error("ParseRequest","request is not a valid request (does not have hte required nodes)") ;
			$this->invalidRequest() ;
			return ; 
		}
		
		$request_id = $this->requestData->body->id ;
		
		//validate the id
		if(!safe_input::is_number($request_id) || $request_id >13 || $request_id < 1)
		{
			Report::error( __METHOD__.",".__LINE__,"invalid request id!") ;
			$this->invalidRequest() ;
			return ;
		}
		
		
		//forward the execution to the correct method
		switch($request_id)
		{
			case 1:
			$this->registerNewUser() ;
			break ;
			
			case 2:
			$this->newSession();
			break ;
			
			case 3:
			$this->endSession();
			break ;
			
			case 4:
			$this->registerGCM();
			break ;
			
			case 5:
			$this->startNewGame();
			break; 
			
			case 6:
			$this->retrievePendingGamesList();
			break;
			
			case 7:
			$this->retrieveGamesList();
			break ;
			
			case 8:
			$this->retrieveAllGameMovesAfterGivenDate() ;
			break ;
			
			case 9:
			$this->retrieveAllMessagesAfterGivenDate() ;
			break;
			
			case 10:
			$this->playTurn() ;
			break;
			
			case 11:
			$this->withdrawFromGame() ;
			break ;
			
			case 12:
			$this->cancelPendingGame() ;
			break;
			
			case 13:
			$this->reportError() ;
			break ;
		}
		
		
		
	}
	
	//1
	public function registerNewUser()
	{
		if(XmlRequestValidator::isValidReigsterNewUerRequest($this->requestData))
		{
			$username = $this->requestData->body->username ;
			$password = $this->requestData->body->password ;
			$email    = $this->requestData->body->email ;
			$result = Execute::registerNewUser($username,$password,$email) ;
			if($result == true)
			{
				//retrun success response
				$this->response = XmlBuilder::registerNewUserSuccessfullResponse("plain") ;
			}
			else
			{
				//return failed response
				Report::error( __METHOD__.",".__LINE__,"some required data is missing in the request!") ;
				$this->response = XmlBuilder::failed_response("plain",1,0,"some required data is missing in the request!") ;
			}
		}
		else
		{
			Report::error( __METHOD__.",".__LINE__,"invalid register new user request!") ;
			$this->invalidRequest() ;
		}
		
	}
	
	//2
	public function newSession()
	{
		if(XmlRequestValidator::isValidNewSessionRequest($this->requestData))
		{
			$username = $this->requestData->body->username ;
			$password = $this->requestData->body->password ;
			$gcm_id   = $this->requestData->body->gcm ;
			if(Execute::startNewSession($username,$password,$gcm_id))
			{
				//seccuss response with the session id
				$user_info = user::getUserByUsername($username) ;
				$session_info = session::get_last_session_for_user_id($user_info['id']) ;
				$hash = $session_info['hash'] ;
				$this->response = XmlBuilder::newSessionSuccessfullResponse("plain",$hash) ;
			}
			else
			{
				//failure response with the reason
				$this->response = XmlBuilder::failed_response("plain",2,XmlParseRequest::$DATABASE_ERROR,Execute::$lastErrorMessage) ;
			}
			
		}
		else
		{
			Report::error( __METHOD__.",".__LINE__,"invalid new session request!") ;
			$this->invalidRequest() ;
		}
	}
	
	//3
	public function endSession()
	{
		if(XmlRequestValidator::isValidEndSessionRequest($this->requestData))
		{
			$session = $this->requestData->body->session ;
			if(safe_input::is_valid_session_hash($session))
			{
				if(Execute::endSession($session))
				{
					$this->response = XmlBuilder::endSessionSuccessfullResponse("plain",$session) ;
				}
				else
				{
					$this->response = XmlBuilder::failed_response("plain",3,XmlParseRequest::$DATABASE_ERROR,"server was unable to end session, try again") ;
				}
				
			}
			else
			{
				Report::error( __METHOD__.",".__LINE__,"end session request contains an incorrectly formatted session hash") ;
				$this->response = XmlBuilder::failed_response("plain",3,0,"invalid session") ;
			}
		}
		else
		{
			Report::error( __METHOD__.",".__LINE__,"invalid end session request!") ;
			$this->invalidRequest() ;
		}
	}
	
	//4
	public function registerGCM()
	{
		if(XmlRequestValidator::isValidregisterGCMRequest($this->requestData))
		{
			$session = $this->requestData->body->session ;
			$gcm     = $this->requestData->body->gcm ;
			
			if(safe_input::is_valid_session_hash($session) && safe_input::is_valid_gcm_id($gcm))
			{
				//chkec if the session hash exists
				$session_info = session::get_session_by_hash($session) ;
				if($session_info != null)
				{
					$res = Execute::registerGCM($session,$gcm) ;
					if($res)
					{
						$this->response = XmlBuilder::registerGCMSuccessfullResponse("plain",$session) ;
					}
					else
					{
						$this->response = XmlBuilder::failed_response("plain",4,XmlParseRequest::$DATABASE_ERROR,"server was unable register gcm id, try again") ;
					}
					
				}
				else
				{
					//the given hash doesn't exist in the database
					Report::warning( __METHOD__.",".__LINE__,"register gcm id request contains a session hash that does not exist in the database: hash=".$session) ;
					$this->response = XmlBuilder::failed_response("plain",4,1,"expired session") ;
				}
			}
			else
			{
				//invalid data passed
				Report::error( __METHOD__.",".__LINE__,"register gcm id request contains an incorrectly formatted session hash or gcm id") ;
				$this->response = XmlBuilder::failed_response("plain",4,0,"invalid session or gcm id") ;
			}
		}
		else
		{
			//xml request was not formatted correctly
			Report::error( __METHOD__.",".__LINE__,"invalid register gcm request!") ;
			$this->invalidRequest() ;
		}
	}
	
	//5
	public function startNewGame()
	{
		if(XmlRequestValidator::isValidStartPendingGameRequest($this->requestData))
		{
			$session = $this->requestData->body->session ;
			$size = $this->requestData->body->size ;
			if(safe_input::is_valid_session_hash($session) && safe_input::is_number($size) && $size > 1)
			{
				//chkec if the session hash exists
				$session_info = session::get_session_by_hash($session) ;
				if($session_info != null)
				{
					$res = Execute::newPendingGame($session,$size) ;
					if($res)
					{
						$this->response = XmlBuilder::startNewPendingGameSuccessfullResponse("plain",$session) ;
					}
					else
					{
						//faild to add new game
						Report::error( __METHOD__.",".__LINE__,"failed to add new pending game") ;
						$this->response = XmlBuilder::failed_response("plain",5,0,"failed to add new pending game, try again") ;
					}
				}
				else
				{
					//the given hash doesn't exist in the database
					Report::warning( __METHOD__.",".__LINE__,"start new pending game request contains a session hash that does not exist in the database: hash=".$session) ;
					$this->response = XmlBuilder::failed_response("plain",5,1,"expired session") ;
				}
			}
			else
			{
				//invalid data passed
				Report::error( __METHOD__.",".__LINE__,"start new pending game request contains an incorrectly formatted session hash or game size, size:".$size) ;
				$this->response = XmlBuilder::failed_response("plain",5,0,"invalid session or gcm id") ;
			}
		}
		else
		{
			//xml request was not formatted correctly
			Report::error( __METHOD__.",".__LINE__,"invalid new pending game request!") ;
			$this->invalidRequest() ;
		}
	}
	
	//6
	public function retrievePendingGamesList()
	{
		
	}
	
	//7
	public function retrieveGamesList()
	{
		
	}
	
	//8
	public function retrieveAllGameMovesAfterGivenDate()
	{
		
	}
	
	//9
	public function retrieveAllMessagesAfterGivenDate()
	{
		
	}
	
	//10
	public function playTurn()
	{
		
	}
	
	//11
	public function withdrawFromGame()
	{
		
	}
	
	//12
	public function cancelPendingGame()
	{
		
	}
	
	//13
	public function reportError()
	{
		
	}
	
	
	public function invalidRequest()
	{
		Report::error("ParseRequest_class, invalidRequest()","invalid request format! ") ;
		$this->response = xmlBuilder::general_error(0,"invalid request format!") ;
	}
	
	
	

}

?>
