1.获取对应的ca证书【腾讯云免费版】https://buy.cloud.tencent.com/ssl?fromSource=ssl

2.修改本地配置
	2.1 建立私钥存放位置	我习惯放在nginx里面
		mkdir /usr/local/nginx/key

	2.2 修改具体站点配置 www.example.cn.conf 	可以在nginx.conf中全局做
		增加ssl配置

			ssl on;
	        ssl_certificate /usr/local/nginx/key/1_www.owntools.cn_bundle.crt;
	        ssl_certificate_key /usr/local/nginx/key/2_www.owntools.cn.key;
	        ssl_session_timeout  5m;
	        ssl_protocols TLSv1 TLSv1.1 TLSv1.2;     #指定SSL服务器端支持的协议版本
	        ssl_ciphers  HIGH:!aNULL:!MD5;
	        #ssl_ciphers  ALL：!ADH：!EXPORT56：RC4+RSA：+HIGH：+MEDIUM：+LOW：+SSLv2：+EXP;    #指定加密算法
	        ssl_prefer_server_ciphers   on;    #在使用SSLv3和TLS协议时指定服务器的加密算法要优先于客户端的加密算法




FAQ：
	当在具体站点配置好ssl之后，可以使用https访问，但是不能使用默认http 80访问，添加80访问默认调整443即可
		server {
		    listen 80;
		    server_name www.owntools.cn;
		    rewrite ^(.*)$ https://${server_name}$1 permanent;
		}






