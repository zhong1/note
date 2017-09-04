<?php
define("ROOT", dirname(dirname(__FILE__)));
require_once ROOT ."/job/PHPExcelReadFilter.php";
require_once ROOT ."/job/mysql.php";

function getFileType($File) 
{ 
	return substr(strrchr($File, '.'), 1); 
}

while (true) {
	$ScanDIR = ROOT ."\job\work\\";
	$OKDIR = ROOT . "\job\ok\\";
	$WorkDir = scandir($ScanDIR);
	foreach ($WorkDir as $FileName) {
		if ($FileName === "." || $FileName === "..") {
				continue;
		}
		$FileType = getFileType($FileName);

		if ($FileType != "xls" && $FileType != "xlsx") {
				continue;
		}

		$RowSize = 200;
		$StartRow = 2;
		$EndRow = $RowSize;
		$Reader = new PHPExcelReadFilter();
		while (true) {
			$Res = $Reader->readFromExcel($ScanDIR . $FileName,$StartRow,$EndRow);

			if(empty($Res)) {
				rename($ScanDIR . $FileName, $OKDIR . $FileName);
				break;
			}
			BEGIN();
			foreach ($Res as $Info) {
				$TemplateID = $Info[0];
				$Content = array();

				foreach ($Info as $key => $value) {
					if ($key == 0 || $key == 1) {
						continue;
					}
					$Content[] = $value;
				}

				$Status = 0;
				$InsertRes = InsertTemplate($TemplateID,json_encode($Content),$Status);
				if ($InsertRes != 1) {
					ROLLBACK();
					break;
				}
			}
			COMMIT();
			echo "OK\r\n";
			$StartRow = $EndRow + 1;
			$EndRow = $EndRow + $RowSize;
		}
	}

	//做完一轮暂停10秒
	usleep(1000000);
}



