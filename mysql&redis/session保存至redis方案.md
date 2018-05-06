php实现session保存至redis方案
===

首先我们要知道，php的session是默认是已文件的方式进行保存的；

在php.ini文件中就看出；
[Session]
; Handler used to store/retrieve data.
; http://php.net/session.save-handler
session.save_handler = files

既然php的session是在配置文件中实现的，那么肯定可以修改配置文件进行设置，或者可以通过ini_set方法进行设置

下面看看存储在redis的实现过程。

方案一：修改php.ini
session.save_handler = redis
session.save_path = "tcp://127.0.0.1:6379"
重启php环境


方案二：使用ini_set函数
ini_set("session.save_handler", "redis");
ini_set("session.save_path", "tcp://127.0.0.1:6379");

注意：如果redis带有密码；save_path修改为 tcp://127.0.0.1:6379?auth=authpwd


注意：将sesssion放入redis后，所有环境都应该开启redis扩展
