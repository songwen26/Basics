#PHP7新特性
##标量类型声明
标量类型声明有两种模式：强制（默认）和严格模式。现在可以使用下列类型参数（无论用强制模式还是严格模式）：字符串（string），整型（int），浮点数（float），以及布尔值（bool）。它们扩充了PHP5中引入的其他类型：类名，接口，数组和回调类型。<br>
<pre>
// Coercive mode
function sumOfInts(int ...$this)
{
	return array_sum($ints);
}

var_dump(sumOfInts(2,'3',4.1));
</pre>
以上例程会输出：<br>
<pre>int(9)</pre>
要使用严格模式，一个declare声明指令必须放在文件的顶部。这意味着严格声明标量是基于文件配置的。这个指令不仅影响参数的类型声明，也影响到函数的返回值声明。<br>
##返回值类型声明
PHP7增加了对返回类型声明的支持。类似于参数类型声明，返回类型声明了函数返回值的类型。可用的类型与参数声明中可用的类型相同。<br>
<pre>
function arraysSum(array ...$arrays):array
{
	return array_map(function(array $array):int {
		return array_sum($array);
	},$arrays);
}

print_r(arraysSum([1,2,3], [4,5,6], [7,8,9]));
</pre>
以上例程会输出：<br>
<pre>
Array
(
	[0] => 6
	[1] => 15
	[2] => 24 
)
</pre>
##null合并运算符
由于日常使用中存在大量同时使用三元表达式和isset()的情况，添加了null合并运算符(??)这个语法糖。如果变量存在企且值不为null，它就会返回自身的值，否则返回它的第二个操作数。<br>
<pre>
// Fetches the value of $_GET['user'] and returns 'nobody'
// if it does not exist.
$username = $_GET['user'] ?? 'nobody';
// This is equivalent to:
$username = isset($_GET['user']) ? $_GET['user'] : 'nobody';
// Coalesces can be chained: this will return the first
// defined value out of $_GET['user'], $_POST['user'], and
// 'nobody'.
$username = $_GET['user'] ?? $_POST['user'] ?? 'nobody';
</pre>
##太空船操作符 （组合比较符）
太空船操作符用于比较两个表达式。当$a小于、等于、大于$b时分别返回-1,0，1。<br>
<pre>

<?php
// 整数
echo 1 <=> 1; // 0
echo 1 <=> 2; // -1
echo 2 <=> 1; // 1

// 浮点数
echo 1.5 <=> 1.5; // 0
echo 1.5 <=> 2.5; // -1
echo 2.5 <=> 1.5; // 1
 
// 字符串
echo "a" <=> "a"; // 0
echo "a" <=> "b"; // -1
echo "b" <=> "a"; // 1
?>

</pre>
##通过define()定义常量数组
<pre>
define('ANIMALS',[
	'dog',
	'cat',
	'bird'
]);

echo ANIMALS[1];	//输出 "cat"
</pre>
##匿名类
现在支持通过new class来实例化一个匿名类，这可以用于替代一些“用后即焚”的完整类定义。<br>
<pre>
<?php
interface Logger {
	public function log(string $msg);
}
class Application {
	private $logger;

	public function getLogger(): Logger {
		return $this->logger;
	}

	public function setLogger(Logger $logger){
		$this->logger = $logger;
	}
}

$app = new Application;
$app->setLogger(new class implements Logger {
	public function log(string $msg){
		echo $msg;
	}
});

