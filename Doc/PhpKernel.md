#深入理解PHP内核
##第一部分 基本原理
###第一章准备工作和背景知识
####源码结构、阅读代码方法
#####PHP源码目录结构
***
<p>
	<b>根目录：/</b>这个目录主要包含一些说明文件以及设计方案。其实项目中的这些README文件是非常值得阅读的列如：
	<p>
		/README.PHP4-TO-PHP5-THIN-CHANGES这个文件就详细列举了PHP4和PHP5的一些差异。
	</p>
	<p>
		/CODING_STANDARDS,如果要想写PHP扩展的话，这个文件一定要阅读一下，不管你个人的代码风格是什么样，怎么样使用缩进和花括号，既然来到了这样一个团体里就应该去适应这样的规范，这样在阅读代码或者别人阅读你的 代码是都会更轻松。
	</p>
</p>
<p>
	<b>build </b>顾名思义，这里主要放置一下和源码编译相关的一些文件，比如开始构建之前的buildcon脚本等文件，还有一些检查环境的脚本等。
</p>
<p>
	<b>ext </b>官方扩展目录，包括了绝大多数PHP的函数定义和实现，如array系列，pad系列，sql系列等函数的实现，都在这个目录中。个人写的扩展在测试时也可以放到这个目录，方便测试和调试。
</p>
<p>
	<b>main /b>这里存放的就是PHP最核心的文件了,主要实现PHP的基本设施，这里和Zend引擎不一样，Zend引擎主要实现语言最核心的语言运行环境。
</p>
<p>
	<b>Zend </b>Zend引擎的实现目录，比如脚本的词法语法解析，opcode的执行以及扩展机制的实现等等。
</p>
<p>
	<b>pear </b>PHP扩展与应用仓库，包含PEAR的核心文件。
</p>
<p>
	<b>TSRM </b>PHP的线程安全是构建在TSRM库之上的，PHP实现中常见的*G宏通常是对TSRM的封装，TSRM（Thread Safa Resource Manager）线程安全资源管理器。
</p>
<p>
	<b>tests </b>PHP的测试脚本集合，包含PHP各项功能的测试文件
</p>
<p>
	<b>win32 </b>这个目录主要包含Windows平台相关的一些实现，比如sokcet的实现在Windows下和*Nix平台就不太一样，同时也包含了Windows下编译PHP相关的脚本。
</p>
<p>
	PHP的测试是使用PHP来测试，测试php脚本在/run-tests.php，这个脚本读取tests目录中phpt文件.例如下面的测试脚本：
</p>
	--TEST--
	Trivial "Hello World" test
	--FILE--
	<?php echo "Hello World"?>
	--EXPECT--
	Hello World
这段测试脚本很容易看懂，执行--FILE--下面的PHP文件，如果最终的输出是--EXPECT--所期望的结果则表示这个测试通过，可能会有读者会想，如果测试的脚本不小心触发Fatal Error,或者抛出未被捕获的异常了，因为如果在同一个进程中执行测试就会停止，后面的测试也将无法执行，php中有很多脚本隔离的方法比如：system(),exec()等函数，这样可以使用主测试进程服务调度被测脚本和检测测试结果，通过这些外部调用执行测试。php测试使用了proc_open()函数，这样就可以保证测试脚本和被测试脚本之间能隔离开。phpt文件的编写详细信息可以参考 附录E phpt文件的编写。 如果你真的那么感兴趣，那么研究下$PHP_SRC/run-tests.php脚本的实现也是不错的选择。这个测试框架刚开始 由PHP的发明者Rasmus Lerdorf编写，后来进行了很多的改进。后面可能会引入并行测试的支持。
#####PHP源码阅读工具
使用VIM + Ctags
***
<p>
	通常在Linux或其他*Nix环境我们都使用VIM作为代码编辑工具，在纯命令终端下，它几乎是无可替代的。它具有非常强大的扩展机制，在文字编辑方面基本上无所不能。
</p>
<p>
	推荐在Linux下编写代码的读者或多或少的试一试ctags。 ctags支持非常多的语言，可以将源代码中的各种符号（如:函数、宏类等信息）抽取出来做上标记并保存到一个文件中， 供其他文本编辑工具（VIM，EMACS等）进行检索。 它保存的文件格式符合UNIX的哲学（小即是美）， 使用也比较简洁：
</p>
	#在PHP源码目录(假定为/server/php-src)执行：
	cd /server/php-src
	ctags -R
	
	#小技巧：在当前目录生成的tags文件中使用的是相对路径，
	#若改用 ctags -R /server/ ,可以生成包含完整路径的ctags，就可以任意文件夹中了。

	#在 ~/.vimrc中添加：
	set tags+=/server/php-src/tags
	#或者在vim中运行命令：
	set tags+=/server/php-src/tags
上面代码会在/server/php-src目录下生成一个名为tags的文件，这个文件的格式如下：<br>

	{tagname}<Tab>{tagfile}<Tab>{tagaddress}
	EG Zend/zend_globals_macros.h /^# define EG(/;"	 d
它的每行是上面的这样一个格式，第一列是符号名（如上例的EG宏），第二列是该符号的文件位置以及这个符号所在的位置。VIM可以读取tags文件，当我们在符号上（可以是变量之类）使用CTRL+]时VIM将尝试从tags文件中检索这个符号.如果找到则根据该符号所在的文件以及该符号的位置打开该文件，并将光标定位到符号定义所在位置。这样我们就能快速的寻找到符号的定义。<br>
使用Ctrl+]就可以自动跳转至定义，Ctrl+t可以返回上一次查看位置。这样就可以快速的在代码之间“游动”了。<br>
####常用代码
在PHP的源码中经常会看到的一些很常见的宏，或者有些对于才开始接触源码的读者比较难懂的代码。这些代码在PHP的源码中出现的频率极高，基本在每个模块都会有他们的身影。<br>
#####1."##"和"#"
***
<p>
	宏是C/C++是非常强大，使用也很多的一个功能，有时用来实现类似函数内联的效果，或者将复杂的代码进行简单封装，提高可读性或可移植性等。在PHP的宏定义中经常使用双井号。下面对"##"及"#"进行详细介绍
</p>
#####双井号（##）
***
<p>
	在C语言的宏中，"##"被称为<b>连接符</b>,它是一种预处理运算符，用来把两个语言符号(Token)组合成单个语言符号。这里的语言符号不一定是宏的变量。并且双井号不能作为第一个或者最后一个元素存在。如下所示源码：
</p>
	#define PHP_FUNCTION					ZEND_FUNCTION
	#define ZEND_FUNCON(name)				ZEND_NAMED_FUNCTION(ZEND_FN(name))
	#define	ZEND_FN(name) zif_##name		
	#define ZEND_NAMED_FUNCTION(name)		void name(INTERNAL_FUNCTION_PARAMETERS)
	#define	INTERAL_FUNCTION_PARAMETES int ht, zval * return_value, zval **return_value_ptr, \
	zval *this_ptr, int return_value_used TSRML_DC

	PHP_FUNCTION(count);

	//	预处理器处理以后， PHP_FUNCTION(count);就展开为如下代码
	void zif_count(int ht, zval *return_value, zval **return_value_ptr, zval *this_ptr, int return_value_used TSRMLS_DC)
