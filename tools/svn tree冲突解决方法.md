local file unversioned, incoming file add upon update  

解决方案如下：  
svn resolve --accept working removed_directory  
svn resolve --accept working removed_directory  
svn st  

removed_directory：你的工作目录  
