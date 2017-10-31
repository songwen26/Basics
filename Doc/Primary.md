#Linux系统命令及Shell脚本
##第一章 Linux简介
<p>
	系统引导概述：
	<p>
		首先，计算机会加载BIOS,这是计算机上最接近硬件的软件，而BIOS中一项很重要的功能就是对自身硬件做一次健康检查，只有硬件没有问题，才能运行软件，操作系统也是一种软件。这种通电后开始的自检过程被称为“加电自检”。
	</p>
	<p>
		机器自检通过后，下面就要引导系统了。这个动作是BIOS设定的，BIOS默认会从硬盘上的第0柱面、第0磁道、第一个扇区中读取被称为MBR的东西，即主引导程序部分占用446字节，另外64字节是磁盘分区表DPT，最后两字节是MBR的结束位。这512字节的空间内容是由专门的分区程序产生的，比如说Windows下的fdisk.exe或者Linux下的fdisk命令，所以它不依赖于任何操作系统，而MBR中的引导程序也是可以修改的，所以可以利用这个特性实现多操作系统共存。由于RedHat、CentOS默认会使用Grub作为其引导操作系统的程序，而Grub本身又比较大，所以常见的方式是在MBR中写人Grub的地址，这样系统实际会载入Grub作为操作系统的引导程序。
	</p>
	<p>
		第三步就是顺理成章的运行Grub了。Grub最重要的功能就是根据其配置文件加载kernel镜像，并运行内核加载后的第一个程序/sbin/init，这个程序会根据/etc/inittab来进行初始化的工作。其实这里最重要的就是根据文件中设定的值来确定系统将会运行的runlevel，默认的runlevel定义在"id:3:initdefault:"中，其中的数字3说明目前的运行级别定义为3.
	</p>
	<p>
		第四步，Linux将根据/etc/inittab中定义的系统初始化配置si:sysinit:/etc/rc.d/rc.sysinit执行/etc/rc.sysinit脚本，该脚本将会设置系统变量、网络配置、并启动swap、设定/proc、加载用户自定义模块、加载内核设置等。
	</p>
	<p>
		第五步是根据第三步读到的runlevel值来启动对应得服务，如果值为3，就会运行/etc/rc.3d/下的所有脚本，如果值为5，就会运行/etc/rc5.d/下的所有脚本。
	</p>
	<p>第六步将运行/etc/rc.local，第七步会生成终端或X Window来等待用户登录</p>
</p>
##第二章 Linux用户管理
###2.2 Linux账号管理
####2.2.1 新增和删除用户
<p>	
	1、新增用户：useradd [name]
	<p>
	 	指定UID	useradd -u 555 [name]
	</p>
	<p>
		指定GID	useradd -g [group] [name]
	</p>
	<p>
		指定用户的家目录	useradd -d [route] [name]
	</p>
</p>
<p>
	2、修改密码： passwd [name]
	<p>
		普通用户修改时不需要加用户名，root修改其他人密码是添加名称
	</p>
</p>
<p>
	3、修改用户：usermod	
</p>
<p>
	4、删除用户： userdel
</p>
####2.2.2新增和删除用户组
<p>
	<p>1、增加用户组： groupadd [group]</p>
	<p>2、删除用户组： groupdel [group]</p>
</p>
####2.2.3检查用户信息
<p>
	1、查看用户：users、who、w
</p>
<p>
	2、调查用户：finger
</p>
###2.3切换用户
<p>
	切换用户指令：su
</p>
###2.4例行任务管理
####2.4.1 单一时刻执行一次任务：at
	at now + 30 minutes
	at> /sbin/shutdown -h now
	at> <EOT>
	job 1 at 2017-10-30 10:50
<p>
	其中，第一行是定义从现在开始算，30分钟后安排一个任务；第二行是到了时间后执行关机操作；第三行是个<EOT>，这不是使用键盘输入的，而是使用了组合键Ctrl+D，表示输入结束；第四行是系统提示有一个任务将在10:50被执行。可以使用atq命令查看当前使用at命令调度的任务列表，第一列是任务编码；也可以使用atrm删除已经进入任务队列的任务，在使用atq查询时，发现已经没有任务列表了。
</p>
####2.4.2 周期性执行任务： cron
<p>
	在Linux中，可以利用cron工具做这种设置。首先需要确定crond进程在运行，如果没有运行，需要先启动该进程。
</p>
	service crond start		//开启crond

	service crond status	//查看状态
<p>
	用户可通过crontab来设置自己的计划任务，并使用-e参数来编辑任务。在这之前需要先了解一下设置的“语法”，当使用crontab -e 进入编辑模式时，需要编辑执行的时间和执行的命令。在下面的示例中，前面5个*可以用来定义时间，第一个*表示分钟，可以使用的值为1~59，每分钟可以使用*和*/1表示；第二个*表示小时，可以使用的值是0~23；第三个*表日期，可以使用的值是1~31；第四个*表示月份，可以使用的值是1~12;第五个*表示星期几，可以只用值是0~6，0代表星期日；最后是执行的命令。当到了设定的时间时，系统就会自动执行定义好的命令，设置crontab的基本格式如下所示。
