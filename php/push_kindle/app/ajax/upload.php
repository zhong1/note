<?php
    class Upload{
        private $filepath = Config::UPLOAD_FILE; //上传目录
        private $tmpPath;  //PHP文件临时目录
        private $blobNum; //第几个文件块
        private $totalBlobNum; //文件块总数
        private $fileName; //文件名

        public function __construct($tmpPath,$blobNum,$totalBlobNum,$fileName){
            $this->tmpPath =  $tmpPath;
            $this->blobNum =  $blobNum;
            $this->totalBlobNum =  $totalBlobNum;
            $this->fileName =  $fileName;
            
            $this->moveFile();
            $this->fileMerge();
        }
        
        //判断是否是最后一块，如果是则进行文件合成并且删除文件块
        private function fileMerge(){
            if($this->blobNum == $this->totalBlobNum){
                $blob = '';
                for($i=1; $i<= $this->totalBlobNum; $i++){
                    //分文件追加写入目标文件
                    $blob = file_get_contents($this->filepath.'/'. $this->fileName.'__'.$i);
                    //中文乱码问题
                    file_put_contents($this->filepath.'/'. iconv('UTF-8', 'GBK', $this->fileName), $blob, FILE_APPEND);
                }
               $this->deleteFileBlob();
            }
        }
        
       //删除文件块
        private function deleteFileBlob(){
            for($i=1; $i<= $this->totalBlobNum; $i++){
                @unlink($this->filepath.'/'. $this->fileName.'__'.$i);
            }
        }
        
        //移动文件
        private function moveFile(){
            $this->touchDir();
            $filename = $this->filepath.'/'. $this->fileName.'__'.$this->blobNum;
            move_uploaded_file($this->tmpPath, $filename);
        }
        
        //API返回数据
        public function apiReturn(){
            if($this->blobNum == $this->totalBlobNum){
                    if(file_exists($this->filepath.'/'. $this->fileName)){
                        $data['code'] = 2;
                        $data['msg'] = 'success';
                        $data['file_path'] = 'http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['DOCUMENT_URI']).str_replace('.','',$this->filepath).'/'. $this->fileName;
                    }
            }else{
                    if(file_exists($this->filepath.'/'. $this->fileName.'__'.$this->blobNum)){
                        $data['code'] = 1;
                        $data['msg'] = 'waiting for all';
                        $data['file_path'] = '';
                    }
            }
            header('Content-type: application/json');
            echo json_encode($data);
        }
        
        //建立上传文件夹
        private function touchDir(){
            if(!file_exists($this->filepath)){
                return mkdir($this->filepath);
            }
        }
    }

    if (!isset($_FILES['file']['tmp_name']) || !isset($_POST['blob_num']) || !isset($_POST['total_blob_num']) || !isset($_POST['file_name'])) {
        echo "params error";die;
    }

    $tmpName = $_FILES['file']['tmp_name'];
    $blob_num = $_POST['blob_num'];
    $totalBlobNum = $_POST['total_blob_num'];
    $fileName = trim($_POST['file_name']);

    //替换空格
    $fileName = str_replace(' ', '', $fileName);

    //实例化并获取系统变量传参
    $upload = new Upload($tmpName, $blob_num, $totalBlobNum, $fileName);
    //调用方法，返回结果
    $upload->apiReturn();