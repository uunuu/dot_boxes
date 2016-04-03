<?php

/*
 * This class need writing permission to work because It will create files 
*/

class CriticalSection
{
	private $key ;
	private $handle ;
	
	function __construct($k)
	{
		$this->key = $k ;
		$this->key = $this->key.".lock" ;
		$this->init() ;
	}
	

	
	private function init()
	{
		if(!file_exists($this->key))
		{
			$this->handle = fopen($this->key, "w")  ;
			fclose($this->handle) ;
		}
	}
	
	public function lock()
	{
		$this->handle = fopen($this->key, "w")  ;
		flock($this->handle, LOCK_EX) ;
		
	}
	
	public function unlock()
	{
		flock($this->handle, LOCK_UN); 
		fclose($this->handle) ;
	}
	
	
	function __destruct()
	{
		
	}
	
}

?>
