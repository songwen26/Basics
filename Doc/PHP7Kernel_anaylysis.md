#PHP7内核剖析
##PHP基本架构
####基本实现
fpm的实现就是创建一个master进程，在master进程中创建并监听socket，然后fork出多个子进程，这些子进程各自accept请求，子进程的处理非常简单，它在启动阻塞在accept上，有请求到达后开始读取请求数据，读取完成后处理然后再返回，在这期间是不会接受其它请求的，也就是说fpm的子进程同时只能响应一个请求，只有把这个请求处理完成后才会accept下一个请求，这一点与nginx的事件驱动有很大的区别，nginx的子进程通过epoll管理套接字，如果一个请求数据还未发送完成则会处理下一个请求，即一个进程会同时连接多个请求，它是非阻塞的模型，只处理活跃的套接字。<br>
fpm的master进程与worker进程之间不会直接进行通信，master通过共享内存获取worker进程的信息，比如当前状态、已处理请求数等，当master进程要杀掉一个worker进程时则通过发送信号的方式通知worker进程。<br>
fpm可以同时监听多个端口，每个端口对应一个worker pool，而每个pool下对应多个worker进程，类似nginx中server的概念。<br>
<img src="/img/worker_pool.png" style="max-width:100%">
<p>在php-fpm.conf中通过【pool name】声明一个worker pool;</p>
	[web1]
	listen = 127.0.0.1:9000
	...
	[web2]
	listen = 127.0.0.1:9001
	...
<p>启动fpm后查看进程：ps -aux | grep fpm</p>
	root     27155  0.0  0.1 144704  2720 ?        Ss   15:16   0:00 php-fpm: master process (/usr/local/php7/etc/php-fpm.conf)
	nobody   27156  0.0  0.1 144676  2416 ?        S    15:16   0:00 php-fpm: pool web1
	nobody   27157  0.0  0.1 144676  2416 ?        S    15:16   0:00 php-fpm: pool web1
	nobody   27159  0.0  0.1 144680  2376 ?        S    15:16   0:00 php-fpm: pool web2
	nobody   27160  0.0  0.1 144680  2376 ?        S    15:16   0:00 php-fpm: pool web2
<p>具体实现上worker pool通过fpm_worker_pool_s这个结构表示，多个worker pool组成一个单链表</p>
	struct fpm_worker_pool_s {
		struct fpm_worker_pool_s * next;	//指向下一个worker pool
		struct fpm_worker_pool_config_s * config;	//conf配置:pm、max_children、start_servers...
		int listening_socket;	//监听的套接字
		...
		//以下这个值用于master定时检查、记录worker数
		struct fpm_child_s * children;	//当前pool的worker链表
		int runing_chiledren;	//当前pool的worker运行总数
		int idle_spawn_rate;
		int warn_max_children;

		struct fpm_scoreboard_s * scoreboard;	//记录worker的运行信息，比如空闲】忙碌worker数
		...
	}
FPM的初始化<br>
<p>fpm的启动流程，从main()函数开始</p>
	//sapi/fpm/fpm_main.c
	int main(int argc, char *argv[]){
		...
		//注册SAPI：将全局变量sapi_module设置为cgi_module
		sapi_startup(&cgi_sapi_module);
		...
		//执行php_module_starup()
		if (cgi_sapi_module.startup(&cgi_sapi_module) == FAILURE){
			return FPM_EXIT_SOFTWARE;
		}
		...
		//初始化
		if (0 > fpm_init(...)){
			...
		}
		...
		fpm_is_running = 1;
		fcgi_fd = fpm_run(&max_requests); //后面都是worker进程的操作，master进程不会走到下面
		parent = 0；
		...
	}
