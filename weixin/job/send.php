<?php
define("ROOT", dirname(dirname(__FILE__)));
require_once ROOT ."/job/mysql.php";
require_once ROOT ."/job/curl.php";

function sendMsg($AccessToken, $OPENID, $TemplateID, $Content)
{
	$URL = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=" . $AccessToken;
	$ReqObj = array();
	$ReqObj['touser'] = $OPENID;
	$ReqObj['template_id'] = $TemplateID;
	$ReqObj['url'] = "http://www.baicu.com";
	$Data = json_decode($Content);
	$SendData = array();
	foreach ($Data as $Value) {
		$Info = explode("|", $Value);
		$SendData[$Info[0]]['value'] = $Info[1] ."\n\n";
	}
	$ReqObj['data'] = $SendData;
	return httpPOST($URL,json_encode($ReqObj));
}


$Limit = 1000;
$TemplateList = MYgetTemplate($Limit);
foreach ($TemplateList as $Template) {
	$AccessToken = "FES12yHiKDqbWBP-gJVYVOXnudvJ57YqqOBzIeG-Kb3XmhhxjQNh8fudGv_dtvxP4bShRLK76BtOGBwXmVXPtfqmMplNKx5YAb9-6YBpISq_LqEci3jARMlKVHNAQelMWWOiABAQQI";
	$OPENID = "oh_3AvyZDa2ZDgmZoLOmdYA6_m-0";

	$res = sendMsg($AccessToken,$OPENID,trim($Template['TemplateID']),$Template['Content']);
	print_r($res);die;
}