</p>
		* * * * *  command
		设置crontab的语法比较难以理解，这里举一些例子方便大家更好地理解，如下所示：
		* * * * * 		service httpd restart 
		*/1 * * * * 	service httpd restart
		#这两种写法其实是一致的，都是每分钟重启httpd进程。请注意，这只是个例子，除非你有确定的目的，否则不要在实际生产环节中这么设置
		* */1 * * *		service httpd restart
		#每小时重启httpd进程
		* 23-3/1 * * *	service httpd restart
		#从23点开始到三点，每小时重启httpd进程
		30 23 * * * 	service httpd restart
		#每天晚上23点30分重启httpd进程
		30 23 1 * *		service httpd restart
		#每月的第一天晚上23点30分重启httpd进程
		32 23 1 1 * 	service httpd restart
		#每年1月1日的晚上23点30分重启httpd进程
		30 23 * * 0 	service httpd restart

<p>
	设置完成后，可以使用crontab -l 查看设置的任务，也可以使用crontab -r 删除所有的任务。
</p>
##第三章 Linux 文件管理
###3.1文件和目录管理
<p>
	FHS 定义的目录结构
	<p>
		/bin/				常见的用户指令
	</p>
	<p>
		/boot/				内核和启动文件
	</p>
	<p>
		/dev/				设备文件
	</p>
	<p>
		/etc/				系统和服务的配置文件
	</p>
	<p>
		/home/				系统默认的普通用户的家目录
	</p>
	<p>
		/lib/				系统函数库目录
	</p>
	<p>
		/lost+found/		ext3 文件系统需要的目录，用于磁盘检查
	</p>
	<p>
		/mnt/				系统加载文件系统时常用的挂载点
	</p>
	<p>
		/opt/				第三方软件安装目录
	</p>
	<p>
		/proc/				虚拟文件系统
	</p>
	<p>
		/root/				root 用户的家目录
	</p>
	<p>
		/sbin/				存放系统管理命令
	</p>
	<p>
		/tmp/				临时文件的存放目录
	</p>
	<p>
		/usr/				存放与用户直接相关的文件和目录
	</p>
	<p>
		/media/				系统用来挂载光驱等临时文件系统的挂载点
	</p>
</p>
####3.1.2 文件的相关操作
<p>
	1、创建文件：touch
</p>
<p>
	2、删除文件：rm
</p>
<p>
	3、移动或者重命名文件：mv
</p>
<p>
	4、查看文件：cat
</p>
<p>
	5、查看文件头：head
</p>
<p>
	6、查看文件尾：tail
</p>
<p>
	7、文件格式转换：dos2unix
</p>
####3.1.3 目录的相关操作
<p>
	1、进入目录：cd
</p>
<p>
	2、创建目录：mkdir
</p>
<p>
	3、删除目录：rmdir和rm
</p>
<p>
	4、文件和目录复制：cp
</p>
####3.2.2 文件隐藏属性
<p>
	查看文件隐藏属性：lsattr
</p>
<p>
	增加文件属性：chattr	+a filename
</p>
####3.2.3 改变文件权限：chmod
<p>
	使用字母u、g、o来分别代表拥有者、拥有组、其他人。而对应的具体权限则使用rwx的组合来定义，增加权限使用+号，删除权限使用-号，详细权限使用=号。r 读取、 w 写、 x 执行权限。
</p>
####3.2.4 改变文件的拥有者： chown
<p>
	chown user file		#修改文件拥有者
</p>
<p>
	chown :group file	#修改文件拥有群组
</p>
<p>
	chown user:group file 	#配合使用
</p>
####3.2.5 改变文件的拥有组：chgrp
<p>
	chgrp group file 
</p>
###3.3查找文件
####3.3.1一般查找：find
<p>
	find PATH -name FILENAME
	<p>
		-name filename 			查找文件名为filename的文件
	</p>
	<p>
		-perm 					根据文件权限查找
	</p>
	<p>
		-user username			根据用户名查找
	</p>
	<p>
		-mtine	-n/+n			查找n天内/n天前更改过的文件
	</p>
	<p>
		-atime  -n/+n			查找n天内/n天前访问过得文件
	</p>
	<p>
		-ctime  -n/+n			查找n天内/n天前创建过得文件
	</p>
	<p>
		-newer filename			查找更改时间比 filename 新的文件
	</p>
	<p>
		-type b/d/c/p/l/f/s		查找块/目录/字符/管道/链接/普通/套接字文件
	</p>
	<p>
		-size					根据文件大小查找
	</p>
	<p>
		-depth n				最大的查找目录深度
	</p>
</p>
####3.3.2	数据库查找：locate
<p>
	locate命令依赖于一个数据库文件，Linux默认每天会检索一下系统中的所有文件，然后将检索到的文件记录到数据库中。在运行locate命令的时候可以直接到数据库中查找记录并打印到屏幕上，所以使用locate命令要比find命令反馈更为迅速。在执行这个命令之前一般需要执行updatedb命令，以及时更新数据库记录。