fpm_init主要有以下几个关键操作<br>
<p>
	(1)fpm_conf_init_main():<br>
	解析php-fpm.conf配置文件，分配worker pool内存结构并保存到全局变量中：fpm_worker_all_pools，各worker pool配置解析到fpm_worker_pool_s->config中。<br>
	(2)fpm_scoreboard_init_main():分配用于记录worker进程运行信息的共享内存，按照worker pool的最大worker进程数分配，每个worker pool分配一个fpm_scoreboard_s结构，pool下对应的每个worker进程分配一个fpm_scoreboard_proc_s结构，各结构的对应关系如下图：<br>
	<img src="/img/worker_pool_struct.png"><br>
	(3)fpm_signals_init_main():<br>
<pre>
static int sp[2];

int fpm_signals_init_main()
{
	struct sigaction act;
	//创建一个全双工管道
	if (0 > socketpair(AF_UNIX,SOCK_STREAM,0,sp)){
		return -1;
	}
	//注册信号处理handler
	act.sa_handler = sig_handler;
	if (0 > sigaction(SIGTERM, &act, 0) 	||
		0 > sigaction(SIGINT, &act, 0) 	||
		0 > sigaction(SIGUSR1, &act, 0) ||
		0 > sigaction(SIGUSR2, &act, 0) ||
		0 > sigaction(SIGCHLD, &act, 0) ||
		0 > sigaction(SIGQUIT, &act, 0)){
		return -1;
	}
	return 0;
}		
</pre>
	这里会通过socketpair()创建一个管道，这个管道并不是用于master与worker进程通信的，它只在master进程中使用。另外设置master的信号处理handler，当master收到SIGTERM、SIGINT、SIGUSR1、SIGUSR2、SIGCHLD、SIGQUIT这些信号时将调用sig_handler()处理：
<pre>
static void sig_handler(int signo)
{
	static const char sig_chars[NSIG + 1] = {
		[SIGTERM] = 'T',
		[SIGINT] = 'I',
		[SIGUSR1] = '1',
		[SIGUSR2] = '2',
		[SIGQUIT] = 'Q',
		[SIGCHLD] = 'C',
	};
	char s;
	...
	s = sig_chars[signo];
	//将信号通知写入管道sp[1]端
	write(sp[1], &s, sizeof(s));
	...
}
</pre>
	(4)fpm_sockets_init_main():创建每个worker pool的socket套接字。<br>
	(5)fpm_event_init_main():<br>
	启动master的事件管理，fpm实现了一个事件管理器用于管理IO、定时事件，其中IO事件通过kqueue、epoll、poll、select等管理，定时事件就是定时器，一定时间后触发某个事件。<br>
	在fpm_init()初始化完成后接下来就是最关键的fpm_run()操作了，此环节将fork子进程，启动进程管理器，另外master进程将不会再返回，只有各worker进程会返回，也就是说fpm_run()之后的操作均是worker进程的。
