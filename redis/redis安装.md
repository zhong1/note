本环境在centos

#编译和安装所需要的包
yum install gcc tcl

#下载所需安装包去官网下载即可 移动到你需要安装的目录 我是安装在/usr/local/redis  
wget http://download.redis.io/releases/redis-4.0.9.tar.gz  

#解压  
tar -zxvf redis-4.0.9.tar.gz   

#进入目录  
cd redis-4.0.9/  

# 编译安装Redis  
make PREFIX=/user/local/redis/redis-4.0.9 install
