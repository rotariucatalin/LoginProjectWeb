<?php

class MCrypt 
{
	function iv_key()
	{
		$decr = array();
		$random_str = md5("fedcba9876543210");
		$decr['iv'] = substr($random_str,4,16);
		$decr['key'] = $random_str;
		return $decr;
	}

	function encrypt($str) 
	{
		if($str == "") return $str;
		$str = self::pkcs5_pad($str); 
		$data = self::iv_key();
		$iv = $data['iv'];
		$key = $data['key'];

		$td = mcrypt_module_open('rijndael-128', '', 'cbc', $iv); 
		mcrypt_generic_init($td, $key, $iv);
		$encrypted = mcrypt_generic($td, $str); 
		mcrypt_generic_deinit($td);
		mcrypt_module_close($td); 

		return bin2hex($encrypted);
	}

	function decrypt($code) 
	{
		if($code == "") return $code;
		$data = self::iv_key();
		$iv = $data['iv'];
		$key = $data['key'];

		$code = self::hex2bin($code);
		$td = mcrypt_module_open('rijndael-128', '', 'cbc', $iv); 

		mcrypt_generic_init($td, $key, $iv);
		$decrypted = mdecrypt_generic($td, $code); 
		mcrypt_generic_deinit($td);
		mcrypt_module_close($td); 
		$ut =  utf8_encode(trim($decrypted));

		return self::pkcs5_unpad($ut);
	}

	function hex2bin($hexdata) 
	{
		$bindata = ''; 
		for ($i = 0; $i < strlen($hexdata); $i += 2) 
		{
			$bindata .= chr(hexdec(substr($hexdata, $i, 2)));
		}
		return $bindata;
	}

	protected function pkcs5_pad ($text) 
	{
		$blocksize = 16;
		$pad = $blocksize - (strlen($text) % $blocksize);
		return $text . str_repeat(chr($pad), $pad);
	}

	protected function pkcs5_unpad($text)
	{
		if($text == "") return $text;
		$return_str = "";
		$pad = ord($text{strlen($text)-1});
		if ($pad > strlen($text)) 
		{
			$return_str = $text;
		}
		else
		{
			$return_str = substr($text, 0, -1 * $pad);
		}
		return utf8_decode($return_str);

	}

	function secretKey($getKey)
	{
		$secretKey = "fedcba9876543210";
		if($getKey == $secretKey)
		{
			return true;
		}
		
		return false;
	}
	function sendEnc($data)
	{
		$mcrypt = new MCrypt();
		$response = array();
		foreach($data as $key => $value)
		{
			$response[self::encrypt($key)] = self::encrypt($value);
		}
		
		return json_encode($response);
	}
	function newPassword() 
	{
		$length = 7;// code lenght
		$characters = '23456789abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ'; //chars userd
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, strlen($characters) - 1)];
		}
		return $randomString;
	}
	
	function addLog($clientId, $serverResponse)
	{
		if(!in_array($serverResponse['actionStatus'], array("running", "finished")) && $serverResponse['action'] != "waiting")
		{
			if($serverResponse['action'] != "")
			{
				$date = date("Y-m-d H:i:s");
				$add = mysqli_query($conn, "INSERT INTO action_log (al_c_id, al_pa_id, al_action_type, al_action_number, al_action_data, al_on)
						VALUES
						('$clientId', '$serverResponse[taken_by]', '$serverResponse[action]', '$serverResponse[actionNumber]', '$serverResponse[actionData]', '$date')");
			}
		}
	}

}