<p>
	宏ZEND_FN(name)中有一个"##"，它的作用一如之前所说，是一个连接符，将zif和宏的变量name的值连接起来。以这种连接的方式以基础，多次使用这种宏形式，可以将它当作一个代码生成器，这样可以在一定程度上减少代码密度，我们也可以将它理解为一种代码重用的手段，间接地减少不小心所造成的错误。
</p>
#####单井号(#)
***
<p>
	"#"是一种预处理运算符，它的功能是将其后面的宏参数进行<b>字符串化操作</b>，简单说就是在对它所引用的宏变量通过替换后在其左右各加上一个双引号，用比较官方的话就是讲语言符号(Token)转化为字符串。例如：
</p>
	#define STR(x) #x
	int main(int argc char** argv)
	{
		printf("%s\n", STR(It's a long string));	//输出 It's a long string
		return 0;
	}
<p>
	如前文所述，It's a long string 是宏STR 的参数，在展开后被包裹成一个字符串了。所以printf函数能直接输出这个字符串，当然这个使用场景并不是很适合，因为这种用法并没有实际的意义，实际中在宏中可能会包裹其他得逻辑，比如对字符串进行封装等等。
</p>
#####2.关于宏定义中的do-while循环
***
<p>
	PHP源码中大量使用了宏操作，比如PHP5.3新增加的垃圾收集机制中的一段代码：
</p>
	#define ALLOC_ZVAL(z)
	do
	{
		(z) = (zval*)emalloc(sizeof(zval_gc_info));
		GC_ZVAL_INIT(z);
	} while (0)
<p>
	这段代码，在宏定义中使用了do{}while(0)语句格式。在其他使用C/C++编写的程序中也会有很多这种编写宏的代码，多行宏的这种格式已经是一种公认的编写方式了。当使用do{ }while(0)时由于条件肯定为false，代码也肯定只执行一次， 肯定只执行一次的代码为什么要放在do-while语句里呢? 这种方式适用于宏定义中存在多语句的情况。 如下所示代码：
</p>
	#define TEST(a,b) a++;b++;
	if(expr)
		TEST(a,b);
	else
		do_else();
<p>代码进行预处理后，会变成：</p>
	if(expr)
		a++;b++;
	else
		do_else();
<p>
	这样if-else的结构就被破坏了if后面有两个语句，这样是无法编译通过的，那么为什么非要用do-while而不是简单的用{}括起来呢。这样也能保证if后面只有一个语句。例如上面的例子，在调用宏TEST的时候后面加了一个分号，虽然这个分号可有可无，但是出于习惯我们一般都会写上。那如果是把宏里的代码用{}括起来，加上最后的那个分号。还是不能通过编译。所以一般的多表达式宏定义中都采用do-while(0)的方式。
</p>
<p>
了解了do-while循环在宏中的作用，再来看"空操作"的定义。由于PHP需要考虑到平台的移植性和不同的系统配置，所以需要在某些时候把一些宏的操作定义为空操作。例如在sapi\thttpd\thttpd.c文件中的VEC_FREE():
</p>
	#ifdef SERIALIZE_HEADERS
		#define VEC_FREE() smart_str_free(&vec_str)
	#else
		#define VEC_FREE() do {} while (0)
	#endif
<p>
	这里涉及到条件编译，在定义了SERIALIZ_HEADERS宏的时候将VEC_FREE()定义为如上的内容，而没有定义时，不需要做任何操作，所以后面的宏将VEC_FREE()定义为一个空操作，不做任何操作，通常这样来保证一致性，或者充分利用系统提供的功能。
</p>
<p>
	有时也会使用如下的方式来定义"空操作"，这里的空操作和上面的还是一样，例如很常见的Debug日志打印宏：
</p>
	#ifdef DEBUG
	#	define LOG_MSG printf
	#else
	#	define LOG_MSG(...)
	#endif
<p>
	在编译时如果定义了DEBUG则将LOG_MSG当做printf使用，而不需要调试，正式发布时则将LOG_MSG()宏定义为空，由于宏是在预编译阶段进行处理的，所以上面的宏相当于从代码删除了。
</p>
####	#line 预处理
	#line 838 "Zend/zend_language_scanner.c"
<p>
	#line预处理用于改变当前的行号(__LINE__)和文件名(__FILE__).如上所示代码，将当前的行号改变为838，文件名Zend/zend_language_scanner.c它的作用体现在编译器的编写中，我们知道编译器对C源码编译过程中会产生一些中间文件，通过这条指令，可以保证文件名是固定的，不会被这些中间文件代替，有利于进行调试分析。
</p>
####PHP中的全局变量宏
***
<p>
	在PHP代码中经常能看到一些类似PG()，EG()之类的函数，他们都是PHP中定义的宏，这系列宏主要的作用是解决线程安全所写得全局变量包裹宏，如$PHP_SRC/main/php_globals.h文件中就包含了很多这类的宏。例如PG这个PHP的核心全局变量的宏。如下所示代码为其定义。
</p>
	#ifdef ZTS	//编译时开启了线程安全则使用线程安全库
	# define PG(V) TSRMG(core_globals_id, php_core_globals *, v)
	extern PHPAPI	int core_globals_id;
	#else
	# define PG(v) (core_globals.v)	//	否则这其实就是一个普通的全局变量
	extern ZEND_API struct _php_core_globals core_globals;
	#endif
<p>
	如上，ZTS是线程安全的标记，PHP运行时的一些全局参数， 这个全局变量为如下的一个结构体，各字段的意义如字段后的注释：
</p>
	struct _php_core_globals {
		zend_bool magic_quotes_gpc;				//是否对输入的GET/POST/Cookie数据使用自动字符串转义。
		zend_bool magic_quotes_runrtime;		//是否对运行时从外部资源产生的数据使用自动字符串转义。
		zend_bool magic_quotes_sybase;			//是否采用Sybase形式的自动字符串转义

		zend_bool safe_mode;					//是否启用安全模式
	
		zend_bool allow_call_time_pass_reference;	//是否强迫在函数调用时按引用传递参数
		zend_bool implicit_flush;				//是否要求PHP输出层在每个输出块之后自定刷新新数据
		
		long output_buffering;					//输出缓冲区大小(字节)、
	
		char *safe_mode_include_dir;			//在安全模式下，该组目录和其子目录的文件被包含时，跳过UID
		zend_bool safe_mode_gid;				//在安全模式下，默认在访问文件会做UID比较检查
		zend_bool sql_safe_mode;
		zend_bool enble_dl;						//是否允许使用dl()函数。dl()函数仅在PHP作为apache模块安装才有效。
		
		char *output_handler;					 //将所有脚本的输出重定向到一个输出处理函数。、

		char *unserialize_callback_func;		//如果解序列化处理器需要实例化一个未定义的类，这里指定的回调函数将以该未定义类的名字作为参数被unserialize()调用.
		long serialize_precision;				//将浮点型和双精度数据序列化存储时的精度（有效位数）
		
		char *safe_mode_exec_dir;   			//在安全模式下，只有该目录下的可执行程序才允许被执行系统程序的函数执行。

		long memory_limit;						//一个脚本所能够申请到的最大内存字节数(可以使用K和M作为单位)
		long max_input_time;					//每个脚本解析输入数据(POST, GET, upload)的最大允许时间(秒)。
	
		zend_bool track_errors;					//是否在变量$php_errormsg中保持最近一个错误或警告消息。
		zend_bool display_errors;				//是否将错误信息作为输出的一部分
		zend_bool display_startup_errors;		//是否显示PHP启动时的错误
		long log_errors_max_len;				//设置错误日志中附加的与错误信息相关联的错误源的最大长度。
		zend_bool ignore_repeated_errors;		//记录错误日志是否忽略重复的错误信息
		zend_bool ignore_repeated_source;		//是否在忽略重复的错误信息时忽略重复错误源
		zend_bool report_memleaks;  			//是否报告内存泄漏。
		char *error_log;    					//将错误日志记录到哪个文件中。

		char *doc_root; 						//PHP的”根目录”。
        char *user_dir; 						//告诉php在使用 /~username 打开脚本时到哪个目录下去找
        char *include_path; 					//指定一组目录用于require(), include(), fopen_with_path()函数寻找文件。
        char *open_basedir; 					// 将PHP允许操作的所有文件(包括文件自身)都限制在此组目录列表下。
        char *extension_dir;    				//存放扩展库(模块)的目录，也就是PHP用来寻找动态扩展模块的目录。	
		
		char *upload_tmp_dir;   				// 文件上传时存放文件的临时目录
        long upload_max_filesize;   			// 允许上传的文件的最大尺寸。

		char *error_append_string;  			// 用于错误信息后输出的字符串
        char *error_prepend_string; 			//用于错误信息前输出的字符串

		char *auto_prepend_file;    			//指定在主文件之前自动解析的文件名。
        char *auto_append_file; 				//指定在主文件之后自动解析的文件名。

		arg_separators arg_separator;			//PHP所产生的URL中用来分割参数的分隔符

		char *variables_order;  				// PHP注册 Environment, GET, POST, Cookie, Server 变量的顺序。
 
        HashTable rfc1867_protected_variables;  //  RFC1867保护的变量名，在main/rfc1867.c文件中有用到此变量
 
        short connection_status;    			//  连接状态，有三个状态，正常，中断，超时
        short ignore_user_abort;    			//  是否即使在用户中止请求后也坚持完成整个请求。
 
        unsigned char header_is_being_sent; 	//  是否头信息正在发送
 		
		zend_llist tick_functions;  			//  仅在main目录下的php_ticks.c文件中有用到，此处定义的函数在register_tick_function等函数中有用到。
 
        zval *http_globals[6];  				// 存放GET、POST、SERVER等信息
 
        zend_bool expose_php;   				//  是否展示php的信息
 
        zend_bool register_globals; 			//  是否将 E, G, P, C, S 变量注册为全局变量。
        zend_bool register_long_arrays; 		//   是否启用旧式的长式数组(HTTP_*_VARS)。
        zend_bool register_argc_argv;   		//  是否声明$argv和$argc全局变量(包含用GET方法的信息)。
        zend_bool auto_globals_jit; 			//  是否仅在使用到$_SERVER和$_ENV变量时才创建(而不是在脚本一启动时就自动创建)。
 
        zend_bool y2k_compliance;   			//是否强制打开2000年适应(可能在非Y2K适应的浏览器中导致问题)。
 		
		char *docref_root;  					// 如果打开了html_errors指令，PHP将会在出错信息上显示超连接，
        char *docref_ext;   					//指定文件的扩展名(必须含有’.')。
 
        zend_bool html_errors;  				//是否在出错信息中使用HTML标记。
        zend_bool xmlrpc_errors;   
 
        long xmlrpc_error_number;
 
        zend_bool activated_auto_globals[8];

		zend_bool modules_activated;    		//  是否已经激活模块
        zend_bool file_uploads; 				//是否允许HTTP文件上传。
        zend_bool during_request_startup;   	//是否在请求初始化过程中
        zend_bool allow_url_fopen;  			//是否允许打开远程文件
        zend_bool always_populate_raw_post_data;    //是否总是生成$HTTP_RAW_POST_DATA变量(原始POST数据)。
        zend_bool report_zend_debug;    		//  是否打开zend debug，仅在main/main.c文件中有使用。
 
        int last_error_type;    				//  最后的错误类型
        char *last_error_message;   			//  最后的错误信息
        char *last_error_file;  				//  最后的错误文件
        int  last_error_lineno; 				//  最后的错误行

		char *disable_functions;    			//该指令接受一个用逗号分隔的函数名列表，以禁用特定的函数。
        char *disable_classes;  				//该指令接受一个用逗号分隔的类名列表，以禁用特定的类。
        zend_bool allow_url_include;    		//是否允许include/require远程文件。
        zend_bool exit_on_timeout;  			//  超时则退出

	#ifdef PHP_WIN32
		zend_bool com_initialized;
	#endif
		long max_input_nesting_level;   		//最大的嵌套层数
        zend_bool in_user_include;  			//是否在用户包含空间
 
        char *user_ini_filename;    			//  用户的ini文件名
        long user_ini_cache_ttl;    			//  ini缓存过期限制
 
        char *request_order;    				//  优先级比variables_order高，在request变量生成时用到，个人觉得是历史遗留问题
 
        zend_bool mail_x_header;    			//  仅在ext/standard/mail.c文件中使用，
        char *mail_log;
 
        zend_bool in_error_log;	
	}
<p>
	上面的字段很大一部分是与php.ini文件中的配置项对应得。在PHP启动并读取php.ini文件时就会对这些字段进行赋值，而且用户空间的ini_get()及ini_set()函数操作的一些配置也是对这个全局变量进行操作的。
</p>
<p>
	在PHP代码的其他地方也存在很多类似的宏，这些宏和PG宏一样，都是为了将线程安全进行封装，同时通过约定的 G 命名来表明这是全局的， 一般都是个缩写，因为这些全局变量在代码的各处都会使用到，这也算是减少了键盘输入。
</p>
###第二章 用户代码的执行
####第一节生命周期和Zend引擎
#####SAPI接口
***
<p>
	SAPI指的是PHP具体应用的编程接口，就像PC一样，无论安装哪些操作系统，只要满足了PC的接口规范都可以在PC上正常运行，PHP脚本要执行有很多种方式，通过web服务器，或者直接在命令行下，也可以嵌入在其他程序中。
</p>
<p>
	通常，我们使用Apache或者Nginx这类Web服务器来测试PHP脚本，或者命令行下通过PHP解释器程序来执行。脚本执行完后，Web服务器应答，浏览器显示应答信息，或者命令行标准输出显示内容。
</p>
<p>
	脚本执行的开始都是SAPI接口实现开始的。只是不同的SAPI接口实现会完成他们特定的工作，例如Apache的mod_php SAPI实现需要初始化从Apache获取的一些信息，在输出内容是将内容返回给Apache,其他的SAPI实现也类似。
</p>
#####开始和结束
***
<p>
	PHP开始执行以后会经过两个主要的阶段：处理请求之前阶段和请求之后的结束阶段。开始阶段有两个过程：第一个过程是模拟初始化阶段（MINIT），在整个SAPI生命周期内（例如Apache启动以后的整个生命周期内或者命令行程序整个执行过程中），该过程只进行一次。第二个过程是模块激活阶段（RINIT），该过程发生在请求阶段，例如通过url请求某个页面，则在每次请求之前都会进行模块激活（RINIT请求开始）。例如PHP注册了一些扩展模块，则在MINIT阶段会回调所有模块的MINIT函数。模块在这个阶段可以进行一些初始化工作，例如注册常量，定义模块使用的类等等。模块在实现时可以通过如下宏来实现这些回调函数：
</p>
	PHP_MINIT_FUNCTION(myphpextension)
	{
		//注册常量或者类等初始化操作
		return SUCCESS;		
	}
<p>
	请求到达之后PHP初始化脚本的基本环境，例如创建一个执行环境，包括保持PHP运行过程中变量名称和值内容的符号表，以及当前所有的函数以及类等信息的符号表。然后PHP会调用所有模块的RINIT函数，在这个阶段各个模块也可以执行一些相关操作，模块的RINIT函数和MINIT回调函数类似：
</p>
	PHP_RINIT_FUNCTION(myphpexetension)
	{
		//例如记录请求开始时间
		//随后在请求结束的时间。这样我们就能够记录下处理请求所花费的时间了
		retrun SUCCESS;
	}
<p>
	请求处理完了就进入结束阶段，一般脚本执行到末尾或者通过调用exit()或die()函数，PHP都将进入结束阶段。和开始阶段对应，结束阶段也分为两个环节，一个在请求结束后停用模块(RSHUTDOWN,对应RINIT)，一个在SAPI生命周期结束（Web服务器提出或者命令行脚本执行完毕退出）时关闭模块(MSHUTDOWN,对应MINIT)。
</p>
	PHP_RESHUTDOWN_FUNCTION(myphpexetension)
	{
		//例如记录请求结束时间，并把相应的信息写入到日志文件
		return SUCCESS;
	}
#####单进程SAPI生命周期
***
<p>
	CLI/CGI模式的PHP属于单进程的SAPI模式。这类的请求在处理一次请求后就关闭。也就是只会经过如下几个环节：开始-》请求开始-》请求关闭-》结束 SAPI接口实现就完成了其生命周期。如图2.1所示：
</p>
<div class="book-img" style="text-align: center;">
<img src="/img/02-01-01-cgi-lift-cycle.png" alt="图2.1 单进程SAPI生命周期">
<div class="book-img-desc">图2.1 单进程SAPI生命周期</div>
</div>
<p>
<b>启动</b>
<ul>
<li>
初始化若干全局变量
</li>
<p>
这里的初始化全局变量大多数情况下是将其设置为NULL，有一些除外，比如设置zuf(zend_utility_functiontions),以zuf.printf_function = php_printf为例，这里的php_printf在zend_startup函数中会被赋值给zend_printf作为全局函数指针使用，而zend_printf函数通常会作为常规字符串输出使用，比如显示程序调用栈的debug_printf_backtrace就是使用它打印相关信息。
</p>
<li>
初始化若干常量
</li>
<p>
这里的常量是PHP自己的一些常量，这些常量要么是硬编码在程序中，比如PHPZ_VERSION，要么是写在配置头文件，比如PEAR_EXTENSION_DIR,这些是写在config.w32.h文件中
</p>
<li>
初始化Zend引擎和核心组件
</li>
<p>
前面提到的zend_startup函数的作用就是初始化Zend引擎，这里的初始化操作包括内存管理初始化、全局使用的函数指针初始化（如前面所说的zend_printf等），对PHP源文件进行词法分析、语法分析、中间代码执行的函数指针的赋值，初始化若干HashTable（比如函数表，常量表等），为ini文件解析做准备，为PHP源文件解析做准备，注册内置函数（如strlen，define等），注册标准常量（如E_ALL、TRUE、NULL等）、注册GLOBALS全局变量等。
</p>
<li>
解析php.ini
</li>
<p>
php_init_config的作用是读取php.ini文件，设置配置参数，加载zend扩展并注册PHP扩展函数。次函数分为如下几步：初始化参数配置表，调用当前模式下的ini初始化配置，比如CLI模式下，会做如下初始化：
</p>
	INI_DEFAULT("report_zend_debug","0");
	INI_DEFAULT("display_errors","1");
<p>
不过在其他模式下却没有这样的初始化操作。接下来会的各种操作都查找ini文件：
<p>
1、判断是否有php_ini_path_override，在CLI模式下可以通过-c参数指定此路径（在php的命令参数中-c表示在指定的路径中查找ini文件）。
</p>
<p>
2、如果没有php_ini_path_override，判断php_ini_ignore是否为非空（忽略php.ini配置，这里也就是CLI模式下有用，使用-n参数）。
</p>
<p>
3、如果不忽略ini配置，则开始处理php_ini_search_paath（查找ini文件的路径），这些路径包括CWD（当前路径，不过这种不适用CLI模式）、执行脚本所在目录、环境变量PATH和PHPRC和配置文件中的PHP_CONFIG_FILE_PATH的值。
</p>
<p>
4、在准备完查找路径后，PHP会判断现在的ini路径（php_ini_file_name）是否为文件和是否可打开。如果这里ini路径是文件并且可打开，则会使用此文件，也就是CLI模式通过-c参数指定的ini文件的优先级是最高的，其次是PHPRC指定的文件，第三是在搜索路径中查找php-%sapi-module-name%.ini文件(如CLI模式下应该是查找php-cli.ini文件)，最后才是搜索路径中查找php.ini文件
</p>
</p>
<li>
全局操作函数的初始化
</li>
<p>
php_startup_auto_globals函数会初始化在用户空间所使用频率很高的一些全局变量，如：$_GET,$_POST,$_FILES等。这里只是初始化，所调用的zend_register_autp_global函数也只是将这些变量名添加到CG（auto_globals）这个变量表。
</p>
<p>
php_startup_sapi_countent_types函数用来初始化SAPI对于不同类型内容的处理函数，这里的处理函数包括POST数据默认处理函数，默认数据处理函数等。
</p>
<li>
初始化静态构建的模块和共享模块(MINIT)
</li>
<p>
php_register_internal_extensions_func函数用来注册静态构建的模块，也就是默认加载的模块，我们可以将其认为内置模块。在PHP5.3.0版本中内置的模块包括PHP标准扩展模块（/ext/standard/目录，这里是我们用的最频繁的函数，如字符串函数，数学函数，数组操作函数等），日历扩展模块，FTP扩展模块，session扩展模块等。这些内置模块并不是一成不变的，在不同PHP模块中，由于不同时间的需求或其他影响因素会导致这些默认加载的模块会变化，比如从代码中我们就可以看到mysql、xml等扩展模块曾经或将会作为内置模块出现。
</p>
<p>
模块初始化会执行两个操作：1.将这些模块注册到已注册模块列表(module_registry)，如果注册的模块已经注册过了，PHP会报Modeule XXX already loaded的错误。2、将每个模块中包含的函数注册到函数表(CG(function_table) ),如果函数无法添加，则会报Unable to register functions, unable to load.
</p>
<p>
在注册了静态构建的模块后，PHP会注册附加的模块，不同的模式下可以加载不同的模块集，比如在CLI模式下是没有这些附加的模块的。
</p>
<p>
在内置模块和附加模块后，接下来是注册通过共享对象（比如DLL）和php.ini文件灵活配置的扩展
</p>
<p>
在所有的模块都注册后PHP会马上执行模块初始化操作（zend_startup_modules）。它的整个过程就是依次遍历每个模块，调用每个模块的模块初始化函数，也就是在本小节前面所说的用宏PHP_MINIT_FUNCTION包含的内容。
</p>
<li>禁用函数和类</li>
<p>
php_disable_functions函数来禁用PHP的一些函数。这些被禁用的函数来自PHP的配置文件的disable_functions变量。其禁用的过程是调用zend_disable_funcion函数将指定的函数名从CG（function_table）函数表中删除。
</p>
<p>
php_disable_classes函数用来禁用PHP的一些类。这些被禁用的类来自PHP的配置文件的disable_classes变量。其禁用的过程是调用zend_disable_class函数指定的类名从CG（class_tabel）类表中删除。
</p>
<li>ACTIVATION</li>
<p>
在处理了文件相关的内容，PHP会调用php_request_startup做请求初始化操作。请求初始化操作，除了图中显示的调用每个模块的请求初始化函数外，还做了较多的其它工作，其主要内容如下：
</p>
<li>激活Zend引擎</li>
<p>
gc_reset函数用来重置垃圾收集机制，当然这是在PHP5.3之后才有的。
</p>
<p>
init_compile函数用来初始化编译器，比如将编译过程中放在opcode里的数组清空，准备编译时需要用的数据结构等等。
</p>
<p>
init_executor函数用来初始化中间代码执行过程。在编译过程中，函数列表、类列表等都存放在编译时的全局变量中，在准备执行过程时，会将这些列表赋值给执行的全局变量中，如：EG(function_table)=CG(function_table);中间代码执行是在PHP的执行虚拟栈中，初始化时这些栈等都会一起被初始化。除了栈，还有存放变量的符号表（EG(symbol_table)）会被初始化为50个元素的hashtable，存放对象的EG(objects_store)被初始化了1024个元素。PHP的执行环境除了上面的一些变量外，还有错误处理，异常处理等等，这些都是在这里被初始化的。通过php.ini配置的zend_extensions也是在这里被遍历调用activate函数。
</p>
<li>激活SAPI</li>
<p>
sapi_activate函数用来初始化SG（sapi_headers）和SG（request_info），并且针对HTTP请求的方法设置一些内容，比如当请求方法为HEAD时，设置SG（request_info）.headers_only=1;次函数最重要的一个操作是处理请求的数据，其最终都会调用sapi_module.default_post_reader。而sapi_module.default_post_reader在前面的模块初始化是通过php_startup_sapi_content_types函数注册了默认处理函数为main/php_content_types.c文件中php_default_post_reader函数。此函数会将POST的原始数据写入$HTTP_RAW_POST_DATA变量
</p>
<p>
在处理了post数据后，PHP会通过sapi_module.read_cookies读取cookie的值，在CLI模式下，次函数的实现为sapi_cli_read_cookie，而在函数体中却只有一个return NULL;
</p>
<p>
如果当前模式下有设置activate函数，则运行此函数，激活SAPI，在CLI模式下此函数指针被设置为NULL
</p>
<li>环境初始化</li>
<p>
这里的环境初始化是指在用户空间中需要用到的一些环境变量初始化，这里的环境包括服务器环境、请求数据环境等。实际到我们用到的变量，就是$_POST、$GET、$_COOKIE、$_SERVER、$_ENV、$_FILES和sapi_module.default_post_reader一样，sapi_module.treat_data的值也是在模块初始化时，通过php_startup_sapi_content_types函数注册了默认数据处理函数为main/php_variables.c文件中php_default_treat_data函数。
</p>
<p>
以$_COOKIE为例，php_default_treat_data函数会对依据分隔符，将所有的cookie拆分并赋值给对应的变量。
</p>
<li>模块请求初始化</li>
<p>
PHP通过zend_activate_modules函数实现模块的请求初始化，也就是我们在图中看到Call each extension's RINIT。此函数通过遍历注册在module_registry变量中的所有模块，调用其RINIT方法实现模块的请求初始化操作。
</p>
<p>
<b>运行</b>
</p>
<p>
php_execute_script函数包含了运行PHP脚本的全部过程。
</p>
<p>
当一个PHP文件需要解析执行时，它可能会需要执行三个文件，其中包括一个前置执行文件、当前需要执行的主文件和一个后置执行文件、非当前的两个文件可以在php.ini文件通过auto_prepend_file参数和auto_append_file参数设置。如果将这两个参数设置为空，则禁用对应的执行文件。
</p>
<p>
对于需要解析执行的文件，通过zend_comoile_file(compile_file函数)做词法分析、语法分析和中间代码生成操作，返回此文件的所有中间代码。如果解析的文件有生成有效的中间代码，则调用zend_execute（execute函数）执行中间代码。如果在执行过程中出现异常并且用户有定义对这些异常的处理，则调用这些异常处理函数。在所有的操作都处理完后，PHP通过EG（return_value_ptr_ptr）返回结果。
</p>
<p>
DEACTIVATION
</p>
<p>
PHP关闭请求的过程是一个若干个关闭操作的集合，这个集合存在于php_request_shutdown函数中。这个集合包括如下内容：
<p>
1、调用所有通过reguster_shutdown_function()注册的函数。这些在关闭时调用的函数是在用户空间添加进来的。一个简单的例子，我们可以在脚本出错时调用一个统一的函数，给用户一个友好一些的页面，这个有点类似于网页的404页面。
<p>
2、执行所有可用的__destruct函数。这里的析构函数包括在对象池(EG(objects_store))中的所有对象的析构函数以及EG（symbol_table）中各个元素的析构方法。
</p>
<p>
3、将所有的输出刷出去
</p>
<p>
4、发送HTTP应答头。这也是一个输出字符串的过程，只是这个字符串可能符合某些规范。
</p>
<p>
5、遍历每个模块的关闭请求方法，执行模块的请求关闭操作，这就是我们在图中看到的Call each extension's RSHUTDOWN.
</p>
<p>
6、销毁全局变量表（PG(http_globals)）的变量
</p>
<p>
7、通过zend_deactivate函数，关闭词法分析器、语法分析器中间代码执行器
</p>
<p>
8、调用每个扩展的post-RSHUTDOWN函数。只是基本每个扩展的post_deactivate_func函数指针都是NULL
</p>
<p>
9、关闭SAPI，通过sapi_deactivate销毁SG（sapi_headers）、SG（request_info）等的内容。
</p>
<p>
10、关闭流的包装器、关闭流的的过滤器
</p>
<p>
11、关闭内存管理
</p>
<p>
12、重新设置最大执行时间
</p>
<p>
结束
</p>
<p>
最终到了要首尾的地方了
</p>
<p>
flush
</p>
<p>
sapi_flush将最后的内容刷新出去。其调用的是sapi_module.flush，在CLI模式下等价于flush函数。
</p>
</p>
</p>
<li>关闭Zend引擎</li>
<p>
zend_shutdown将关闭Zend引擎
</p>
<p>
此时对应图中的流程，我们应该是执行每个模块的关闭模块操作，在这里只有一个zend_hash_graceful_reverse_destroy函数将module_registry销毁了。当然，它最终也是调用了关闭模块的方法的，其根源在于在初始化module_registry时就设置了这个hash表析构时调用ZEND_MODULE_DTOR宏。而ZEND_MODULE_DTOR宏对应的是module_destructor函数。在此函数中会调用模块的module_shutdown_func方法，即PHP_RSHUTDOWN_FUNCTION宏产生的那个函数。
</p>
<p>
在关闭所有的模块后，PHP继续销毁全局函数表，销毁全局类表，销售全局变量表等。通过zend_shutdown_extensions遍历zend_extensions所有元素，调用每个扩展的shutdown函数。
</p>
</ul>
</p>
#####多进程SAPI生命周期
***
<p>
	通常PHP是编译为apache的一个模块来处理PHP请求。Apache一般会采用多进程模式，Apache启动后会fork出多个子进程，每个进程的内存空间独立，每个子进程都会经过开始和结束环节，不过每个进程的开始阶段只在进程fork出来以后进行，在整个进程的生命周期内可能会处理多个请求。只有在Apache关闭或者进程被结束之后才会进行关闭阶段，在这两个阶段之间会随着每个请求重复请求开始-请求关闭的环节。如图2.2所示：
</p>
<div class="book-img" style="text-align: center;">
<img src="/img/02-01-02-multiprocess-life-cycle.png" alt="图2.1 单进程SAPI生命周期">
<div class="book-img-desc">图2.2 多进程SAPI生命周期</div>
</div>
#####多线程的SAPI生命周期
***
<p>
多线程模式和多进程中的某个进程类似，不同的是在整个进程的生命周期内会并行的重复着请求开始-请求关闭的环节
</p>
<div class="book-img" style="text-align: center;">
<img src="/img/02-01-013-multithreaded-lift-cycle.png" alt="图2.3 多线程SAPI生命周期">
<div class="book-img-desc">图2.3 多线程SAPI生命周期</div>
</div>
#####Zend引擎
***
<p>
Zend引擎是PHP实现的核心，提供了语言实现上的基本设施。例如：PHP的语法实现，脚本的编译运行环境，扩展机制以及内存管理等，当然这里的PHP指得是官方的PHP实现（除了官方的实现，目前比较知名的有facebook的hiphop实现，不过到目前为止，PHP还没有一个标准的语言规范），而PHP则提供了请求处理和其他Web服务器的接口（SAPI）。
</p>
<p>
目前PHP的实现和Zend引擎之间的关系非常紧密，甚至有些过于机密了，例如很多PHP扩展都是使用的ZendAPI，而Zend正是PHP语言本身的实现，PHP只是使用Zend这个内核来构建PHP语言的，而PHP扩展大都使用ZendAPI，这就导致PHP的很多扩展和Zend引擎耦合在一起了。
</p>
####第一节生命周期和Zend引擎
<p>
在各个服务器抽象层之间蹲守着相同的约定，这里我们称之为SAPI接口。每个SAPI实现都是一个_sapi_module_struct结构体变量。（SAPI接口）。在PHP的源码中，当需要调用服务器相关信息时，全部通过SAPI接口中对应方法调用实现，而这对应的方法在各个服务器抽象层实现时都会有各自的实现。
</p>
<p>
如图2.4所示，为SAPI的简单示意图。
</p>
<div class="book-img" style="text-align: center;">
<img src="/img/02-02-01-sapi.png" alt="图2.4 SAPI的简单示意图">
<div class="book-img-desc">图2.4 SAPI的简单示意图</div>
</div>
<p>以cgi模式和apache2服务器为例，它们的启动放如下：</p>
	cgi_sapi_module.startup(&cgi_sapi_module)	// cgi模式 cgi/cgi_main.c文件
	apache2_sapi_module.startup(&apache2_sapi_module);
	// apache2服务器 apache2handler/sapi_apache2.c文件
<p>
这里的cgi_sapi_module是sapi_module_struct结构体的静态变量。它的startup方法指向php_cgi_startup函数指针。在这个结构体中除了startup函数指针，还有许多其它方法或字段。其部分分定义如下：
</p>
	struct _sapi_module_struct {
		char *name;			// 名字（标识用）	
		char *pretty_name	//	更好理解的名字
		
		int (*startup)(struct _sapi_module_struct *sapi_module);		// 启动函数
		inr (*shutdown)(struct _sapi_module_struct *sapi_module);		// 关闭方法
		
		int (*activate)(TSRMLS_D);		// 激活
		int (*deactivate)(TSRMLS_D);	// 停用

		int(*ub_write)(const char *str, unsigned int str_length TSRML_DC);		// 不缓存的写操作（unbuffered write）
		void(*flush)(void *server_context);		// flush
		struct stat *(*get_stat)(TSRMLS_D);		// get uid
		char *(*getenv)(char *name, size_t name_len TSRMLS_DC);		//	getenv

		void (*sapi_error)(int type, const char *error_msg, …);		/* error handler */

		int (*header_handler)(sapi_header_struct *sapi_header, sapi_header_op_enum op,
        sapi_headers_struct *sapi_headers TSRMLS_DC);   /* header handler */
 
     	/* send headers handler */
    	int (*send_headers)(sapi_headers_struct *sapi_headers TSRMLS_DC);
 
    	void (*send_header)(sapi_header_struct *sapi_header,
            void *server_context TSRMLS_DC);   /* send header handler */
 
    	int (*read_post)(char *buffer, uint count_bytes TSRMLS_DC); /* read POST data */
    	char *(*read_cookies)(TSRMLS_D);    /* read Cookies */
 
    	/* register server variables */
    	void (*register_server_variables)(zval *track_vars_array TSRMLS_DC);

		void (*log_message)(char *message);	/*Log message*/
		time_t (*get_request_time)(TSRMLS_D);	/*Request Time*/
		void (*terminate_process)(TSRMLS_D);	/*Child Terminate*/
	
		char *php_ini_path_override;		//覆盖的ini路径
		…
		…
	}
<p>
其中一些函数指针的说明如下：
</p>
<ul>
<li>
startup当SAPI初始化时，首先会调用该函数。如果服务器处理多个请求时，该函数只会调用一次。比如Apache的SAPI，它是以mod_php5的Apache模块的形式加载到Apache中的，在这个SAPI中，startup函数只在父进程中创建一次，在其fork的子进程中不会调用。
</li>
<li>
activate此函数会在每个请求开始是调用，它会再次初始化请求前的数据结构
</li>
<li>
deactivate此函数会在每个请求结束时调用，它用来确保所有的数据，以及释放在activate中初始化的数据结构。
</li>
<li>
shutdown关闭函数，它用来释放所有的SAPI的数据结构、内存等
</li>
<li>
ub_write不缓存的写操作(unbffered write)，它是用来将PHP的数据输出给客户端，如在CLI模式下，其最终是调用fwrite实现向标准输出输出内容；在Apache模块中，它最终是调用Apache提供的方法rwrite
</li>
<li>
flush刷新输出，在CLI模式下通过使用C语言的函数库fflush实现，在php_mode5模式下，使用Apache的提供的函数函数rflush实现。
</li>
<li>
read_cookie在SAPI激活时，程序会调用此函数，并且将此函数获取的值赋值给SG(request_info).cookie_data。在CLI模式下，此函数会返回NULL
</li>
<li>
read_post此函数和read_cookie一样也是在SAPI激活时调用，它与请求的方法相关，当请求的方法是POST时，程序会操作$_POST、$_HTTP_RAW_POST_DATA等变量。
</li>
<li>
send_header发送头部信息，此方法一般的SAPI都会定制，其所不同的是，有些的会调服务器自带的(如Apache)。
</li>
</ul>
<p>
以上的这些结构在各服务器的接口实现中都有定义。如Apache2的定义：
</p>
	static sapi_module_struct apache2_sapi_module = {
		"apache2handler",
		"Apache 2.0 Handler",

		php_apache2_startup,			/* startup */
		php_module_shutdown_wrapper,	/* shutdown */
		…
	}
<p>
在PHP的源码中实现了很多，比如IIS的实现以及一些非主流的Web服务器实现，其文件结构如图2.5所示：
</p>
<div class="book-img" style="text-align: center;">
<img src="/img/02-02-02-file-structure.png" alt="图2.5 SAPI文件结构图">
<div class="book-img-desc">图2.5 SAPI文件结构图</div>
</div>
<p style="background-color: #aaa;">
目前PHP内置的很多SAPI实现都已不再维护或者变的有些非主流了，PHP社区目前正在考虑将一些SAPI移出代码库。 社区对很多功能的考虑是除非真的非常必要，或者某些功能已近非常通用了，否则就在PECL库中， 例如非常流行的APC缓存扩展将进入核心代码库中。
</p>
<p>
整个SAPI类似于一个面向对象中的模板方法模式的应用。 SAPI.c和SAPI.h文件所包含的一些函数就是模板方法模式中的抽象模板， 各个服务器对于sapi_module的定义及相关实现则是一个个具体的模板。
</p>
<p>
这样的结构在PHP的源码中有多处使用， 比如在PHP扩展开发中，每个扩展都需要定义一个zend_module_entry结构体。 这个结构体的作用与sapi_module_struct结构体类似，都是一个类似模板方法模式的应用。 在PHP的生命周期中如果需要调用某个扩展，其调用的方法都是zend_module_entry结构体中指定的方法， 如在上一小节中提到的在执行各个扩展的请求初始化时，都是统一调用request_startup_func方法， 而在每个扩展的定义时，都通过宏PHP_RINIT指定request_startup_func对应的函数。 以VLD扩展为例：其请求初始化为PHP_RINIT(vld),与之对应在扩展中需要有这个函数的实现：
</p>
	PHP_RINIT_FUNCTION(vld){}
####Apache模块
<p>
当PHP需要在Apache服务器下运行时，一般来说，它可以mod_php5模块的形式集成，此时mod_php5模块的作用是接收Apache传递过来的PHP文件请求，并处理这些请求，然后将处理后的结果返回给Apache。如果我们在Apache启动前在配置文件中配置好了PHP模块(mod_php5)，PHP模块通过注册apache2的ap_hook_post_config挂钩，在Apache启动的时候启动此模块以接受PHP文件的请求。
</p>
<p>
除了这种启动的加载方式，Apache的模块可以在运行的时候动态装载，这意味着对服务器可以进行功能扩展而不需要重新对源码进行编译，甚至根本不需要停止服务器。我们所需要做的仅仅是给服务器发送信号HUP或者AP_SIG_GRACEFUL通知服务器重新载入模块。但是在动态加载之前，我们需要将模块编译成为动态链接库。此时的动态加载就是加载动态链接库。Apache中对动态链接库的处理是通过模块mod_so来完成的，因此mod_so模块不能被动态加载，它只能被静态编译进Apache的核心。这意味着它是随着Apache一起启动的。
</p>
<p>
我们需要在Apache的配置文件http.conf中添加一行：
</p>
	LoadModule php5_module module/mod_php5.so
<p>
使用loadModule命令，该命令的第一个参数是模块的名称，名称可以在模块实现的源码中找到。第二个选项是该模块所处的路径。如果需要在服务器运行时加载模块，可以通过发送信号HUP或者AP_SIG_GRACEFUL给服务，一旦接受到该信号，Apache将重新装载模块，而不需要重新启动服务器。
</p>
<p>
在配置文件中添加了所有所示的指令后，Apache在加载模块时会根据模块名查找模块并加载，对于每一个模块，Apache必须保证其文件名以"mod_"开始的，如PHP的mod_php5.c如果命名格式不对，Apache将认为此模块不合法。Apache的每个模块都是以module结构体的形式存在，module结构的name属性在最后是通过宏STANDARD20_MODULE_STUFF以__FILE__体现。关于这点可以在后面介绍mod_php5模块时有看到。这也就决定了我们的文件名和模块名是相同的。通过之前指令中指定的路径找到相关的动态链接库文件后，Apache通过内部的函数获取动态链接库中的内容，并将模块的内容加载到内存中的指定变量中。
</p>
<p>
在真正激活模块之前，Apache会检查所加载的模块是否为真正的Apache模块，这个检测是通过检测module结构中的magic字段实现的。而magic字段是通过宏STANDARD20_MODULE_STUFF体现，在这个宏中magic的值为MODULE_MAGIC_COOKIE,MODULE_MAGIC_COOKIE定义如下：
</p>
	#define MODULE_MAGIC_COOKIE 0x41503232UL /* "AP22" */
<p>
最后Apache会调用相关函数(ap_add_loaded_module)将模块激活，此处的激活就是将模块放入相应的链表中（ap_top_module链表：ap_top_module链表用来保存Apache中所有的被激活的模块，包括默认的激活模块和激活的第三方模块。）
</p>
####Apache的mod_php5模块说明
***
<p>
Apache2的mod_php5模块包括sapi/apache2handler和sapi/apache2filter两个目录在apache2_handle/mod_php5.c文件中，模块定义的相关代码如下：
</p>
	AP_MODULE_DECLARE_DATA module php5_module = {
		STANDARD20_MODULE_STUFF,
		 /* 宏，包括版本，小版本，模块索引，模块名，下一个模块指针等信息，其中模块名以__FILE__体现 */
		create_php_config,			 /* create per-directory config structure */
		merge_php_config,			/* merge per-directory config structures */
		NUll,						/* create per-server config structure */
		NULL,						/* merge per-server config structures */
		php_dir_cmds,				/* 模块定义的所有的指令 */
		php_ap2_register_hook
		 /* 注册钩子，此函数通过ap_hoo_开头的函数在一次请求处理过程中对于指定的步骤注册钩子 */
	}
<p>
它所对应的是Apache的module结构，module的结构定义如下：
</p>
	typedef struct module_strcut module;
	strcut module_strcut {
		int version;
		int minor_version;
		int module_index;
		const char *name;
		void *dynamic_load_handle;
		struct module_struct *next;
		unsigned long magic;
		void (*rewrite_args) (process_rec *process);
    	void *(*create_dir_config) (apr_pool_t *p, char *dir);
    	void *(*merge_dir_config) (apr_pool_t *p, void *base_conf, void *new_conf);
    	void *(*create_server_config) (apr_pool_t *p, server_rec *s);
    	void *(*merge_server_config) (apr_pool_t *p, void *base_conf, void *new_conf);
    	const command_rec *cmds;
    	void (*register_hooks) (apr_pool_t *p);
	}
<p>
上面的模块结构与我们在mod_php5.c中所看到的结构有一点不同，这是由于STANDARD20_MODULE_STUFF的原因，这个宏它包含了前面8个字段的定义。STANDARD20_MODULE_STUFF宏的定义如下：
</p>
	/** Use this in all standard modules */
	#define STANDARD20_MODULE_STUFF MODULE_MAGIC_NUMBER_MAJOR, \
			MODULE_MAGIC_NUMBER_MINOR,	\
			-1,	\
			__FILE__, \
			NULL,	\
			NULL,	\
			MODULE_MAGIC_COOKIE,	\
						NULL 	 /* rewrite args spot */
<p>
在php5_module定义的结构中，php_dir_cmds是模块定义的所有的指令集合，其定义的内容如下：
</p>
	const command_rec php_dir_cmds[] = 
	{
		AP_INIT_TAKE2("php_value", php_apache_value_handler, NULL,
        	OR_OPTIONS, "PHP Value Modifier"),
    	AP_INIT_TAKE2("php_flag", php_apache_flag_handler, NULL,
        	OR_OPTIONS, "PHP Flag Modifier"),
    	AP_INIT_TAKE2("php_admin_value", php_apache_admin_value_handler,
        	NULL, ACCESS_CONF|RSRC_CONF, "PHP Value Modifier (Admin)"),
    	AP_INIT_TAKE2("php_admin_flag", php_apache_admin_flag_handler,
        	NULL, ACCESS_CONF|RSRC_CONF, "PHP Flag Modifier (Admin)"),
    	AP_INIT_TAKE1("PHPINIDir", php_apache_phpini_set, NULL,
        	RSRC_CONF, "Directory containing the php.ini file"),
    	{NULL}
	}
<p>
以上代码声明了pre_config,post_config,handler和child_init 4个挂钩以及对应的处理函数。其中pre_config,post_config,child_init是启动挂钩，它们在服务器启动时调用。handler挂钩是请求挂钩，它在服务器处理请求时调用。其中在post_config挂钩中启动php。它通过php_apache_server_startup函数实现。php_apache_server_startup函数通过调用sapi_startup启动sapi，并通过调用php_apache2_startup来注册sapi module struct ,最后调用php_modlue_startup来初始化PHP，其中有会初始化Zend引擎，以及填充zend_module_struct中的treat_data成员(通过php_startup_sapi_content_types)等
</p>
<p>
到这来，我们知道Apache加载mod_php5模块的整个过程，可是这个过程与我们的sapi有什么关系呢？mod_php5也定义了属于Apache的sapi_module_struct结构：
</p>
	static sapi_module_struct apache2_sapi_module = {
	"apache2handler",
	"Apache 2.0 Handler",
 
	php_apache2_startup,                /* startup */
	php_module_shutdown_wrapper,            /* shutdown */
 
	NULL,                       /* activate */
	NULL,                       /* deactivate */
 
	php_apache_sapi_ub_write,           /* unbuffered write */
	php_apache_sapi_flush,              /* flush */
	php_apache_sapi_get_stat,           /* get uid */
	php_apache_sapi_getenv,             /* getenv */
 
	php_error,                  /* error handler */
 
	php_apache_sapi_header_handler,         /* header handler */
	php_apache_sapi_send_headers,           /* send headers handler */
	NULL,                       /* send header handler */
 
	php_apache_sapi_read_post,          /* read POST data */
	php_apache_sapi_read_cookies,           /* read Cookies */
 
	php_apache_sapi_register_variables,
	php_apache_sapi_log_message,            /* Log message */
	php_apache_sapi_get_request_time,       /* Request Time */
	NULL,                       /* Child Terminate */
 
	STANDARD_SAPI_MODULE_PROPERTIES
	};
<p>
这些地方都是专属于Apache服务器。以读取cookie为例，当我们在Apache服务器环境下，在PHP中调用读取Cookie时，最终获取的数据的位置是在激活SAPI时。它所调用的方法是read_cookie。
</p>
	SG(request_info).cookie_data = sapi_module.read_cookies(TSRMLS_C);
<p>
对于每一个服务器在加载时，我们都指定了sapi_module，而Apache的spai_module是apache2_sapi_module。其中对应read_cookie方法的是php_apache_sapi_read_cookies函数
</p>
<p>
又如flush函数，在ext/standard/basic_functions.c文件中，其实现在为sapi_flush:
</p>
	SAPI_API int sapi_flush(TSRMLS_D)
	{
		if (spai_module.flush) {
			sapi_module.flush(SG(server_context));
			return SUCCESS;
		}else {
			return FAILURE;
		}
	}
<p>
如果我们定义了此前服务器接口的flush函数，则直接调用flush对应的函数，返回成功，否则返回失败。对于我们当前的Apache模块，其实现为php_apache_sapi_flush函数，最终会调用Apache的ap_rflush，刷新Apache的输出缓冲区。当然，flush的操作有时也不会生效，因为当PHP执行flush函数时，其所有的行为完全依赖于Apache的行为，而自身却做不了什么，比如启用了Apache的压缩功能，当没有达到预定的输出大小时，即使使用了flush函数，Apache也不会向客户端输出对应的内容。
</p>
#####Apache的运行过程
***
<p>
Apache的运行分为启动阶段和运行阶段。在启动阶段，Apache为了获得系统资源最大的使用权限，将以特权用户root（lunix系统）或者超级管理员Adminstrator(Windows系统)完成启动，并且整个过程处于一个单进程单线程的环境中。这个阶段包括配置文件解析（如http.conf文件）、模块加载（如mod_php, mod_perl）和系统资源初始化（例如日志文件、共享内存段、数据库连接等）等工作。
</p>
<p>
Apache的启动阶段执行了大量的初始化操作，并且将许多比较慢或者花费比较高的操作都集中在这个阶段完成，以减少了后面处理请求服务的压力。
</p>
<p>
在运行阶段，Apache主要工作是处理用户的服务请求。在这个阶段，Apache放弃特权用户级别，使用普通权限，这主要是基于安全性的考虑，防止由于代码的缺陷引起的安全漏洞。Apache对HTT的请求可以分为连接、处理和断开连接三个大的阶段。同时也可以分为11个小阶段，依次为：Post-Read-Request，URI Translation，Header Parsing，Access Control，Authentication，Authorization， MIME Type Checking，FixUp，Response，Logging，CleanUp
</p>
#####Apache Hook机制
***
<p>
Apache的Hook机制是指：Apache允许模块(包括内部模块和外部模块，例如mod_php5.so,mod_perl.so等)将自定义的函数注入到请求处理循环中。模块可以在Apache的任何一个处理阶段中挂接(Hook)上自己的处理函数，从而参与Apache的请求处理过程。mod_php5.so/php5apache2.dll就是将所包含的自定义函数，通过Hook机制注入到Apache中，在Apache处理流程的各个阶段负责处理php请求。关于Hook机制在Windows系统开发也经常遇到，在Windows开发既有系统级的钩子，又有应用级的钩子。
</p>
#####Apache常用对象
***
<p>
<b>request_rec对象</b>当一个客户端请求到达Apache时，就会创建一个request_rec对象，当Apache处理完一个请求后，与这个请求对应的request_rec对象也会随之被释放。request_rec对象包括与一个HHTP请求相关的所有数据，并且还包含一些Apache自己要用到的状态和客户端的内部字段。
</p>
<p>
<b>server_rec对象</b>server_rec定义了一个逻辑上的WEB服务器。如果有定义虚拟主机，每一个虚拟主机拥有自己的server_rec对象。server_rec对象在Apache启动时创建，当整个httpd关闭时才会被释放。它包括服务器名称，连接信息，日志信息，针对服务器的配置，事务处理相关信息等server_rec对象是继request_rec对象之后第二重要的对象
</p>
<p>
<b>conn_rec对象</b>coon_rec对象是TCP连接在Apache的内部实现。它在客户端连接到服务器创建，在连接断开时释放。
</p>


