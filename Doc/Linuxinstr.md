<style type="text/css">
li	{
	list-style-type:none;
}
</style>
#Linux常用指令集合
##常用指令
<p>
ls		显示文件或目录
	<p>
	-l	列出文件详细信息（list）
	</p>
	<p>
	-a	列出当前目录下所有文件及目录，包括隐藏的a(all)
	</p>
</p>
<p>
mkdir	创建目录
	<p>
	-p	创建目录，若无父目录，则创建p(parent)
	</p>
</p>
<p>
cd	切换目录
</p>
<p>
touch	创建空文件
</p>
<p>
echo	创建带有内容的文件
</p>
<p>
cat	查看文件内容	cat filename | tail/head -n 1
	<p>
	tail -n 1	显示最后1行
	</p>
	<p>
	head -n 1	显示前面1行
	</p>
</p>
<p>
cp		拷贝
</p>
<p>
mv	移动或者重命名
</p>
<p>
rm 	删除文件
	<p>
	-r 	递归删除，可以删除子目录及文件
	</p>
	<p>
	-f	强制删除
	</p>
</p>
<p>
find	在文件系统中搜索某个文件
</p>
<p>
wc	统计文本中行数、字数、字符数
</p>
<p>
grep	在文本文件中查找某个字符串
</p>
<p>
rmdir	删除空目录
</p>
<p>
tree	树形结构显示目录，需要安装tree包
</p>
<p>
pwd		显示当前目录
</p>
<p>
ln		创建链接文件
</p>
<p>
more、less	分页显示文本文件内容
</p>
<p>
ctrl+alt+F1		命令行全屏模式
</p>
<p>
ps				将某个时间点的进程运行情况选取下来并输出
</p>
<p>
chgrp/chown		用于改变文件所属用户组
</p>
<p>
chmod			用于改变文件的权限
</p>
##系统管理命令
<p>
stat	显示指定文件的详细信息，比ls更详细
</p>
<p>
who		显示在线登录用户
</p>
<p>
whoami	显示当前操作用户
</p>
<p>
hostname	显示主机名
</p>
<p>
uname	显示系统信息
</p>
<p>
top		动态显示当前消耗资源最多的进程信息
</p>
<p>
ps		显示瞬间进程状态 ps	-aux
</p>
<p>
du		查看目录大小	du -h /home带有单位显示目录信息
</p>
<p>
df		查看磁盘大小 df -h 带有单位显示磁盘信息
</p>
<p>
ifconfig	查看网络情况
</p>
<p>
ping	测试网络连接
</p>
<p>
netstat		显示网络状态信息
</p>
<p>
man		查看其它指令的命令
</p>
<p>
clear	清屏
</p>
<p>
alias	对命令重命名	如：alias showmeit="ps -aux" ，另外解除使用unaliax showmeit
</p>
<p>
kill	杀死进程，可以先用ps或top命令查看进程id，然后再用kill命令
</p>
##打包压缩相关命令
<p>
gzip/bzip2/tar:		打包压缩
	<p>
	-c		归档文件
	</p>
	<p>
	-x		压缩文件
	</p>
	<p>
	-z		gzip压缩文件
	</p>
	<p>
	-j		bzip2压缩文件
	</p>
	<p>
	-v		显示压缩或者解压过程
	</p>
	<p>
	-f		使用档名
	</p>
</p>
##关机/重启机器
<p>
	shutdown
	<p>
	-r	关机重启
	</p>
	<p>
	-h	关机不重启
	</p>
	<p>
	now	立刻关机
	</p>
</p>
<p>
halt	关机
</p>
<p>
reboot	重启
</p>
##Linux管道
<p>
将一个命令的标准输出作为另一个命令的标准输入。也就是把几个命令组合起来使用，后一个命令除以前一个命令的结果。
</p>
<p>
例如：grep -r "close" /home/* |more 在home目录下所有文件中查找，包括close的文件，并分页输出。
</p>
##Linux软件包管理
<p>
dpkg管理工具，软件包名以.deb后缀。这种方法适合系统不能联网的情况下。
</p>
<p>
<ul>
<li>注：将tree.deb传到Linux系统中，有多种方式。VMwareTool，使用挂载方式；使用winSCP工具等；</li>
<li>APT高级软件工具。这种方法适合系统能够连接互联网的情况</li>
<li>依然以tree为例</li>
<li>sudo apt-get install tree	&nbsp;&nbsp;&nbsp;安装tree</li>
<li>sudo apt-get remove tree	&nbsp;&nbsp;&nbsp; 卸载tree</li>
<li>sudo apt-get update 	&nbsp;&nbsp;&nbsp; 更新软件</li>
<li>sudo apt-get upgrade</li>
</ul>
</p>
<p>
将.rpm文件转为.deb文件<br>

.rpm为RedHat使用的软件格式。在Ubuntu下不能直接使用，所以需要转换一下。<br>

sudo alien abc.rpm
</p>
##用户及用户管理
/etc/passwd			存储用户账号<br/>
/etc/group			存储组账号<br/>
/etc/shadow			存储用户账号的密码<br/>
/etc/gshadow		存储账户组账号的密码<br/>
useradd	 	用户名<br/>
userdel	 	用户名<br/>
adduser		用户名<br/>
groupadd	组名<br/>
groupdel	组名<br/>
passwd root	给root设置密码<br/>
/etc/profile	系统环境变量<br/>
bash_profile	用户环境变量<br/>
.bashrc			用户环境变量<br/>
<b>更改文件的用户及用户组</b><br/>
sudo chown [-R] owner[:group] {File|Directory}<br/>
例如：还以jdk-7u21-linux-i586.tar.gz为例。属于用户hadoop，组hadoop<br/>
要想切换此文件所属的用户及组。可以使用命令。<br/>
sudo chown root:root jdk-7u21-linux-i586.tar.gz<br/>
##文件权限管理
sudo chmod [u所属用户  g所属组  o其他用户  a所有用户]  [+增加权限  -减少权限]  [r  w  x]   目录名 