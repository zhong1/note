<?php
    header("Content-type: text/html; charset=utf-8");
    if (!defined('ROOT')) {
        define('ROOT', dirname(dirname(__FILE__)));
    }

    require_once ROOT . "/inc/sys_cfg.php";
    require_once ROOT . "/lib/error.php";
    require_once ROOT . "/lib/log.php";

    set_time_limit(0);
    date_default_timezone_set('Asia/shanghai');

    /**
    * ServiceSocket
    */
    class ServiceSocket
    {
    	const LISTEN_SOCKET_NUM  = 10;

    	private $master;
    	private $sockets;
    	
    	function __construct($host, $port){
    		$this->master = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
    		socket_set_option($this->master, SOL_SOCKET, SO_REUSEADDR, 1);
    		socket_bind($this->master, $host, $port);
            socket_listen($this->master, self::LISTEN_SOCKET_NUM);

            $this->sockets[0] = ['resource' => $this->master];
            while (true) {
                echo date("Y-m-d H:i:s") . " doServer Start...\r\n";
                try {
                    $this->doServer();
                } catch (Exception $e) {
                    Log::WARN("doServer Error : code" . $e->getCode() . ",message:" . $e->getMessage());
                }
            }
    	}

        //开启接入 及接收消息
    	public function doServer(){
    		$write = $except = NULL;
            $sockets = array_column($this->sockets, 'resource');
            print_r($sockets);

            $read_num = socket_select($sockets, $write, $except, NULL);

            // select作为监视函数,参数分别是(监视可读,可写,异常,超时时间),返回可操作数目,出错时返回false;
    		if ($read_num === false) {
                Log::WARN("socket select error | code:" . socket_last_error() . ",msg:".socket_strerror($err_code));
                return;
            }   

            foreach ($sockets as $socket) {
            	if ($socket == $this->master) {
                    //创建连接
                    $client = socket_accept($this->master);
                    if ($client === false) {
                        Log::WARN("socket accpt error | code : " . socket_last_error() . ",msg : ".socket_strerror($err_code));
                        continue;
                    } else {
                        self::connect($client);
                        continue;
                    }
            	}
            	else{
                    // 处理应答请求，握手请求
                    $bytes = @socket_recv($socket, $buffer, 2048, 0);
                    if ($bytes < 9) {
                        $recv_msg = $this->disconnect($socket);
                    } else {
                        //判断是否握手请求
                        if (!$this->sockets[(int)$socket]['handshake']) {
                            $this->handShake($socket, $buffer);
                            continue;
                        }
                        $recv_msg = self::parse($buffer);
                    }
                    Log::DEBUG("recv_msg : " . json_encode($recv_msg) . "\n");

                    array_unshift($recv_msg, 'receive_msg');
                    $msg = self::dealMsg($socket, $recv_msg);
                    $this->broadcast($msg);
            	}	
            }
    	}

        /**
         * 用公共握手算法握手
         *
         * @param $socket
         * @param $buffer
         *
         * @return bool
         */
        public function handShake($socket, $buffer) {
            // 获取到客户端的升级密匙
            $line_with_key = substr($buffer, strpos($buffer, 'Sec-WebSocket-Key:') + 18);
            $key = trim(substr($line_with_key, 0, strpos($line_with_key, "\r\n")));

            // 生成升级密匙,并拼接websocket升级头
            $upgrade_key = base64_encode(sha1($key . "258EAFA5-E914-47DA-95CA-C5AB0DC85B11", true));// 升级key的算法
            $upgrade_message = "HTTP/1.1 101 Switching Protocols\r\n";
            $upgrade_message .= "Upgrade: websocket\r\n";
            $upgrade_message .= "Sec-WebSocket-Version: 13\r\n";
            $upgrade_message .= "Connection: Upgrade\r\n";
            $upgrade_message .= "Sec-WebSocket-Accept:" . $upgrade_key . "\r\n\r\n";
            socket_write($socket, $upgrade_message, strlen($upgrade_message));// 向socket里写入升级信息
            $this->sockets[(int)$socket]['handshake'] = true;
            socket_getpeername($socket, $ip, $port);
            $debugMsg = [
                'hand_shake',
                $socket,
                $ip,
                $port
            ];
            Log::DEBUG(__FUNCTION__ . " debugMsg " . json_encode($debugMsg));
            // 向客户端发送握手成功消息,以触发客户端发送用户名动作;
            $msg = [
                'type' => 'handshake',
                'content' => 'done',
            ];
            $msg = $this->build(json_encode($msg));
            socket_write($socket, $msg, strlen($msg));
            return true;
        }

        public function connect($socket)
        {
            socket_getpeername($socket, $ip, $port);
            $socket_info = [
                'resource' => $socket,
                'uname' => '',
                'handshake' => false,
                'ip' => $ip,
                'port' => $port,
            ];
            $this->sockets[(int)$socket] = $socket_info;
        }

        /**
         * 客户端关闭连接
         *
         * @param $socket
         *
         * @return array
         */
        private function disconnect($socket) {
            $recv_msg = [
                'type' => 'logout',
                'content' => $this->sockets[(int)$socket]['uname'],
            ];
            unset($this->sockets[(int)$socket]);
            return $recv_msg;
        }

        //发送信息到链接端
        public function sendMessage($socket, $message){
            socket_write($socket, $message, strlen($message));
            return true;
        }

        /**
         * 解析数据
         *
         * @param $buffer
         *
         * @return bool|string
         */
        private function parse($buffer) {
            $decoded = '';
            $len = ord($buffer[1]) & 127;
            if ($len === 126) {
                $masks = substr($buffer, 4, 4);
                $data = substr($buffer, 8);
            } else if ($len === 127) {
                $masks = substr($buffer, 10, 4);
                $data = substr($buffer, 14);
            } else {
                $masks = substr($buffer, 2, 4);
                $data = substr($buffer, 6);
            }
            for ($index = 0; $index < strlen($data); $index++) {
                $decoded .= $data[$index] ^ $masks[$index % 4];
            }
            return json_decode($decoded, true);
        }

        /**
         * 将普通信息组装成websocket数据帧
         *
         * @param $msg
         *
         * @return string
         */
        private function build($msg) {
            $frame = [];
            $frame[0] = '81';
            $len = strlen($msg);
            if ($len < 126) {
                $frame[1] = $len < 16 ? '0' . dechex($len) : dechex($len);
            } else if ($len < 65025) {
                $s = dechex($len);
                $frame[1] = '7e' . str_repeat('0', 4 - strlen($s)) . $s;
            } else {
                $s = dechex($len);
                $frame[1] = '7f' . str_repeat('0', 16 - strlen($s)) . $s;
            }
            $data = '';
            $l = strlen($msg);
            for ($i = 0; $i < $l; $i++) {
                $data .= dechex(ord($msg{$i}));
            }
            $frame[2] = $data;
            $data = implode('', $frame);
            return pack("H*", $data);
        }

        /**
         * 拼装信息
         *
         * @param $socket
         * @param $recv_msg
         *          [
         *          'type'=>user/login
         *          'content'=>content
         *          ]
         *
         * @return string
         */
        private function dealMsg($socket, $recv_msg) {
            $msg_type = $recv_msg['type'];
            $msg_content = $recv_msg['content'];
            $response = [];
            switch ($msg_type) {
                case 'login':
                    $this->sockets[(int)$socket]['uname'] = $msg_content;
                    // 取得最新的名字记录
                    $user_list = array_column($this->sockets, 'uname');
                    $response['type'] = 'login';
                    $response['content'] = $msg_content;
                    $response['user_list'] = $user_list;
                    break;
                case 'logout':
                    $user_list = array_column($this->sockets, 'uname');
                    $response['type'] = 'logout';
                    $response['content'] = $msg_content;
                    $response['user_list'] = $user_list;
                    break;
                case 'user':
                    $uname = $this->sockets[(int)$socket]['uname'];
                    $response['type'] = 'user';
                    $response['from'] = $uname;
                    $response['content'] = $msg_content;
                    break;
            }
            return $this->build(json_encode($response));
        }

        /**
         * 广播消息
         *
         * @param $data
         */
        private function broadcast($data) {
            foreach ($this->sockets as $socket) {
                if ($socket['resource'] == $this->master) {
                    continue;
                }
                socket_write($socket['resource'], $data, strlen($data));
            }
        }
        
    }

    $logHandler= new CLogFileHandler(SysConfig::LOG_DIR . "/service_socket.log");
    $log = Log::Init($logHandler, SysConfig::LOG_LEVEL);

    $ws = new ServiceSocket("127.0.0.1", "9999");


