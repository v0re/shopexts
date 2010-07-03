<?php
	include "rsa.php";

	list($keylength, $modulus, $public, $private) = read_ssl_key("rsa-example-key");

	$encrypted = rsa_encrypt("Hello world", $public, $modulus, $keylength);
	$decrypted = rsa_decrypt($encrypted, $private, $modulus, $keylength);
	
	echo $decrypted;

	/*
	 * Read an openssl (ssh-keygen) generated SSL key
	 * Note: this is a complete hack; we try to interpret a textual format 
	 */
	function read_ssl_key($filename)
	{
		exec("openssl rsa -in $filename -text -noout", $raw); 

		// read the key length
		$keylength = (int) expect($raw[0], "Private-Key: (");

		// read the modulus
		expect($raw[1], "modulus:");
		for($i = 2; $raw[$i][0] == ' '; $i++) $modulusRaw .= trim($raw[$i]);

		// read the public exponent
		$public = (int) expect($raw[$i], "publicExponent: "); 

		// read the private exponent
		expect($raw[$i + 1], "privateExponent:");
		for($i += 2; $raw[$i][0] == ' '; $i++) $privateRaw .= trim($raw[$i]);

		// Just to make sure
		expect($raw[$i], "prime1:");

		// Conversion to decimal format for bcmath 
		$modulus = bc_hexdec($modulusRaw);
		$private = bc_hexdec($privateRaw);

		return array($keylength, $modulus, $public, $private);
	}

	/*
	 * Convert a hexadecimal number of the form "XX:YY:ZZ:..." to decimal 
	 * Uses BCmath, but the standard normal hexdec function for the components
	 */
	function bc_hexdec($hex)
	{
		$coefficients = explode(":", $hex);
		
		$i = 0;
		$result = 0;
		foreach(array_reverse($coefficients) as $coefficient)
		{
			$mult = bcpow(256, $i++);
			$result = bcadd($result, bcmul(hexdec($coefficient), $mult));
		}

		return $result;
	}

	/*
	 * If the string has the given prefix, return the remainder. 
	 * If not, die with an error
	 */
	function expect($str, $prefix)
	{
		if(substr($str, 0, strlen($prefix)) == $prefix)
			return substr($str, strlen($prefix));
		else
			die("Error: expected $prefix");
	}
?>
