<?php
	//kindle邮件推送程序，建议文件在50M以下
	define('ADMIN', dirname(dirname(__FILE__)));
	require_once ADMIN . "/inc/config.php";
	require_once ADMIN . "/lib/function.php";

	//临时设置内存大小
	//TODO 大文件传输问题，邮件无法分片上传
	//ps线上服务器只保存上传文件，文件迁移后，在另一台主机进行
	ini_set('memory_limit', '512M');
		
	$util = new Util();
	$files = $util->getPushFile(AdminConfig::PUSH_DIR);
	if ($files == false) {
		echo AdminConfig::PUSH_DIR . "file not found\n";die;
	}

	$successDir = AdminConfig::PUSH_SUCCESS_DIR;
	$errorDir = AdminConfig::PUSH_SUCCESS_DIR;

	foreach ($files as $file) {

		$mail = new Mail();
		//这里填写自己的kindle帐号
		$mail->to = "8618826255945@kindle.cn";
		$mail->title = "push_kindle";
		$mail->content = "converse";

		$mail->userName = AdminConfig::EMAIL_USER_NAME;
		$mail->password = AdminConfig::EMAIL_PASSWORD;
		$mail->from = "646621268@qq.com";
		$mail->charSet = "GB2312";

		$startTS = time();
		$fileName =  $util->getFileName($file);
		$res = $mail->sendMail($file, $fileName);
		if ($res == true) {
			$moveDir = $successDir . $fileName;
			echo date("YmdHis", time()) . " push | file : " . $file . ",ok";
		}
		else{
			$moveDir = $errorDir .  $fileName;
			echo date("YmdHis", time()) . " push | file : " . $file . ",error";
		}

		if (!rename($file, $moveDir)) {
			//直接删除
			unlink($file);
			echo date("YmdHis", time()) . " move | file : " . $file . ",error";;
		}

		echo ":" . (time() - $startTS) . "s\n";
	}
	