<pre>
int fpm_run(int *max_requests)
{
	struct fpm_worker_pool_s *wp;
	for (wp == fpm_worker_all_pools; wp; wp = wp->next){
		//调用fpm_children_make() fork子进程
		is_parent = fpm_children_create_initial(wp);
		
		if(!is_parent){
			goto run_child;
		}
	}
	//master进程将进入even循环，不再往下走
	fpm_event_loop(0);

	run_child:	//只有worker进程会到这里
	
		*max_requests = fpm_globals.max_requests;
		return fpm_globals.listening_socket;	//返回监听的套接字
}
</pre>
在fork后worker进程返回了监听的套接字继续main()后面的处理，而master将永远阻塞在fpm_event_loop()，接下来分别介绍master、worker进程的后续操作。	
</p>
####请求处理
fpm_run()执行后将fork出worker进程，worker进程返回main()中继续向下执行，后面的流程就是worker进程不断accept请求，然后执行PHP脚本并返回。整体流程如下：<br>
<ul>
<li>(1)等待请求：worker进程阻塞在fcgi_accept_request()等待请求；</li>
<li>(2)解析请求：fastcg请求到达后被worker接收，然后开始接收并解析请求数据，直到request数据完全到达；</li>
<li>
(3)请求初始化：执行php_request_startup()，此阶段会调用每个扩展的：PHP_RINIT_FUNCTION();
</li>
<li>
(4)编译、执行：由php_execute_script()完成PHP脚本的编译、执行；
</li>
<li>
(5)关闭请求：请求完成后执行php_request_shutdown(),此阶段会调用每个扩展的：PHP_RSHUTDOWN_FUNCTION(),然后进入步骤(1)等待下一个请求。
</li>
</ul>
<pre>
int main(int argc, char *argv[])
{
	...
	fcgi_fd = fpm_run(&max_requests);
	parent = 0;
	//初始化fastcgi请求
	request = fpm_init_request(fcgi_fd);
	//worker进程将阻塞在这，等待请求
	while (EXPECTED(fcgi_accept_request(request) >= 0)){
		SG(server_context) = (void *) request;
		init_request_info();

		//请求开始
		if(UNEXPECTED(php_request_startup() == FAILURE)){
			...
		}
		...
		fpm_request_executing();
		//编译、执行PHP脚本
		php_execute_script(&file_handle);
		...
		//请求结束
		php_request_shutdown((void *)0);
		...
	}
	...
	//worker进程退出
	php_module_shutdown();
	...
}
</pre>
worker进程一次请求的处理被划分为5个阶段：
<ul>
<li>FPM_REQUEST_ACCEPTING:等待请求阶段</li>
<li>FPM_REQUEST_READING_HANDERS:读取fastcg请求header阶段</li>
<li>FPM_REQUEST_INFO:获取请求信息阶段，此阶段是将请求method、query string、request uri 等信息保存到各worker进程的fpm_scorboard_proc_s结构中，此操作需要加锁，因为master进程也会操作此结构</li>
<li>FPM_REQUEST_EXECUTING:执行请求阶段</li>
<li>FPM_REQUEST_END:没有使用</li>
<li>FPM+REQUEST_FINISHED:请求处理完成</li>
</ul>
worker处理到各个阶段时将会把当前阶段更新到fpm_scoreboard_proc_s->request_stage,master进程正是通过这个标识判断worker进程是否空闲的。
####进程管理
master管理worker进程。三种不同的进程管理方式：<br>
<ul>
<li>
static:这种方式比较简单，在启动时master按照pm.start_server初始化一定数量的worker进程，即worker进程数是固定不变的
</li>
<li>
dynamic：动态进程管理，首先在fpm启动时按照pm.start_servers初始化一定数量的worker，运行期间如果master发现空闲worker数低于pm.min_spare_servers配置数（表示请求比较多，worker处理不过来了）则会fork worker进程，但总的worker数不能超过pm.max_children,如果master发现空闲worker数超过了pm.max_spare_servers（表示闲着的worker太多了）则会杀死一些worker，避免占用过多资源，master通过这个4个值来控制worker数
</li>
<li>
ondemand:这种方式一般很少用，在启动时不分配worker进程，等到有请求了后再通知master进程fork worker进程，总的worker数不超过pm.max_children，处理完成后worker进程不会立即退出，当空闲时间超过pm.process_idle_timeout后再退出
</li>
</ul>
前面介绍到在fpm_run() master进程将进入 fpm_event_loop():<br>
<pre>
void fpm_event_loop(int err)
{
	//创建一个io read的监听事件，这里监听的就是在fpm_init()阶段中通过socketpair()创建管道sp[0]
	//当sp[0]可读时将回调fpm_got_signal()
	fpm_event_set(&signal_fd_event, fpm_signals_get_fd(), FPM_EV_READ, &fpm_got_signal, NULL);
	fpm_event_add(&signal_fd_event, 0);

	//如果php-fpm.conf配置了request_terminate_timeout则启动心跳检查
	if (fpm_globals.heartbeat > 0){
		fpm_pctl_heartbeat(NULL, 0, NULL);
	}
	//定时触发进程管理
	fpm_pctl_perform_idle_server_maintenace_heartbeat(NULL, 0, NULL);
	
	//进入事件循环。master进程将阻塞在此
	while (1){
		...
		//等待IO事件
		ret = module->wair(fpm_event_queue_fd, timeout);
		...
		//检查定时器事件
		...
	}
}
</pre>
<p>(1)sp[1]管道可读事件：</p>
在fpm_init()阶段master曾创建了一个全双工的管道：sp,然后在这里创建了一个sp[0]可读的事件，当sp[0]可读时将交由fpm_got_signal()处理，向sp[1]写数据时sp[0]才会可读，那么什么时机会向sp[1]写数据呢？前面提到的：当master收到注册的那几种信号时会写入sp[1]端，这个时候将触发sp[0]可读事件。<br>
<img src="/img/master_event_1.png">
<ul>
这个事件是master用于处理信号的，我们根据master注册的信号逐个看下不同的用途：
<li>
SIGINT/SIGTERM/SIGOUT:退出fpm，在master收到退出信号后将向所有的worker进程发送退出信号，然后master退出
</li>
<li>
SIGUSR1:重新加载日志文件，生产环境中通常会对日志进行分割，切割后会生成一个新的日志文件，如果fpm不重新加载将无法继续写入日志，这个时候就需要向master发送一个USR1的信号
</li>
<li>
SIGUSR2：重启fpm，首先master也是会向所有的worker进程发送退出信号，然后master会调用execvp()重新启动fpm，最后旧的master退出
</li>
<li>
SIGCHLD:这个信号是子进程退出时操作系统发送给父进程的，子进程退出时，内核将子进程置为僵尸状态，这个进程称为僵尸进程，它只保留最小的一些内核数据结构，以便父进程查询子进程的退出状态，只有当父进程调用wait或者waitpid函数查询子进程退出状态后子进程才告终止，fpm中当worker进程因为异常原因（比如coredump了）退出而非master主动杀掉时master将受到此信号，这个时候父进程将调用waitpid()查下子进程的退出，然后检查下是不是需要重新fork新的worker
</li>
</ul>
<p>(2)fpm_pctl_perform_idle_server_maintenance_heartbeat():</p>
<pre>
static void fpm_pctl_perform_idle_server_maintenance(struct timeval *now)
{
	for (wp = fpm_worker_all_pools; wp; wp = wp->next){
		struct fpm_child_s *last_idle_child = NULL; //空闲时间最久的worker
		int idle = 0;	//空闲worker数
		int active = 0;	//忙碌worker数

		for (child = wp->children; child; child = child->next){
			//根据worker进程的fpm_scoreboard_proc_s->request_stage判断
			if (fpm_request_is_idle(child)){
				//找空闲时间最久的worker
				...
				idle++;
			}else{
				active++;
			}
		}
		...
		//ondemand模式
		if (wp->config->pm == PM_STYLE_ONDEMAND){
			if (!last_idle_child) continue;
			
			fpm_request_last_activity(last_idle_child, &last);
			fpm_clock_get(&now);
			if (last.tv_sec < now.tv_sec - wp->config->pm_process_idle_timeout){
				 //如果空闲时间最长的worker空闲时间超过了process_idle_timeout则杀掉该worker
				last_idle_child->idle_kill = 1;
				fpm_pctl_kill(last_idle_chile->pid, FPM_PCTL_QUIT);
			}
			continue;
		}
		//dynamic
		if (wp->config->pm != PM_STYLE_DYNAMIC) continue;
		if (idle > wp->config->pm_max_spare_servers && last_idle_child){
			//空闲worker太多了，杀掉
            last_idle_child->idle_kill = 1;
            fpm_pctl_kill(last_idle_child->pid, FPM_PCTL_QUIT);
            wp->idle_spawn_rate = 1;
            continue;
		}
		if (idle < wp->config->pm_min_spare_servers) {
            //空闲worker太少了，如果总worker数未达到max数则fork
            ...
        }
	}
}
</pre>
<p>(3)fpm_pctl_hearbeat():</p>
这个事件是用于限制worker处理单个请求最大耗时的，php-fpm.conf中有一个request_terminate_timeout的配置项，如果worker处理一个请求的总时长超过了这个值那么master将会向此worker进程发送kill-TERM信号杀掉worker进程，此配置单位为秒，默认值为0表示关闭此机制，另外fpm打印的slow log也是在这里完成的。<br>
<pre>
static void fpm_pctl_check_request_timeout(struct timeval *now)
{
	struct fpm_worker_pool_s *wp;

	for (wp = fpm_worker_all_pools; wp; wp = wp->next){
		int terminate_timeout = wp->config->request_teminate_timeout;
		int slowlog_timeout = wp->config->request_slowlog_timeout;
		struct fpm_child_s *child;

		if (terminate_timeout || slowlog_timeout){
			for (child = wp->children; child; child = child->next){
				//检查当前当前worker处理的请求是否超时
				fpm_request_check_timed_out(child, now, terminate_timeout, slowlog_timeout);
			}
		}
	}
}
</pre>
除了上面这几个事件外还有一个没有提到，那就是ondemand模式下master监听的新请求到达的事件，因为ondemand模式下fpm启动时是不会预创建worker的，有请求时才会生成子进程，所以请求到达时需要通知master进程，这个事件是在fpm_children_create_initial()时注册的，事件处理函数为fpm_pctl_on_socket_accept()。
####执行流程
<img src="/img/php.png">
<ul>
<li>模块初始化阶段</li>
<li>请求初始化阶段</li>
<li>执行PHP脚本阶段</li>
<li>请求结束阶段</li>
<li>模块关闭阶段</li>
</ul>
##变量
变量是一个语言实现的基础，变量有两个组成部分：变量名、变量值，PHP中可以将其对应为：zval、zend_value,这两个概念一定要区分开，PHP中变量的内存是通过引用计数进行管理的，而且PHP7中引用计数是在zend_value而不是zval上，变量之间的传递赋值通常也是针对zend_value。<br>
####变量的基础结构
<pre>
//zend_types.h
typedef struct _zval_struct zval;

