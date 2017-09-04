<?php
	define("ROOT",dirname(dirname(dirname(__FILE__))));

	require_once ROOT . "/job/mysql.php";
	require_once ROOT . "/job/curl.php";
	require_once ROOT . "/job/json.php";

	function getWXAccesToken($APPID, $APPSECRET)
	{
		$URL = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$APPID&secret=$APPSECRET";
		return httpGet($URL);
	}

	$APPID = "wxc74675bc43c6801c";
	$APPSECRET = "d4624c36b6795d1d99dcf0547af5443d";
	$Res = getWXAccesToken($APPID, $APPSECRET);
	$Result = stringToObj($Res);

	$Name = "access_token";
	$Token = $Result->access_token;
	$UpdateTS = time();
	$ExpireTS = time()+$Result->expires_in;
	$Res = setAccessToken($Name, $Token, $UpdateTS, $ExpireTS);
	print_r($Res);