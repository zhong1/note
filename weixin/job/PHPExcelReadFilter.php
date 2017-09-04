<?php
	if(!defined('ROOT')){
		define("ROOT", dirname(dirname(__FILE__)));
	}
	require_once ROOT."/PHPExcel/PHPExcel.class.php";
	/**
	 * 读取excel过滤器类 单独文件
	 */
	class PHPExcelReadFilter implements PHPExcel_Reader_IReadFilter {
		public $startRow = 1;
		public $endRow;
		public function readCell($column, $row, $worksheetName = '') {
			if (!$this->endRow) {
					return true;
			}

			if ($row >= $this->startRow && $row <= $this->endRow) {
					return true;
			}
			return false;
		}
		/**
		 * 读取excel转换成数组
		 *
		 * @param string $excelFile 文件路径
		 * @param int $startRow 开始读取的行数
		 * @param int $endRow 结束读取的行数
		 * @return array
		 */
		public function readFromExcel($excelFile, $startRow = 1, $endRow = 100) {
			$excelType = PHPExcel_IOFactory::identify($excelFile);
			$excelReader = \PHPExcel_IOFactory::createReader($excelType);
			if(strtoupper($excelType) == 'CSV') {
					$excelReader->setInputEncoding('GBK');
			}
			if ($startRow && $endRow) {
				$excelFilter           = new PHPExcelReadFilter();
				$excelFilter->startRow = $startRow;
				$excelFilter->endRow   = $endRow;
				$excelReader->setReadFilter($excelFilter);
			}
			$phpexcel    = $excelReader->load($excelFile);
			$activeSheet = $phpexcel->getActiveSheet();
			$highestColumn      = $activeSheet->getHighestColumn(); //最后列数所对应的字母，例如第1行就是A
			$highestColumnIndex = \PHPExcel_Cell::columnIndexFromString($highestColumn); //总列数
			$data = array();
			for ($row = $startRow; $row <= $endRow; $row++) {
					for ($col = 0; $col < $highestColumnIndex; $col++) {
						$data[$row][] = (string) $activeSheet->getCellByColumnAndRow($col, $row)->getValue();
					}
					if(implode($data[$row], '') == '') {
						unset($data[$row]);
					}
			}
			return $data;
		}
	}
?>