typedef union _zend_value {
	zend_long 			lval;		//int整形
	double 	  			dval;		//浮点型
	zend_refcounted		*counted;
	zend_string 		*str;		//string字符串
	zend_array 			*arr;		//array数组
	zend_object 		*obj;		//object对象
	zend_resource 		*res;		//resource资源类型
	zend_reference 		*ref;		//引用类型，通过&$var_name定义的
	zval 				*zv;		
	void 				*ptr;
	zend_class_entry 	*ce;
	zend_function		*func;
	struct {
		uint32_t w1;
		uint32_t w2;
	} ww;
} zend_value;

struct _zval_struct {
	zend_value			value;		//变量实际的value
	union {
		struct {
			ZEND_ENDIAN_LOHT_4( //这个是为了兼容大小字节序，小字节序就是下面的顺序，大字节序则下面4个顺序翻转
				zend_uchar	type,			//变量类型
				zend_uchar	type_flags,		//类型掩码，不同的类型会有不同的几种属性，内存管理会用到
				zend_uchar  const_flags,
				zend_uchar	reserved)    	//call info, zend执行流程会用到
		} v;
		uint32_t	type_info;	//上面4个值得组合值，可以直接根据type_info取得4个对应位置的值
	} u1;
	union {
		uint32_t	var_flags;	
		uint32_t	next;			//哈希表中解决哈希冲突时用到
		uint32_t	cache_slot;		/* literal cacahe slot */
		uint32_t	lineno;			/* line number (for ast nodes) */
		uint32_t	num_args;		/* arguments number for EX(This) */
		uint32_t	fe_pos;			/* foreach position */
		uint32_t	fe_iter_idx;	/* foreach iterator index */
	} u2;	//一些辅助值
};
</pre>
zval 结构比较简单，内嵌一个union类型的zend_value保存具体变量类型的值或指针，zval中还有两个union:u1、u2 :<br/>
<ul>
<li>
	u1:它的意义比较直接，变量的类型就通过u1.v.type区分，另外一个值type_flags为类型掩码，在变量的内存管理、gc机制中会用到，第三部分会详细分析，至于后面两个const_flags、reserved暂且不管。