var_dump($app->getLosgger());
?>
</pre>
以上例程会输出：<br>
<pre>
object(class@anonymous)#2 (0) {
}
</pre>
##Unicode codepoint 转译语法
这接受一个以16进制形式的Unicode codepoint,并打印出一个双引号或heredoc包围的UTF-8编码格式的字符串。可以接受任何有效的codepoint，并且开头的0可以省略的。<br>
<pre>
echo "\u{aa}";
echo "\u{0000aa}";
echo "\u{9999}";
</pre>
以上例程会输出：<br>
<pre>
ª
ª (same as before but with optional leading 0's)
香
</pre>
###Closure::call()
Closure::call()现在有着更好的性能，简短干练的暂时绑定一个方法到对象上闭包并调用它。<br>
<pre>
<?php
class A {private $x = 1;}

// PHP7 之前版本的代码
$getXCB = function() {return $this->x;};
$getX = $getXCB->bindTo(new A, 'A');	//中间层闭包
echo $getX();

// PHP7+ 及更高版本的代码
$getX = function() {return $this->x;};
echo $getX->call(new A);
</pre>
以上例程会输出：<br>
<pre>
1
1
</pre>
###为unserialize()提供过滤
这个特性旨在提供更安全的方式解包不可靠的数据。它通过白名单的方式来防止潜在的代码注入<br>
<pre>
<?php

// 将所有的对象都转换为 __PHP_Incomplete_Class 对象
$data = unserialize($foo, ["allowed_class" => false]);

// 将除 MyClass 和 MyClass2 之外的所有对象都转换为 __PHP_Incomplete_Class 对象
$data = unserialize($foo, ["allowed_classes" => ["MyClass", "MyClass2"]);

// 默认情况下所有的类都是可接受的，等同于省略第二个参数
$data = unserialize($foo, ["allowed_classes" => true]);

</pre>
###IntlChar
新增加的IntlChar类旨在暴露更多的ICU功能。这个类自身定义了许多静态方法用于操作多字符集的unicode字符。<br>
<pre>
printf('%x', IntlChar::CODEPOINT_MAX);
echo IntlChar::charName('@');
var_dump(IntlChar::ispunct('!'));

</pre>
以上例程会输出：<br>
<pre>
10ffff
COMMERCIAL AT
bool(true)
</pre>
若要使用此类，请先安装Intl扩展 
##预期
预期是向后兼用增强之前的asset()的方法。它使得在生产环境中启动断言为零成本，并且提供当断言失败时抛出特定异常的能力。<br>
老版本的API出于兼容目的将继续被维护，assert()现在是一个语言结构，它允许第一个参数是一个表达式，而不仅仅是一个待计算的 string或一个待测试的boolean。 <br>
<pre>

ini_set('assert.exception', 1);

class CustomError extends AssertionError {}

assert(false, new CustomError('Some error message'));
</pre>
以上例程会输出：<br>
<pre>
Fatal error: Uncaught CustomError: Some error message
</pre>
##Group use declarations
从同一 namespace 导入的类、函数和常量现在可以通过单个 use 语句 一次性导入了。 <br>
<pre>
// PHP 7 之前的代码
use some\namespace\ClassA;
use some\namespace\ClassB;
use some\namespace\ClassC as C;

use function some\namespace\fn_a;
use function some\namespace\fn_b;
use function some\namespace\fn_c;

use const some\namespace\ConstA;
use const some\namespace\ConstB;
use const some\namespace\ConstC;

// PHP 7+ 及更高版本的代码
use some\namespace\{ClassA, ClassB, ClassC as C};
use function some\namespace\{fn_a, fn_b, fn_c};
use const some\namespace\{ConstA, ConstB, ConstC};
</pre>
##生成器可以返回表达式
此特性基于PHP5.5版本中引入的生成器特性构建的。它允许在生产器函数中通过return语法来返回一个表达式（但是不允许返回引用值），可以通过调用Generator::getReturn()方法来取得生成器的返回值，但是这个方法只能在生成器完成产生工作以后调用一次。<br>
<pre>
$gen = (function() {
	yield 1;
	yield 2;

	return 3;
})();

foreach ($gen as $val) {
	echo $val, PHP_EOL;
}

echo $gen->getReturn(), PHP_EOL;

</pre>
以上例程会输出：<br>
<pre>
1
2
3
</pre>
在生成器中能够返回最终的值是一个非常便利的特性，因为它使得调用生成器的客户端代码可以直接得到生成器（或者其他协同计算）的返回值，相对于之前版本中客户端代码必须先检查生成器是否产生了最终的值然后再进行响应处理来得方便多了<br>








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
typedef struct _zend_refcounted_h {
	uint32_t	refcount;		/* reference counter 32-bit */
	union {
		struct {
			ZEND_ENDIAN_LOHI_3(
				zend_uchar 	type,
				zend_uchar	flags,	/* used for string & objects */
				uint_16_t	gc_info)	/*  */
		} v;
		uint32_t type_info;
	} u;
} zend_refcounted_h;
</pre>
从上面的zend_value结构可以看出并不是所有的数据类型都会用到引用计数，long、double直接都是硬拷贝，只有value是指针的那几种类型才__可能__会用到引用计数。<br>
下面再看一个例子：<br>
<pre>
$a = "hi~";
$b = $a;
</pre>
猜测一下变量$a/$b的引用情况。<br>
这个不跟上面的例子一样吗？字符串"hi~"有$a/$b两个引用，所以zend_string1(refcount=2)。但是这是错的，gdb调试发现上面例子zend_string的引用计数为0。这是为什么呢？<br>
<pre>
$a,$b -> zend_string_1(refcount=0,val="hi~")
</pre>
事实上并不是所有的PHP变量都会用到引用计数，标量：true/false/double/long/null是硬拷贝自然不需要这种机制，但是除了这几个还有两个特殊的类型也不会用到：interned string(内部字符串，就是上面提到的字符串flag：IS_STR_INTERNED)、immutable array，它们的type是IS_STRING、IS_ARRAY，与普通string、array类型相同，那怎么区分一个value是否支持引用计数呢？还记得zval.u1中那个类型掩码type_flag吗？正是通过这个字段标识的，这个字段除了标识value是否支持引用计数外还有其它几个标识位，按位分割，注意：type_flag与zval.value->gc.u.flag不是一个值。<br>
支持引用计数的value类型其zval.u1.type_flag 包含 (注意是&，不是等于)IS_TYPE_REFCOUNTED：<br>
<pre>
#define IS_TYPE_REFCOUNTED	(1<<2)
</pre>
下面具体列下哪些类型会有这个标识：<br>
<pre>
|     type       | refcounted |
+----------------+------------+
|simple types    |            |
|string          |      Y     |
|interned string |            |
|array           |      Y     |
|immutable array |            |
|object          |      Y     |
|resource        |      Y     |
|reference       |      Y     |
</pre>
simple types很显然用不到，不再解释，string、array、object、resource、reference有引用计数机制也很容易理解，下面具体解释下另外两个特殊的类型：<br>
<ul>
<li>
interned string： 内部字符串，这是种什么类型？我们在PHP中写的所有字符都可以认为是这种类型，比如function name、class name、variable name、静态字符串等等，我们这样定义:$a = "hi~";后面的字符串内容是唯一不变的，这些字符串等同于C语言中定义在静态变量区的字符串：char *a = "hi~";，这些字符串的生命周期为request期间，request完成后会统一销毁释放，自然也就无需在运行期间通过引用计数管理内存。
</li>
<li>
immutable array： 只有在用opcache的时候才会用到这种类型，不清楚具体实现，暂时忽略。
</li>
</ul>
####写时复制
引用计数，多个变量可能指向同一个value，然后通过refcount统计引用计数，这时候如果其中一个变量试图更改value的内容则会重新拷贝一份value修改，同时断开旧的指向，写时复制的机制在计算机系统中有非常广的应用，它只有在必要的时候（写）才会发生硬拷贝，可以很好的提高效率，下面从示例看下：<br>
<pre>
$a = array(1,2);
$b = $a;
$c = $a;
//发生分离
$b = [];
</pre>
最终结果：<br>
<img src="/img/zval_sep.png">
不是所有的类型都可以copy的，比如对象，资源，实时上只有string，array两种支持，与引用计数相同，也是通过zval.u1.type_flag标识value是否可复制的：<br>
<pre>
#define IS_TYPE_COPYABLE	(1<<4)
</pre>
<pre>
|     type       |  copyable  |
+----------------+------------+
|simple types    |            |
|string          |      Y     |
|interned string |            |
|array           |      Y     |
|immutable array |            |
|object          |            |
|resource        |            |
|reference       |            |
</pre>
copyable的意思是当value发生duplication时是否需要或者能够copy,这个具体有两种情形下会发生：<br>
<ul>
<li>
a.从literal变量区复制到局部变量区，比如：$a = []; 实际会有两个数组，而$a = 'hi~'; //interned string 则只有一个string
</li>
<li>
b.局部变量区分离时（写时复制）：如改变变量内容时引用计数大于1则需要分离，$a = []; $b = $a; $b[] = 1; 这里会分离，类型是array所有可以复制，如果是对象： $a = new user; $b = $a; $a->name = 'dd';这种情况是不会复制object的，$a、$b指向的对象还是同一个。
</li>
</ul>
具体literal、局部变量区变量的初始化、赋值后面编译、执行两篇文章会具体分析，这里知道变量有个copyable的属性就行。
####变量回收
PHP变量的回收主要有两种：主动销毁、自动销毁。主动销毁指的是unset，而自动销毁就是PHP的自动管理机制，在return时减掉局部变量的refcount，即使没有显示的return，PHP也会自动加上这个操作，另一个就是写时复制时会断开原来value的指向，这时候也会检查断开后旧value的refcount。
####垃圾回收
PHP变量的回收是根据refcount实现的，当unset、return时会将变量的引用计数减掉，如果refcount减到0则直接释放value，这是变量的简单gc过程，但是实际过程中出现gc无法回收导致内存泄漏的bug，先看一个例子：<br>
<pre>
$a = [1];
$a[] = &$a;
unset ($a);
</pre>
unset($a) 之前的引用关系：<br>
<img src="/img/gc_1.png">
<img src="/img/gc_2.png">
可以看到，unset($a)之后由于数组中有子元素指向$a，所以refcount>0，无法通过简单的gc机制回收，这种变量就是垃圾，垃圾回收器要处理的就是这种情况，目前垃圾只会出现在array、object两种类型中，所以只会针对这两种情况作特殊处理：当销毁一个变量时，如果发现减掉refcount后任然大于0，且类型是IS_ARRAY、IS_OBJECT则将此value放入gc可能垃圾双向链表中，等这个链表达到一定数量后启动检查程序将所有变量检查一遍，如果确定是垃圾则销毁释放。<br>
标识变量是否需要回收也通过u1.type_flag区分的：<br>
<pre>
#define IS_TYPE_COLLECTABLE
</pre>
<pre>
|     type       | collectable |
+----------------+-------------+
|simple types    |             |
|string          |             |
|interned string |             |
|array           |      Y      |
|immutable array |             |
|object          |      Y      |
|resource        |             |
|reference       |             |
</pre>
##数组
数组是PHP中非常强大、灵活的一种数据类型，它的底层实现为散列表(HashTable,也称作哈希表)，除了我们熟悉的PHP用户空间的Array类型外，内核中也随处用到散列表，比如函数、类、常量、已include文件的索引表、全局符号表等都用的HashTable存储。<br>
散列表是根据关键码值(key value)而直接进行访问的数据结构，它的key - value之间存在一个映射函数，可以根据key通过映射函数直接索引到对应的value值，它不以关键字的比较为基本操作，采用直接寻址技术(就是说，它直接通过key映射到内存地址上去的)，从而加快查找速度，在理想情况下，无须任何比较就可以找到待查关键字，查找的期望时间为O(1).<br>
###数组结构
存放记录的数组称为散列表，这个数组用来存储value，而value具体在数组中的存储位置有映射函数根据key计算确定，映射函数可以采用取模的方式，key可以通过一些譬如"times 33"的算法得到一个整形值，然后与数组总大小取模得到在散列表中的存储位置。这是个普通散列表的实现，PHP散列表的实现整体也是这个思路，只是有几个特殊的地方，下面就是PHP中HashTable的数据结构：<br>
<pre>
//Bucket：散列表中存储的元素
typedef struct _Bucket {
    zval              val; //存储的具体value，这里嵌入了一个zval，而不是一个指针
    zend_ulong        h;   //key根据times 33计算得到的哈希值，或者是数值索引编号
    zend_string      *key; //存储元素的key
} Bucket;

//HashTable结构
typedef struct _zend_array HashTable;
struct _zend_array {
    zend_refcounted_h gc;
    union {
        struct {
            ZEND_ENDIAN_LOHI_4(
                    zend_uchar    flags,
                    zend_uchar    nApplyCount,
                    zend_uchar    nIteratorsCount,
                    zend_uchar    reserve)
        } v;
        uint32_t flags;
    } u;
    uint32_t          nTableMask; //哈希值计算掩码，等于nTableSize的负值(nTableMask = -nTableSize)
    Bucket           *arData;     //存储元素数组，指向第一个Bucket
    uint32_t          nNumUsed;   //已用Bucket数
    uint32_t          nNumOfElements; //哈希表有效元素数
    uint32_t          nTableSize;     //哈希表总大小，为2的n次方
    uint32_t          nInternalPointer;
    zend_long         nNextFreeElement; //下一个可用的数值索引,如:arr[] = 1;arr["a"] = 2;arr[] = 3;  则nNextFreeElement = 2;
    dtor_func_t       pDestructor;
};
</pre>
HashTable中有两个非常相近的值：nNumUsed、nNumOfElements、nNumOfElements表示哈希表已有元素数，那这个值不跟nNumUsed一样吗？为什么要定义两个呢？实际上它们有不同的含义，当将一个元素从哈希表删除时并不会将对于的Bucker移除，而是将Bucket存储的zval改为IS_UNDEF，只有扩容时发现nNumOfElements与nNumUsed相差达到一定数量(这个数量是：ht->nNumUsed - ht->nNumOfElements > (ht->nNumOfElements >> 5))时才会将已删除的元素全部移除，重新构建哈希表。所以nNumUsed >= nNumOfElements。<br>
HashTable中另外一个非常重要的值arData，这个值指向存储元素数组的第一个Bucket，插入元素时按顺序 依次插入 数组，比如第一个元素在arData[0]、第二个在arData[1]...arData[nNumUsed]。PHP数组的有序性正是通过arData保证的，这是第一个与普通散列表实现不同的地方。<br>
既然arData并不是按key映射的散列表，那么映射函数是如何将key与arData中的value建立映射关系的呢？<br>
实际上这个散列表也在arData中，比较特别的是散列表在ht->arData内存之前，分配内存时这个散列表与Bucket数组一起分配，arData向后移动到了Bucket数组的起始位置，并不是申请内存的起始位置，这样散列表可以由arData指针向前移动访问到，即arData[-1]、arData[-2]、arData[-3]......散列表的结构是uint32_t，它保存的是value在Bucket数组中的位置。<br>
所以，整体来看HashTable主要依赖arData实现元素的存储、索引。插入一个元素时先将元素按先后顺序插入Bucket数组，位置是idx，再根据key的哈希值映射到散列表中的某个位置nIndex，将idx存入这个位置；查找时先在散列表中映射到nIndex，得到value在Bucket数组的位置idx，再从Bucket数组中取出元素。<br>
<pre>
$arr["a"] = 1;
$arr["b"] = 2;
$arr["c"] = 3;
$arr["d"] = 4;

unset($arr["c"]);
</pre>
对应的HashTable如下图所示。<br>
<img src="/img/zend_hash_1.png">
####映射函数
映射函数(即：散列函数)是散列表的关键部分，它将key与value建立映射关系，一般映射函数可以根据key的哈希值与Bucket数组大小取模得到，即key->h%ht->nTableSize，但是PHP却不是这么做的：<br>
<pre>
nIndex = key->h | ht->nTableMask;
</pre>
显然位运算要比取模更快。<br>
nTableMask为nTableSize的负数，即:nTableMask = -nTableSize，因为nTableSize等于2^n，所以nTableMask二进制位右侧全部为0，也就保证了nIndex落在数组索引的范围之内(|nIndex| <= nTableSize)：<br>
<pre>
11111111 11111111 11111111 11111000   -8
11111111 11111111 11111111 11110000   -16
11111111 11111111 11111111 11100000   -32
11111111 11111111 11111111 11000000   -64
11111111 11111111 11111111 10000000   -128
</pre>
####哈希碰撞
哈希碰撞是指不同的key可能计算得到相同的哈希值(数值索引的哈希值直接就是数值本身)，但是这些值只需要插入同一个散列表。一般解决方法是将Bucket串成链表，查找时遍历链表比较key。<br>
PHP的实现也是如此，只是将链表的指针指向转化为了数值指向，即：指向冲突元素的指针并没有直接存在Bucket中，而是保存到了value的zval中：<br>
<pre>
struct _zavl_struct {
	zend_value		value;		/* value */
	...
	union {
		uint32_t     var_flags;
        uint32_t     next;                 /* hash collision chain */
        uint32_t     cache_slot;           /* literal cache slot */
        uint32_t     lineno;               /* line number (for ast nodes) */
        uint32_t     num_args;             /* arguments number for EX(This) */
        uint32_t     fe_pos;               /* foreach position */
        uint32_t     fe_iter_idx;          /* foreach iterator index */
	} u2;
}
</pre>
当出现冲突时将原value的位置保存到新value的zval.u2.next中，然后将新插入的value的位置更新到散列表，也就是后面冲突的value始终插入header。所以查找过程类似：<br>
<pre>
zend_ulong h = zend_string_hash_val(key);
uint32_t idx = ht->arHash[h & ht->nTableMask];
while (idx != INVALID_IDX) {
    Bucket *b = &ht->arData[idx];
    if (b->h == h && zend_string_equals(b->key, key)) {
        return b;
    }
    idx = Z_NEXT(b->val); //移到下一个冲突的value
}
return NULL;
</pre>
####插入、查找、删除
这几个基本操作比较简单，不再赘述，定位到元素所在Bucket位置后的操作类似单链表的插入、删除、查找。
####扩容
散列表可存储的value数是固定的，当空间不够用时就要进行扩容了。<br>
PHP散列表的大小为2^n，插入时如果容量不够则首先检查已删除元素所占比例，如果达到阈值(ht->nNumUsed - ht->nNumOfElements > (ht->nNumOfElements >> 5)，则将已删除元素移除，重建索引，如果未到阈值则进行扩容操作，扩大小当前大小的2倍，将当前Bucket数组复制到新的空间，然后重建索引。<br>
<pre>
//zend_hash.c
static void ZEND_FASTCALL zend_hash_do_resize(HashTable *ht)
{

    if (ht->nNumUsed > ht->nNumOfElements + (ht->nNumOfElements >> 5)) {
        //只有到一定阈值才进行rehash操作
        zend_hash_rehash(ht); //重建索引数组
    } else if (ht->nTableSize < HT_MAX_SIZE) {
        //扩容
        void *new_data, *old_data = HT_GET_DATA_ADDR(ht);
        //扩大为2倍，加法要比乘法快，小的优化点无处不在...
        uint32_t nSize = ht->nTableSize + ht->nTableSize;
        Bucket *old_buckets = ht->arData;

        //新分配arData空间，大小为:(sizeof(Bucket) + sizeof(uint32_t)) * nSize
        new_data = pemalloc(HT_SIZE_EX(nSize, -nSize), ...);
        ht->nTableSize = nSize;
        ht->nTableMask = -ht->nTableSize;
        //将arData指针偏移到Bucket数组起始位置
        HT_SET_DATA_ADDR(ht, new_data);
        //将旧的Bucket数组拷到新空间
        memcpy(ht->arData, old_buckets, sizeof(Bucket) * ht->nNumUsed);
        //释放旧空间
        pefree(old_data, ht->u.flags & HASH_FLAG_PERSISTENT);
        
        //重建索引数组：散列表
        zend_hash_rehash(ht);
        ...
    }
    ...
}

#define HT_SET_DATA_ADDR(ht, ptr) do { \
        (ht)->arData = (Bucket*)(((char*)(ptr)) + HT_HASH_SIZE((ht)->nTableMask)); \
    } while (0)
</pre>
####重建散列值
当删除元素达到一定数量或扩容后都需要重建散列表，因为value在Bucket位置移动了或哈希数组nTableSize变化了导致key与value的映射关系改变，重建过程实际就是遍历Bucket数组中的value，然后重新计算映射值更新到散列表，除了更新散列表之外，这里还有一个重要的处理：移除已删除的value，开始的时候我们说过，删除value时只是将value的type设置为IS_UNDEF，并没有实际从Bucket数组中删除，如果这些value一直存在那么将浪费很多空间，所以这里会把它们移除，操作的方式也比较简单：将后面未删除的value依次前移，具体过程如下：<br>
<pre>
//zend_hash.c
ZEND_API int ZEND_FASTCALL zend_hash_rehash(HashTable *ht)
{
    Bucket *p;
    uint32_t nIndex, i;
    ...
    i = 0;
    p = ht->arData;
    if (ht->nNumUsed == ht->nNumOfElements) { //没有已删除的直接遍历Bucket数组重新插入索引数组即可
        do {
            nIndex = p->h | ht->nTableMask;
            Z_NEXT(p->val) = HT_HASH(ht, nIndex);
            HT_HASH(ht, nIndex) = HT_IDX_TO_HASH(i);
            p++;
        } while (++i < ht->nNumUsed);
    } else {
        do {
            if (UNEXPECTED(Z_TYPE(p->val) == IS_UNDEF)) {
                //有已删除元素则将后面的value依次前移，压实Bucket数组
                ......
                while (++i < ht->nNumUsed) {
                    p++;
                    if (EXPECTED(Z_TYPE_INFO(p->val) != IS_UNDEF)) {
                        ZVAL_COPY_VALUE(&q->val, &p->val);
                        q->h = p->h;
                        nIndex = q->h | ht->nTableMask;
                        q->key = p->key;
                        Z_NEXT(q->val) = HT_HASH(ht, nIndex);
                        HT_HASH(ht, nIndex) = HT_IDX_TO_HASH(j);
                        if (UNEXPECTED(ht->nInternalPointer == i)) {
                            ht->nInternalPointer = j;
                        }
                        q++;
                        j++;
                    }
                }
                ......
                ht->nNumUsed = j;
                break;
            }
            
            nIndex = p->h | ht->nTableMask;
            Z_NEXT(p->val) = HT_HASH(ht, nIndex);
            HT_HASH(ht, nIndex) = HT_IDX_TO_HASH(i);
            p++;
        }while(++i < ht->nNumUsed);
    }
}
</pre>
##静态变量

