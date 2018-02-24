<?php
	define('ROOT', dirname(dirname(__FILE__)));
	define('APP', dirname(__FILE__));
	define("VIEWS", APP . "/views");

	require_once APP . "/inc/config.php";
	require_once APP . "/route.php";

	$modName = key($_GET);
	if (!isset($routes[$modName])) {
		die("not found " . $modName);
	}

	$baseFileName = $routes[$modName];

	$fileName = APP . "/" . $baseFileName;
	if(file_exists($fileName)) {
		include $fileName ;
	}
	else {
		die("file exists " . $fileName);
	}