</li>
<li>
	u2:这个值纯粹是个辅助值，假如zval只有：value、u1两个值，整个zval的大小也会对齐到16byte，既然不管有没有u2大小都是16byte，把多余的4byte拿出来 用于一些特殊用途还是很划算的，比如next在哈希表解决哈希冲突时会用到，还有fe_pos在foreach会用到……
</li>
从zend_value可以看出，除了long、double类型直接存储值外，其他类型都为指针，指向各自的结构。
</ul>
####类型
zval.u1.type类型：<br>
<pre>
/* regular data types */
#define IS_UNDEF		0
#define IS_NULL			1
#define IS_FALSE		2
#define IS_TEUE			3
#define IS_LONG			4
#define IS_DOUBLE		5
#define IS_STRING		6
#define IS_ARRAY		7
#define IS_OBJECT		8
#define IS_RESOURCE		9
#define IS_REFERENGE	10

/* constant expressions */
#define IS_CONSTANT		11
#define IS_CONSTANT_AST	12

/* fake type */
#define _IS_BOOL		13
#define IS_CALLABLE		14

/* internal types */
#define IS_INDIRECT		15
#define IS_PTR			17
</pre>
####标量类型
最简单的类型是true、long、double、null，其中true、false、null没有value，直接跟type区分，而long、double的值则直接存在value中：zend_long、double，也就是标量类型不需要额外的value指针。
####字符串
PHP中字符串通过zend_string表示：<br>
<pre>
struct _zend_string {
	zend_refcounted_h gc;
	zend_ulong h;		/* hash value */
	size_t	len;
	char val[1];
};
</pre>
<ul>
<li>
gc:变量引用信息，比如当前value的引用数，所有用到引用计数的变量类型都会有这个结构。
</li>
<li>
h:哈希值，数组中计算索引时会使用到。
</li>
<li>
len:字符串长度，通过这个值保证二进制安全。
</li>
<li>
val:字符串内容，变长struct，分配时按len长度申请内存。
</li>
</ul>
事实上字符串又可具体分为几类：IS_STR_PERSISTENT(通过malloc分配的)、IS_STR_INTERNED(php代码里写的一些字面量，比如函数名、变量值)、IS_STR_PERMANENT(永久值，生命周期大于request)、IS_STR_CONSTANT(常量)、IS_STR_CONSTANT_UNQUALIFIED，这个信息通过flag保存：zval.value->gc.u.flags，后面用到的时候再具体分析。
####数组
array是PHP中非常强大的一个数据结构，它的底层实现就是普通的有序HashTable。这里简单看下它的结构。<br>
<pre>
typedef struct _zend_array HashTable;

