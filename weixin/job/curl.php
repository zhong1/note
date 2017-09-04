<?php
	$curlErrno = 0;
	$curlError = 'OK';

	function curlErrno() {
		global $curlErrno;
	    return $curlErrno;
	}

	function curlError() {
		global $curlError;
	    return $curlError;
	}

	function setErrorInfo($errno, $error){
		global $curlErrno;
		global $curlError;
		$curlErrno = $errno;
		$curlError = $error;
	}

	function httpGet($url) {
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_TIMEOUT, 10);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
		curl_setopt($curl, CURLOPT_URL, $url);

		$res = curl_exec($curl);
		if ($res === false) {
	        setErrorInfo(curl_errno($curl), curl_error($curl));
	        curl_close($curl);
	        return null;
	    }
		curl_close($curl);

		return $res;
	}

	function httpPostForm($url, $post_object) {
		$curl = curl_init();

		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_TIMEOUT, 10);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
		curl_setopt($curl, CURLOPT_URL, $url);

		curl_setopt($curl,CURLOPT_POST, 1);
		curl_setopt($curl,CURLOPT_POSTFIELDS, $post_object);
		$res = curl_exec($curl);
		if ($res === false) {
	        setErrorInfo(curl_errno($curl), curl_error($curl));
	        curl_close($curl);
	        return null;
	    }
		curl_close($curl);
		return $res;
	}

	function httpPost($url, $post_string) {
		$curl = curl_init();

		$header[]="Content-Type:application/x-www-form-urlencoded";
		$header[]="Content-Length: " . strlen($post_string);

		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_TIMEOUT, 10);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
		curl_setopt($curl, CURLOPT_URL, $url);

		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

		curl_setopt($curl,CURLOPT_POST, 1);
		curl_setopt($curl,CURLOPT_POSTFIELDS, $post_string);
		$res = curl_exec($curl);
		if ($res === false) {
	        setErrorInfo(curl_errno($curl), curl_error($curl));
	        curl_close($curl);
	        return null;
	    }
		curl_close($curl);

		return $res;
	}

?>
