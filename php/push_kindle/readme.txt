����˵��
	1���򵥵��ļ���Ƭ�ϴ�
	2��kindle��������

����˵����
	nginx+php

	nginx����
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
	    
	    #��������app rewrite
	    location ~* ^/app/.*$ 
	    { 
	        rewrite ^/app/(.*) /app/index.php?$1 last;
	        break;
	    }
	}

���������ļ�˵����
inc/config.php

ִ��˵����
	ǰ���ϴ����ӣ�lw.push.cn/app/upload

	���ͳ���
	php push_file_kindle.php