struct _zend_array {
	zend_refcounted_h gc;	//引用计数信息，与字符串相同
	union {
		struct {
			ZEND_ENDIAN_LOHI_4(
				zend_uchar 		flags,
				zend_uchar		nApplyCount,
				zend_uchar		nIteratorsCount,
				zend_ucahr		reserve) 
		} v;
		uint32_t	flags;
	} u;
	uint32_t		nTableMask;		//计算bucket索引时的掩码
	Bucket			*arData;		//bucket数组
	uint32_t		nNumUsed;		//已用bucket数
	uint32_t		nNumOfElements; //已有元素数，nNumOfElements <= nNumUsed，因为删除的并不是直接从arData中移除
	uint32_t		nTableSize;		//数组的大小，为2^n
	uint32_t        nInternalPointer; //数值索引
	zend_long       nNextFreeElement;
	dtor_func_t     pDestructor;
};
</pre>
####对象/资源
<pre>
struct _zend_object {
	zend_refcounted_h gc;
	uint32_t	handle;
	zend_class_entry *ce;	//对象对应得class类
	const zend_object_handlers *handlers;
	HashTable	*properties;	//对象属性哈希表
	zval	properties_table[1];
};

struct _zend_resource {
	zend_refcounted_h gc;
	int		handle;
	int 	type;
	void	*ptr;
};
</pre>
对象比较常见，资源指的是tcp连接、文件句柄等等类型，这种类型比较灵活，可以随意定义struct,通过ptr指向。
####引用
引用是PHP中比较特殊的一种类型，它实际是指向另一个PHP变量，对它的修改会直接改动实际指向的zval，可以简单的理解为C中的指针，在PHP中通过&操作符产生一个引用变量，也就是说不管以前的类型是什么，&首先会创建一个zend_reference结构，其内嵌了一个zval,这个zval的value指向原来zval的value（如果是布尔、整形、浮点则直接复制原来的值）,然后将原来zval的类型修改为IS_REFERENCE,原zval的value指向新创建的zend_reference结构。<br>
<pre>
struct _zend_reference {
	zend_refcounted_h 	gc;
	zva			   	  	val;
};
</pre>
结构非常简单，除了公共部分zend_refcounted_h外只有一个val，举个示例看下具体的结构关系：<br>
<pre>
$a = "time:" . time();      //$a    -> zend_string_1(refcount=1)
$b = &$a;                   //$a,$b -> zend_reference_1(refcount=2) -> zend_string_1(refcount=1)
</pre>
最终的结果如图：
<img src="/img/zend_ref.png">
注意：引用只能通过&产生，无法通过赋值传递，比如：<br>
<pre>
$a = "time:" . time();      //$a    -> zend_string_1(refcount=1)
$b = &$a;                   //$a,$b -> zend_reference_1(refcount=2) -> zend_string_1(refcount=1)
$c = $b;                    //$a,$b -> zend_reference_1(refcount=2) -> zend_string_1(refcount=2)
                            //$c    ->                                 ---
