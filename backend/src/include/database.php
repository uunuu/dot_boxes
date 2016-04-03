<?php


class database
{
	private $host = "127.0.0.1" ;
	private $database_name = "dot_and_boxes" ;
	private $username = "root" ;
	private $password = "123456" ;

	private $db = NULL ;   // holds the database connection
	private $link = NULL ; //holds the link to the last query
	private $res = NULL ;  //holds the last result to the last query

	/*
	########### List of Member Functions ###########
	public function error(); //this function will be called whenever an error occures
	public function query($q) //executes the given query and saves the returned value in $link
	public function number_of_rows()
	public function insert($table_name,$fields,$values) // fields and values has to be seperated by "," ex: "name,age"
	public function insert_id()
	public function select($table_name,$selected_fields,$condtion) // example select("student","salt","username=m") ;
	public function execute_transaction($queries_array) //receives an array of queries and executs them as a transaction (either all or nothing)
	################################################

	*/

	function __construct()
	{
		$this->db= mysql_connect($this->host,$this->username,$this->password) ;
		//check if db is false then call error function
		if(!$this->db)
		{
			$this->error(0) ;
		}
		else
		{
			$status = mysql_select_db($this->database_name,$this->db) ;
			if(!$status)
			{
				$this->error(1) ;
			}
		}
		
	}
	
	function __destruct()
	{
		if($this->db != NULL)
		{
			mysql_close($this->db) ;
		}
		$this->db = NULL ;
		$this->link = NULL ;
		$this->res = NULL ;
	}

	public function error($error_code)
	{
		/*
		$error_code
		0 = error from the constructor connecting to teh server
		1 = error from the constructor selecting the database
		2 = error from query, unable to send the query
		3 = error from fetch_row, $this-> = NULL
		4 = error from execute_transaction
		*/
		echo "<br/>DATABASE ERROR ".$error_code ;
		//echo mysql_error() ;
		return FALSE ;
	}

	public function query($q)
	{
		$this->link = mysql_query($q,$this->db) ;
		if(!$this->link)
		{
			$this->error(2) ;
		}
		else
		{
			return TRUE ;
		}
	}

	public function number_of_rows()
	{
		return mysql_num_rows($this->link) ;
	}

	public function insert($table_name,$fields,$values)
	{
		$fields_array = explode(",",$fields) ;
		$sql_fields = "" ;
		foreach($fields_array as $field)
		{
			$sql_fields .= "`".$field."`," ;
		}
		$sql_fields = substr($sql_fields,0,strlen($sql_fields)-1) ; //remove the comma added by foreach
		$query = "INSERT INTO `".$table_name."` (".$sql_fields.") VALUES(".$values.")" ;
		return $this->query($query) ;

	}

	public function insert_id()
	{
		return mysql_insert_id($this->db) ;
	}

	public function fetch_row()
	{
		if($this->link != NULL)
		{
			return mysql_fetch_assoc($this->link) ;
		}
		else
		{
			$this->error(3) ;
		}
	}

	public  function select($table_name,$selected_fields,$condtion)
	{
			//example select("student","salt","username=m") ;

			//surround each field by `
			$expression = '/\b(\w+)\b/' ;
			$replacment = '`$1`' ;
			$sql_fields = preg_replace($expression,$replacment,$selected_fields) ;
			
			//prepare the $condtion for SQL query
			$expression = '/\b(\w+)\b(\s*=\s*)(.+)\s*/' ;
			$replacment = '`$1` = \'$3\'' ;
			$sql_condtion = ""; 
			if(!empty($condtion))
			{
				$sql_condtion = preg_replace($expression,$replacment,$condtion) ;
			}

			$query = "SELECT $sql_fields FROM `$table_name` " ;
			if(!empty($sql_condtion))
			{
				$query .= "WHERE $sql_condtion" ;
			}

			$this->query($query) ;
	}
	
	
	
	public function execute_transaction($queries_array)
	{
		if(count($queries_array) < 1)
		{
			$this->error(4) ;
			return false ;
		}
		
		
		mysql_query("SET AUTOCOMMIT=0",$this->db);
		mysql_query("START TRANSACTION",$this->db);
		
		$transaction_status = true ;
		foreach($queries_array as $q) 
		{
			$transaction_status = mysql_query($q,$this->db) && $transaction_status;
		}
		
		if($transaction_status)
		{
			mysql_query("COMMIT",$this->db);
			return true ;
		}
		else
		{
			mysql_query("ROLLBACK",$this->db);
			return false ;
		}
	}
	
	


	

}



?>
