程序说明
	1、简单的文件分片上传
	2、kindle附件推送

配置说明：
	nginx+php

	nginx配置
	server {
	    listen 80;
	    server_name lw.push.cn;
	 
	    root D:/phpStudy/WWW/push_kindle;
	    index index.html index.htm index.php;
	 
	    location ~ \.php$ {
	        try_files $uri =404;
	 
	        include fastcgi.conf;
	        fastcgi_pass 127.0.0.1:9000;
	    }
	    
	    #必须配置app rewrite
	    location ~* ^/app/.*$ 
	    { 
	        rewrite ^/app/(.*) /app/index.php?$1 last;
	        break;
	    }
	}

代码配置文件说明：
inc/config.php

执行说明：
	前端上传链接：lw.push.cn/app/upload

	推送程序：
	php push_file_kindle.php