</p>
####3.3.3	查找执行文件：which/whereis
<p>
	which 用于从系统的PATH变量所定义的目录中查找可执行文件的绝对路径。
</p>
<p>
	whereis 也能查到其路径，但是和which不同的是，它不但能找出其二进制文件还能找到相关的man文件
</p>
###3.4 文件压缩和打包
####3.4.1 gzip/gunzip
<p>
	gzip/gunzip是用来压缩和解压缩单个文件的工具，使用比较简单。gzip压缩文件，gunzip解压文件
</p>
####3.4.2 tar
<p>
	tar -zcvf boot.tgz /boot		#这里-z的含义是使用gzip压缩， -c是创建压缩文件， -v 是显示当前被压缩的文件， -f 是使用文件名，也就是这里的boot.tgz文件。
</p>
<p>
	tar -zxvf boot.tgz -C /tmp		#这里-z是解压的意思。-C参数，指定压缩到的位置。
</p>
####3.4.3 bzip2
<p>
	使用bzip2压缩文件时，默认会产生以.bz2扩展名结尾的文件，这里使用-z参数进行压缩，使用-d参数进行解压缩。
</p>
####3.4.4 cpio
<p>
	该命令一般不是单独使用的，需要和find命运一同使用。当由find按照条件找出需要备份的文件列表后，可以通过管道的方式传递给cpio进行备份，生成/tmp/conf.cpio文件，然后再将生成的/tmp/conf.cpio文件中包含的文件列表完全还原回去。
	<p>
		备份： find /etc -name *.conf | cpio -cov > /tmp/conf.cpio
	</p>
	<p>
		还原： cpio --absolute-filename -icvu < /tmp/conf.cpio
	</p>
</p>
##第四章	Linux文件系统
###文件系统
<p>
	Linux支持多种不同的文件系统，包括ext2,ext3,ext4,zfs,iso8660,vfat,msdos,smbfs,nfs等。虽然文件系统多种多样，但是大部分Linux系统都具有类似的通用结构，包括超级块(superblock)、i节点(inode)、数据块(data block)、目录块(directory block)等。
</p>
###4.2 磁盘分区、创建文件系统、挂载
<p>
	磁盘使用前需要对其进行分割，这种动作被形象地称为分区。磁盘的分区分为两类，即主分区和扩展分区。受限制于磁盘的分区表大小(MBR大小为512字节，其中分区表占64字节)，由于每个分区信息使用16字节，所以一块磁盘最多只能创建4个主分区，为了能支持更多分区，可以使用扩展分区(扩展分区中可以划分更多逻辑分区)，但是即便这样，分区还是要受主分区+扩展分区最多不能超过4个的限制。在完成磁盘分区后，需要进行创建文件系统的操作，最后将该分区挂载到系统中的某个挂载点才可以使用。
</p>
####4.2.1 创建文件系统：fdisk
<p>
	使用fdisk -l查看一下发现，有一个/dev/sdb设备。下面开始对/dev/sdb进行分区操作，首先输入fdisk  /dev/sdb, 然后输入字母n，这个字母代表new，也就是新建分区；然后系统会提示是创建扩展分区(extended)还是主分区（primary）,这里选择p；在primary number 中输入数字1,代表这是第一个分区；下面要输入第一柱面开始的位置，该处输入1；然后输入最后一个柱面的位置，这里输入130表将所有的空间划给这个分区；最后输入字母w,表示将刚刚创建的分区写入分区表。
</p>
<p>
	然后在刚刚创建的分区中格式化文件系统，这里使用的是ext3文件系统。可以使用命令 mkfs -t ext3 /dev/sdb1,或简单地将此命令写成 mkfs.ext3 /dev/sdb1，这两个命令是一样的。
</p>
####4.2.2 磁盘挂载：mount
<p>
	创建了文件系统的分区后，在Linux系统下还需要经过挂载才能使用，挂载设备的命令是mount，使用方法如下(其中DEVICE是指具体的设备，MOUNT_POINT是指挂载点，挂载点只能是目录，所以首先在/root目录下创建一个newDisk目录)。
</p>
	mount DEVICE MOUNT_POINT
	
	mkdir newDisk
	#挂载设备
	mount /dev/sdb1 newDisk
	#没有参数的mount会显示所有挂载
	mount
	#……执行挂载指令
	#查看可用空间
	df -h | grep sdb1
####4.2.3 设置启动自动挂载： /etc/fstab
<p>
	echo "/dev/sdb1 /root/newDisk ext3 defaults 0 0" >>/etd/fstab
</p>
<p>
	这行命令的意思显而易见：/dev/sdb1（第一部分）挂载到 /root/newDsik（第二部分），文件系统时ext3（第三部分），使用系统默认的挂载参数（第四部分defaults）,第五部分是决定dump命令在进行备份时是否要将这个分区存档，默认设0，第六部分是设定系统启动时是否对该设备进行fsck,这个值可能是3种：1保留给根分区，其他分区使用2（检查完根分区后检查）或者0（不检查）。这样以后系统重启时，设备就会自动挂载了。
</p>
####4.2.4 磁盘检验：fsck、badblocks
<p>
	
</p>



	
	