</pre>
$b = &$a这时候$a、$b的类型是引用，但是$c = $b并不会直接将$b赋值给$c，而是把$b实际指向的zval赋值给$c，如果想要$c也是一个引用则需要这么操作：<br>
<pre>
$a = "time:" . time();      //$a       -> zend_string_1(refcount=1)
$b = &$a;                   //$a,$b    -> zend_reference_1(refcount=2) -> zend_string_1(refcount=1)
$c = &$b;/*或$c = &$a*/     //$a,$b,$c -> zend_reference_1(refcount=3) -> zend_string_1(refcount=1) 
</pre>
这个也表示PHP中的 引用只可能有一层 ，不会出现一个引用指向另外一个引用的情况 ，也就是没有C语言中指针的指针的概念。<br>
###内存管理
接下来分析下变量的分配、销毁。<br>
在分析变量内存管理之前我们先自己想一下可能的实现方案，最简单的处理方式：定义变量时alloc一个zval及对应的value结构(ref/arr/str/res...)，赋值、函数传参时硬拷贝一个副本，这样各变量最终的值完全都是独立的，不会出现多个变量同时共用一个value的情况，在执行完以后直接将各变量及value结构free掉。<br>
这种方式是可行的，而且内存管理也很简单，但是，硬拷贝带来的一个问题是效率低，比如我们定义了一个变量然后赋值给另外一个变量，可能后面都只是只读操作，假如硬拷贝的话就会有多余的一份数据，这个问题的解决方案是： 引用计数+写时复制 。PHP变量的管理正是基于这两点实现的。
####引用计数
引用计数是指在value中增加一个字段refcount记录指向当前value的数量，变量复制、函数传参时并不是直接硬拷贝一份value数据，而是将refcount++,变量销毁时将refcount--，等到refcount减为0时表示已经没有变量引用这个value，将它销毁即可。<br>
<pre>
$a = "time:" . time();   //$a       ->  zend_string_1(refcount=1)
$b = $a;                 //$a,$b    ->  zend_string_1(refcount=2)
$c = $b;                 //$a,$b,$c ->  zend_string_1(refcount=3)

unset($b);               //$b = IS_UNDEF  $a,$c ->  zend_string_1(refcount=2)
</pre>
引用计数的信息位于给具体value结构的gc中：
<pre>

</pre>
