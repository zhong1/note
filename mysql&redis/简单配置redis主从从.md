本环境分别搭在两台不同的主机
192.168.0.1 主
192.168.0.2 从

先安装redis，详细参考<a href="https://github.com/zhong1/note/blob/master/mysql%26redis/redis%E5%AE%89%E8%A3%85.md">redis安装</a>

复制redis.conf
目录结构参考：
cp redis.conf conf/6379/6379.conf

首先修改redis配置文件6379.conf

# Redis使用后台模式
daemonize yes

# 关闭保护模式
protected-mode no

# 注释以下内容开启远程访问
# bind 127.0.0.1

# 修改启动端口为6379,最好对应你的目录名
port 6379

# 修改pidfile指向路径
pidfile /var/run/redis_6379.pid

#启动：建议保存至脚本
/usr/local/redis/bin/redis-server /usr/local/redis/etc/6379/6379.conf

#从服务器同上修改配置

#登录从服务器
redis-cli -h 192.168.29.128 -p 6379

#设置同步
slaveof 主服务器 6379

#查看主从关系
info replication
