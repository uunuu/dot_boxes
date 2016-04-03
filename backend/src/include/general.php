<?php


class random
{
	//it will generate a random string (alphabitic + numbers) of the length $length
	public static function generateString($length)
	{
		// start with a blank string
		$string = "";

		// define possible characters
		$possible = "0123456789abcdefghijklmnopqrstuvwxyz"; 

		// set up a counter
		$i=0;
		// add random characters to $password until $length is reached
		while ($i < $length) 
		{
			// pick a random character from the possible ones
			$char = substr($possible, mt_rand(0, strlen($possible)-1), 1);
			$string .= $char;
			$i++;
		}

		return $string;
	
	}

	public static function salt()
	{
		return random::generateString(5) ;
	}

}






?>
