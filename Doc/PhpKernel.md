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
####嵌入式
	#include <sapi/embed/php_embed.h>
<p>
	在sapi目录下的embed目录是PHP对于嵌入式的抽象层所在。在这里有我们所要用到的函数或宏定义。
</p>
	#ifdef ZTS
		void ***tsrm_ls;
	#endif
<p>
	ZTS是Zend Thread Safety的简写，与这个相关的有一个TSRM(线程安全资源管理)。
</P>
	zend_module_entry php_mymod_module_entry = {
		STANDARD_MODULE_HEADER,
		"mymod", 		/* extension name */
		NULL,			/* function entries */
		NULL,			/* MINIT */
		NULL,			/* MSHUTDOWN */
		NULL,			/* RINIT */
		NULL,			/* RSHUTDOWN */
		NULL,			/* MINFO */
		"1.0",			/* version */
		STANDARD_MODULE_PROPERTIES
	};
<p>
	以上PHP内部的模块结构声明，此处对于模块初始化，请求初始化等函数指针均为NULL，也就是模块在初始化及请求开始结束等事件发生的时候不执行任何操作，不过这些操作在sapi/embed/php_embed.c文件中的php_embed_shutdown等函数中有体现。关于模块结构的定义在zend/zend_modules.h中。
</P>
<p>
	startup_php函数：
</P>
	static void startup_php(void)
	{
		int argc = 1;
		char *argv[2] = {"embed5", NULL};
		php_embed_init(argc, argv PTSRMLS_CC);
		zend_startup_module(&php_mymod_module_entry);
	}
<p>
	这个函数调用了两个函数php_embed_init和zend_startup_module完成初始化工作。php_embed_init函数定义在sapi/embed.php_embed.c文件中。它完成了PHP对于嵌入式的初始化支持。zend_startup_module函数是PHP的内部API函数，它的作用是注册定义的模块，这里是注册mymod模块。这个注册过程仅仅是将所定义的zend_module_entry结构添加到注册模块列表中。
</P>
<p>
	execute_php函数：
</P>
	static void execute_php(char *filename)
	{
		zend_first_try{
			char *includ_script;
			spprintf(&include_script, 0, "include '%s'", filename);
			zend_eval_string(include_script, NULL, filename TSRMLS_CC);
			efree(include_script);	
		} zend_end_try();
	}
<p>
	从函数的名称来看，这个函数的功能是执行PHP代码的。它通过调用spprintf函数构造一个include语句，然后再调用zend_eval_string函数执行这个include语句。zend_eval_string最终是调用zend_eval_string函数，这个函数是流程是一个编译PHP代码，生成zend_op_array类型数据，并执行opcode的过程。这段程序相当于下面的这段php程序，这段程序可以用php命令来执行，虽然下面这段程序没有实际意义，而通过嵌入式PHP在，你可以在一个用C实现的系统中嵌入PHP，然后用PHP来实现功能。
</P>
	<?php
	if($argc < 2) die("Usage: embed4 scriptfile");
	include $argv[1];
<p>
	main函数：
</p>
	int main(int argc, char *argv[])
	{
		if(argc <= 1){
			printf("Usage: embed4 scriptfile");
			return -1;
		}
		startup_php();
		executr_php(argv[1]);
		php_embed_shutdown(TSRMLS_CC);
		return 0;
	}
<p>
	这个函数是主函数，执行初始化操作，根据输入的参数执行PHP的include语句，最后执行关闭操作，返回。其中php_embed_shutdown函数定义在sapi/embed/php_embed.c文件中。它完成了PHP对于嵌入式的关闭操作支持。包括请求关闭操作，模块关闭操作等。
</p>
<p>
	以上是使用PHP的嵌入式方式开发的一个简单的PHP代码运行器，它的这些调用的方式都基于PHP本身的一些实现，而针对嵌入式的SAPI定义是非常简单的，没有Apache和CGI模式的复杂，或者说是相当简陋，这也是由其所在环境决定。在嵌入式的环境下，很多的网络协议所需要的方法都不再需要。如下所示，为嵌入式的模块定义。
</p>
	sapi_module_struct php_embed_modeule = {
		"embed",                       /* name */
    	"PHP Embedded Library",        /* pretty name */
 
    	php_embed_startup,              /* startup */
    	php_module_shutdown_wrapper,   /* shutdown */
 
    	NULL,                          /* activate */
    	php_embed_deactivate,           /* deactivate */
 
    	php_embed_ub_write,             /* unbuffered write */
    	php_embed_flush,                /* flush */
    	NULL,                          /* get uid */
    	NULL,                          /* getenv */
 
    	php_error,                     /* error handler */
 
    	NULL,                          /* header handler */
    	NULL,                          /* send headers handler */
    	php_embed_send_header,          /* send header handler */
 	
    	NULL,                          /* read POST data */
    	php_embed_read_cookies,         /* read Cookies */
 
    	php_embed_register_variables,   /* register server variables */
    	php_embed_log_message,          /* Log message */
    	NULL,                           /* Get request time */
    	NULL,                           /* Child terminate */

		STANDARD_SAPI_MODULE_PROPERTIES
	};
<p>
	在这个定义中我们看到了若干的NULL定义，在前面一个小节中说到SAPI时，我们是以cookie的读取为例，在这里也有读取cookie的实现——php_embed_read_cookies函数，但是这个函数的实现是一个空指针NULL.
</p>
<p>
	而这里的flush实现与Apache的不同
</p>
	static void php_embed_flush(void *server_context)
	{
		if (fflush(stdout==EOF)){
			php_handle_aborted_connection();
		}
	}
<p>
	flush是直接调用fflush(stdout),以达到清空stdout的缓存的目的。如果输出失败(fflush成功返回0， 失败返回EOF)，则调用php_handle_aborted_connection，进入中断处理程序。
</p>
####FastCGI
#####CGI简介
***
<p>
	CGI全称“通用网关接口”，它可以让一个客户端，从网页浏览器向执行在web服务器上的程序请求数据。CGI描述了客户端和这个程序之间传输数据的一种标准。CGI的一个目的是要独立于任何语言的，所以CGI可以用任何一种语言编写，只要这种语言具有标准输入、输出和环境变量。如：php,perl,tcl等。
</p>
#####CGI的运行原理
<p>
	<p>
	1.客户端访问某个URL地址之后，通过GET/POST/PUT等方式提交数据，并通过HTTP协议向Web服务器发出请求。
	</p>
	<p>
	2.服务器的HTTP Daemon(守护进程)启动一个子进程。然后在子进程中，将HTTP请求里描述的信息通过标准输入stdin和环境变量传递给URL指定的CGI程序，并启动此应用程序进行处理，处理结果通过标准输出stdout返回给HTTP Daemon子进程。
	</p>
	<p>
	3.再由HTTP Daemon 子进程通过HTTP协议返回给客户端。
	</p>
</p>
<p>
Web服务器程序：
</p>
	#include <stdio.h>
	#include <stdlib.h>
	#include <unistd.h>
	#include <sys/types.h>
	#include <sys/socket.h>
	#include <arpa/inet.h>
	#include <netinet/in.h>
	#include <string.h>

	#include SERV_PORT	9003
	
	char *str_join(char *str1, char *str2);
	
	char *html_response(char *res, char *buf);
	
	int main(void){
		int lfd, cfd;
		struct socket_in serv_addr, clin_addr;
		socklen_t clin_len;
		char buf(1024), web_result[1024];
		int len;
		FILE *cin;

		if ((lfd = socket(AF_INET, SOCK_STREAM, 0)) == -1){
			perror("create socket failed");
			exit(1);
		}

		memset(&serv_addr, 0, sizeof(serv_addr));
		serv_addr.sin_family = AF_INET;
		serv_addr.sin_addr.s_addr = htonl(INADDR_ANY);
		serv_addr.sin_port = htons(SERV_PORT);

		if (bind(lfd, (struct sockaddr *) &serv_addr, sizeof(serv_addr)) == -1) {
        	perror("bind error");
        	exit(1);
    	}
 
    	if (listen(lfd, 128) == -1) {
        	perror("listen error");
        	exit(1);
    	}

		signal(SIGCLD, SIG_IGN);

		while (1) {
			clin_len = sizeof(clin_addr);
			if ((cfd = accept(lfd, (struct sockaddr *) &clin_addr, &clin_len)) == -1) {
            	perror("接收错误\n");
            	continue;
        	}
			
			cin = fdopen(cfd, "r");
			setbuf(cin, (char *) 0);
			fgets(buf, 1024, cin)	//读取第一行
			printf("\n%s", buf);

			//======================== cgi 环境变量设置演示 ====================
			// 例如 "GET /cgi-bin/user?id=1 HTTP/1.1";

			char *delim = " ";
			char *p;
			char *method, *filename, *query_string;
			char *query_string_pre = "QUERY_STRING=*";

			method = strtok(buf, delim);         // GET
        	p = strtok(NULL, delim);             // /cgi-bin/user?id=1 
        	filename = strtok(p, "?");           // /cgi-bin/user

			if (strcmp(filename, "/favicon.ico") == 0) {
            	continue;
        	}
 
        	query_string = strtok(NULL, "?");    // id=1
        	putenv(str_join(query_string_pre, query_string));

			//============================ cgi 环境变量设置演示 ============================
 
        	int pid = fork();
 
        	if (pid > 0) {
            	close(cfd);
        	}
        	else if (pid == 0) {
            	close(lfd);
            	FILE *stream = popen(str_join(".", filename), "r");
            	fread(buf, sizeof(char), sizeof(buf), stream);
            	html_response(web_result, buf);
            	write(cfd, web_result, sizeof(web_result));
            	pclose(stream);
            	close(cfd);
            	exit(0);
        	}
        	else {
            	perror("fork error");
            	exit(1);
        	}
				
		}	
		
		close(lfd);
 
    	return 0;	
	}

	char *str_join(char *str1, char *str2) {
		char *result = malloc(strlen(str1) + strlen(str2) + 1);
		if (result == NULL) exit(1);
		strcpy(result, str1);
		strcat(result, str2);

		return result;
	}

	char *html_response(char *res, char *buf) {
		char *html_response_template = "HTTP/1.1 200 OK\r\nContent-Type:text/html\r\nContent-Length: %d\r\nServer: mengkang\r\n\r\n%s";
		
		sprintf(res, html_response_template, strlen(buf), buf);

		return res;
	}
#####FastCGI简介
***
<p>
	FastCGI是Web服务器和处理程序之间通信的一种协议，是CGI的一种改进方案，FastCGI像是一个常驻型的CGI，它可以一直执行，在请求到达时不会花费时间去fork一个进程来处理(这是CGI最为人诟病的fork-and-execute模式)。正是因为他只是一个通信协议，它还支持分布式的运算，所以FastCGI程序可以在网站服务器以外的主机上执行，并且可以接受来自其它网站服务器的请求。
</p>
<p>
	FastCGI是与语言无关的、可伸缩架构的CGI开放扩展，将CGI解释器进程保存在内存中，以此获得较高的性能、CGI程序反复加载是CGI性能低下的主要原因，如果CGI程序保持在内存中并接受FastCGI进程管理器调度，则可以提供良好的性能、伸缩性、Fail-Over特性等。
</p>
#####FastCGI工作流程如下：
***
<p>
	<p>
	1、FastCGI进程管理器自身初始化，启动多个CGI解释器进程，并等待来自Web Server的连接。
	</p>
	<p>
	2、Web服务器与FastCGI进程管理器进行Socket通信，通过FastCGI协议发送CGI环境变量和标准输入数据给CGI解释器进程。
	</p>
	<p>
	3、CGI解释器进程完成处理后将标准输出和错误信息从同一连接返回Web Server.
	</p>
	<p>
	4、CGI解释器进程接着等待并处理来自Web Server的下一个连接。
	</p>
</p>
<p>
	FastCGI与传统CGI模式的区别之一则是Web服务器不是直接执行CGI程序了，而是通过Socket与FastCGI响应器（FastCGI进程管理器）进行交换，也正是由于FastCGI进程管理器是基于Socket通信的，所以也是分布式的，Web服务器可以和CGI响应服务器分开部署。Web服务器需要将数据CGI/1.1的规范封装在遵循FastCGI协议包中发送给FastCGI响应器程序。
</p>
#####FastCGI消息类型
***
	typedef enum _fcgi_request_type {
		FCGI_BEGIN_REQUEST      =  1, /* [in]                              */
    	FCGI_ABORT_REQUEST      =  2, /* [in]  (not supported)             */
    	FCGI_END_REQUEST        =  3, /* [out]                             */
    	FCGI_PARAMS             =  4, /* [in]  environment variables       */
    	FCGI_STDIN              =  5, /* [in]  post data                   */
    	FCGI_STDOUT             =  6, /* [out] response                    */
    	FCGI_STDERR             =  7, /* [out] errors                      */
    	FCGI_DATA               =  8, /* [in]  filter data (not supported) */
    	FCGI_GET_VALUES         =  9, /* [in]                              */
    	FCGI_GET_VALUES_RESULT  = 10  /* [out]                             */
	} fcgi_request_type;
#####消息的发送顺序
***
<p>
下图是一个比较常见消息传递流程
</p>
<div class="book-img" style="text-align: center;">
<img src="/img/02-02-03-fastcgi-data.png" alt="图2.9 FastCGI 消息传递流程示意图">
<div class="book-img-desc">图2.9 FastCGI 消息传递流程示意图</div>
</div>
<p>
	最先发送的是FCGI_BEGIN_REQUEST,然后是FCGI_PARAMS和FCGI_STDIN,由于每个消息头里面能够承载的最大长度时65535，所以这两种类型的消息不一定只发送一次，有可能连续发送多次。
</p>
<p>
	FastCGI响应体处理完毕之后，将发送FCGI_STDOUT、FCGI_STDERR，同理也可能多次连续发送。最后以FCGI_END_REQUEST表示请求的结束。需要注意的一点，FCGI_BEGIN_REQUEST和FCGI_END_REQUEST分别标识着请求的开始和结束，与整个协议息息相关，所以他们的消息的内容也是协议的一部分，因此也会有相应的结构体与之对应。而环境变量、标准输入、标准输出、错误输出，这些都是业务相关，与协议无关，所以他们的消息体的内容则无结构体对应。
</p>
<p>
	由于整个消息是二进制连续传递的，所以必须定义一个统一的结构的消息头，这样以便读取每个消息的消息体，方便消息的切割。这在网络通讯中是非常常见的一种手段。
</p>
#####FastCGI消息头
***
<p>
	FastCGI消息分10种消息类型，有的是输入有的是输出。而所有的消息都以一个消息开头，其结构体定义如下：
</p>
	typedef struct _fcgi_header {
		unsigned char varsion;
		unsigned char type;
		unsigned char requestIdB1;
		unsigend char requestIdB0;
		unsigend char contentLengthB1;
		unsigend char contentLengthB0;
		unsigend char paddingLength;
		unsigend char reserved;
	} fcgi_header;
<p>
	字段解释下：
	<p>
		version标识FastCGI协议版本。type标识FastCGI记录类型，也就是记录执行的一般职能。requestId标识记录所属的FastCGI请求。contentLength记录的contentData组件的字节数。
	</p>
	<p>
		关于上面的xxB1和xxB0的协议说明：当两个相邻的结构组件除了后缀"B1"和"B0"之外命名相同时，它表示这两个组件可视为估值为B1<<8 + B0的单个数字。该单个数字的名字是这些组件减去后缀的名字。这个约定归纳了一个由超过两个字节表示的数字的处理方式。
	</p>
	<p>
		比如协议头中requestId和contentLength表示的最大值就是65535。
	</p>	
</p>
	#include <stdio.h>
	#include <stdlib.h>
	#include <limits.h>

	int main()
	{
		unsigned char requestIdB1 = UCHAR_MAX;
		unsigned char requestIdB0 = UCHAR_MAX;
		printf("%d\n", (requestIdB1 << 8) + requestIdB0);		//65535
	}
#####FCGI_BEGIN_REQUEST的定义
***
	typedef struct _fcgi_begin_request {
		unsigned char roleB1;
		unsigend char roleB0;
		unsigend char flags;
		unsigend char reserved[5];
	} fcgi_begin_request;
<p>
	<b>字段解释：</b>role表示Web服务器期望应用扮演的角色。分为三个角色
</p>
	typedef enum _fcgi_role {
		FCGI_RESPONDER = 1;
		FCGI_AUTHORIZER = 2;
		FCGI_FILTER = 3;
	} fcgi_fole;
<p>
	而FCGI_BEGIN_REQUEST中的flags组件包含一个控制线路关闭的位：flags & FCGI_KEEP_CONN :结果为0，则应用在对本次请求响应后关闭线路。如果非0，应用在对本次请求响应后不会关闭线路；Web服务器为线路保持响应性。
</p>
#####FCGI_END_REQUEST的定义
***
	typedef struct _fcgi_end_request {
		unsigend char appStatusB3;
		unsigend char appStatusB2;
		unsigend char appStatusB1;
		unsigend char appStatusB0;
		unsigend char protocolStatus;
		unsigend char reserved[3];
	} fcgi_end_request;
<p>
	字段解释：
	<p>
		appStatus组件是应用级别的状态码。protocolStatus组件是协议级别的状态码；protocolStatus的值可能是：
	</p>	
</p>
	FCGI_REQUEST_COMPLETE:请求的正常结束
	FCGI_CANT_MPX_CONN:拒绝新请求。这发送在Web服务器通过一条线路向应用发送并发的请求时，后者被设计为每条线路每次处理一个请求。
	FCGI_OVERLOADED:拒绝新请求。这发生在应用用完某些资源时，例如数据库连接。
	FCGI_UNKNOWN_ROLE:拒绝新请求。这发生在Web服务器指定了一个应用不能识别的角色时。
<p>
	protocolStatus在PHP中的定义如下：
</p>
	typedef enum _fcgi_protocol_status {
		FCGI_REQUEST_COMPLETE = 0;
		FCGI_CANT_MPX_CONN = 1;
		FCGI_OVERLOADED = 2;
		FCGI_UNKNOWN_ROLE = 3;	
	} dcgi_protocol_status;
<p>
	需要注意dcgi_protocol_status和fcgi_role各个元素的值都是FastCGI协议里定义好的，而非PHP自定义的。
</p>
#####消息通讯样例
***
<p>
	为了简单的表示，消息头只显示消息的类型和消息的id，其他字段都不予以显示。而一行表示一个数据包。下面的例子来着官网
</p>
	{FCGI_BEGIN_REQUEST, 1, {FCGI_RESPONDER,0}}
	{FCGI_PARMS, 1, "\013\002SERVER_PORT80\013\016SERVER_ADDR199.170.183.42 ..."}
	{FCGI_STDIN,           1, "quantity=100&item=3047936"}
	{FCGI_STDOUT,          1, "Content-type: text/html\r\n\r\n<html>\n<head> ... "}
	{FCGI_END_REQUEST,     1, {0, FCGI_REQUEST_COMPLETE}}
<p>
	配合上面各个结构体，则可以大致想到FastCGI响应器的解析和响应流程：首先读取消息头，得到其类型为FCGI_BEGIN_REQUEST，然后解析其消息体，得知其需要的角色就是FCGI_RESPONDER，flag为0，表示请求结束后关闭线路。然后解析第二段消息，得知其消息类型为FCGI_PARAMS，然后直接将消息体里的内容以回车符切割后存入环境变量。与之类似，处理完毕之后，则返回了FCGI_STDOUT消息体和FCGI_END_REQUEST消息体供Web服务器解析。
</p>
#####PHP中的CGI实现
***
<p>
	PHP的CGI实现了FastCGI协议，是一个TCP或UDP协议的服务器接受来自Web服务器的请求，当启动时创建TCP/UDP协议的服务器的socket监听，并接收相关请求进行处理。随后就进入了PHP的生命周期：模块初始化，sapi初始化，处理PHP请求，模块关闭，sapi关闭等就构成了整个CGI的生命周期。
	<p>
		以TCP为例，在TCP的服务端，一般会执行这样几个操作步骤：
		<p>
			1、调用socket函数创建一个TCP用的流式套接字；
		</p>
		<p>
			2、调用bind函数将服务器的本地地址与前面创建的套接字绑定；
		</p>
		<p>
			3、调用listen函数将新创建的套接字作为监听，等待客户端发起的连接，当客户端有多个连接连接到这个套接字时，可能需要排队处理；
		</p>
		<p>
			4、服务器进程调用accept函数进入阻塞状态，直到有客户进程调用connect函数而建立一个连接；
		</p>
		<p>
			5、当与客户端创建连接后，服务器调用read_stream函数读取客户的请求；
		</p>
		<p>
			6、处理完数据后，服务器调用write函数向客户端发送应答。
		</p>
	</p>		
</p>
<p>
	TCP上客户-服务器事务的时序如图2.6所示：
</p>
<div class="book-img" style="text-align: center;">
<img src="/img/02-02-03-tcp.jpg" alt="图2.6 TCP上客户-服务器事务的时序">
<div class="book-img-desc">图2.6 TCP上客户-服务器事务的时序</div>
</div>
<p>
	PHP的CGI实现从cgi_main.c文件的main函数开始，在main函数中调用了定义在fastcgi.c文件中的初始化，监听等函数。对比TCP的流程，我们查看PHP对TCP协议的实现，虽然PHP本身也实现了这些流程，但是在main函数中一些过程被封装成一个函数实现。对应TCP的操作流程，PHP首先会执行创建socket，绑定套接字，创建监听：
</p>
	if (bindpath) {
		fcgi_fd = fcgi_listen(bindpath, 128);	//实现Socket监听，调用fcgi_init初始化
		…
	}
<p>
	在fastcgi.c文件中，fcgi_listen函数主要用于创建，绑定socket并开始监听，它走完了前面所列TCP流程的前三个阶段，
</p>
	    if ((listen_socket = socket(sa.sa.sa_family, SOCK_STREAM, 0)) < 0 ||
        ...
        bind(listen_socket, (struct sockaddr *) &sa, sock_len) < 0 ||
        listen(listen_socket, backlog) < 0) {
        ...
    }
<p>
	当服务端初始化完成后，进程调用accept函数进入阻塞状态，在main函数中我们看到如下代码：
</p>
	while (parent) {
        do {
            pid = fork();   //  生成新的子进程
            switch (pid) {
            case 0: //  子进程
                parent = 0;
 
                /* don't catch our signals */
                sigaction(SIGTERM, &old_term, 0);   //  终止信号
                sigaction(SIGQUIT, &old_quit, 0);   //  终端退出符
                sigaction(SIGINT,  &old_int,  0);   //  终端中断符
                break;
                ...
                default:
                /* Fine */
                running++;
                break;
        } while (parent && (running < children));
 
    ...
        while (!fastcgi || fcgi_accept_request(&request) >= 0) {
        SG(server_context) = (void *) &request;
        init_request_info(TSRMLS_C);
        CG(interactive) = 0;
                    ...
            }
<p>
	如上对应服务器端读取用户的请求数据。	
</p>
<p>
	在请求初始化完成，读取请求完毕后，就该处理请求的PHP文件了。 假设此次请求为PHP_MODE_STANDARD则会调用php_execute_script执行PHP文件。 在此函数中它先初始化此文件相关的一些内容，然后再调用zend_execute_scripts函数，对PHP文件进行词法分析和语法分析，生成中间代码， 并执行zend_execute函数，从而执行这些中间代码。关于整个脚本的执行请参见第三节 脚本的执行。
</p>
<p>
	在处理完用户的请求后，服务器端将返回信息给客户端，此时在main函数调用的是fcgi_finish_request(&request, 1); fcgi_finish_request函数定义在fastcgi.c文件中，其代码如下：
</p>
	int fcgi_finish_request(fcgi_request *req, int force_close)
	{
		int ret = 1;
		
		if (req->fd >= 0) {
			if (!req->closed) {
        		ret = fcgi_flush(req, 1);
        		req->closed = 1;
    		}
    		fcgi_close(req, force_close, 1);
		}
		return ret;
	}
<p>
	如上，当socket处于打开状态，并且请求未关闭，则会将执行后的结果刷到客户端，并将请求的关闭设置为真。 将数据刷到客户端的程序调用的是fcgi_flush函数。在此函数中，关键是在于答应头的构造和写操作。 程序的写操作是调用的safe_write函数，而safe_write函数中对于最终的写操作针对win和linux环境做了区分， 在Win32下，如果是TCP连接则用send函数，如果是非TCP则和非win环境一样使用write函数。如下代码：
</p>
	#ifdef _WIN32
	if (!req->tcp)
	{
		ret = write(req->fd, ((char*)buf)+n, count-n);
	}else {
		ret = send(req->fd, ((char*)buf)+n, count-n, 0);
		if (ret <= 0)
		{
			error = WSAGetLastError();
		}
	}
	#else
	ret = write(req->fd, ((char*)buf)+n, count-n);
	#endif
<p>
	在发送了请求的应答后，服务器端将会执行关闭操作，仅限于CGI本身的关闭，程序执行的是fcgi_close函数。 fcgi_close函数在前面提的fcgi_finish_request函数中，在请求应答完后执行。同样，对于win平台和非win平台有不同的处理。 其中对于非win平台调用的是write函数。
</p>
<p>
	以上是一个TCP服务器端实现的简单说明。这只是我们PHP的CGI模式的基础，在这个基础上PHP增加了更多的功能。 在前面的章节中我们提到了每个SAPI都有一个专属于它们自己的sapi_module_struct结构：cgi_sapi_module，其代码定义如下：
</p>
	/* {{{ sapi_module_struct cgi_sapi_module
 	*/
	static sapi_module_struct cgi_sapi_module = {
	"cgi-fcgi",                     /* name */
	"CGI/FastCGI",                  /* pretty name */
 
	php_cgi_startup,                /* startup */
	php_module_shutdown_wrapper,    /* shutdown */
 
	sapi_cgi_activate,              /* activate */
	sapi_cgi_deactivate,            /* deactivate */
 
	sapi_cgibin_ub_write,           /* unbuffered write */
	sapi_cgibin_flush,              /* flush */
	NULL,                           /* get uid */
	sapi_cgibin_getenv,             /* getenv */
 
	php_error,                      /* error handler */
 
	NULL,                           /* header handler */
	sapi_cgi_send_headers,          /* send headers handler */
	NULL,                           /* send header handler */
 
	sapi_cgi_read_post,             /* read POST data */
	sapi_cgi_read_cookies,          /* read Cookies */
 
	sapi_cgi_register_variables,    /* register server variables */
	sapi_cgi_log_message,           /* Log message */
	NULL,                           /* Get request time */
	NULL,                           /* Child terminate */
	 
	STANDARD_SAPI_MODULE_PROPERTIES
	};
	/* }}} */
<p>
	同样，以读取cookie为例，当我们在CGI环境下，在PHP中调用读取Cookie时， 最终获取的数据的位置是在激活SAPI时。它所调用的方法是read_cookies。 由SAPI实现来实现获取cookie，这样各个不同的SAPI就能根据自己的需要来实现一些依赖环境的方法。
</p>
	SG(request_info).cookie_data = sapi_module.read_cookies(TSRMLS_C);
<p>
	所有使用PHP的场合都需要定义自己的SAPI，例如在第一小节的Apache模块方式中， sapi_module是apache2_sapi_module，其对应read_cookies方法的是php_apache_sapi_read_cookies函数， 而在我们这里，读取cookie的函数是sapi_cgi_read_cookies。 从sapi_module结构可以看出flush对应的是sapi_cli_flush，在win或非win下，flush对应的操作不同， 在win下，如果输出缓存失败，则会和嵌入式的处理一样，调用php_handle_aborted_connection进入中断处理程序， 而其它情况则是没有任何处理程序。这个区别通过cli_win.c中的PHP_CLI_WIN32_NO_CONSOLE控制。
</p>
####第三节 PHP脚本的执行
#####程序的执行
***
<p>
	1、php程序完成基本的准备工作后启动PHP及Zend引擎，加载注册的扩展模块。	
</p>
<p>
	2、初始化完成后读取脚本文件，Zend引擎对脚本进行词法分析，语法分析。然后编译成opcode执行。如果安装了apc之类的opcode缓存，编译环节可能会被跳过而直接从缓存中读取opcode执行。
</p>
#####脚本的编译执行
***
<p>
	PHP在读取到脚本文件后首先对代码进行词法分析，PHP的词法分析器是通过lex生成的，词法规则文件在$PHP_SRC/Zend_language_scanner.l，这一阶段lex会会将源代码按照词法规则切分一个一个的标记(token)。PHP中提供了一个函数token_get_all()，该函数接收一个字符串参数，返回一个按照词法规则切分好的数组。
</p>
	<?php 	
	$code = <<<PHP_CODE
	<?php
	$str = "Hello, Tipi\n";
	echo $str;
	PHP_CODE;

	var_dump(token_get_all($code));
<p>
	运行上面的脚本会输出如下：
</p>
	array (size=11)
	  0 => 
	    array (size=3)
	      0 => int 376				//脚本开始标记
	      1 => string '<?php		//匹配到的字符串
	
	' (length=7)
	      2 => int 1
	  1 => 
	    array (size=3)
	      0 => int 379
	      1 => string ' ' (length=1)
	      2 => int 2
	  2 => string '=' (length=1)
	  3 => 
	    array (size=3)
	      0 => int 379
	      1 => string ' ' (length=1)
	      2 => int 2
	  4 => 
	    array (size=3)
	      0 => int 318
	      1 => string '"Hello, Tipi
	"' (length=14)
	      2 => int 2
	  5 => string ';' (length=1)
	  6 => 
	    array (size=3)
	      0 => int 379
	      1 => string '
	
	' (length=2)
	      2 => int 3
	  7 => string '@' (length=1)
	  8 => 
	    array (size=3)
	      0 => int 319
	      1 => string 'echo' (length=4)
	      2 => int 4
	  9 => 
	    array (size=3)
	      0 => int 379
	      1 => string ' ' (length=1)
	      2 => int 4
	  10 => string ';' (length=1)
<p>
	这是在Zend引擎词法分析做的事情，将代码切分为一个个的标记，然后使用语法分析器(PHP使用bison生成语法分析器，规则见$PHP+SRC/Zend/zend_language_parser.y)，bison根据规则进行相应的处理，如果代码找不到匹配的规则，也就是语法错误时Zend引擎会停止，并输出错误信息。比如缺少括号，或者不符合语法规则的情况都会在这个环节检查。在匹配到相应的语法规则后，Zend引擎还会进行编译，将代码编译为opcode，完成后，Zend引擎会执行这些opcode，在执行opcode的过程还有可能会继续重复进行编译-执行，例如执行eval,include/require等语句，因为这些语句还会包含或者执行其他文件或者字符串中的脚本。
</p>
<p>
	例如上例中的echo语句会编译为一条ZEND_ECHO指令，该指令由C函数zend_print_variable(zval* z)执行，将传递进来的字符串打印出来。为了方便理解，本例中省去了一些细节，如opcode指令和处理函数之间的映射关系等。
</p>
####词法分析和语法分析
<p>
	编程语言的编译器(compiler)或解释器(interpreter)一般包括两大部分：
	<p>
		1、读取源程序，并处理语言结构。
	</p>
	<p>
		2、处理语言结构并生成目标程序。
	</p>
</p>
<p>
	Lex和Yacc可以解决第一个问题。第一个部分也可以分为两个部分：
	<p>
		1、将代码切分为一个个的标记(token)
	</p>
	<p>
		2、处理程序的层级结构(hierarchical structure)
	</p>
</p>
#####Lex/Flex
***
<p>
	Lex读取词法规则文件，生成词法分析器。目前通常使用Flex以及Bison来完成同样的工作，Flex和Lex之间并不兼容，Bison则是兼容Yacc的实现。
</p>
<p>
	词法规则文件一般以.l作为扩展名，flex文件由三个部分组成，三部分之间用%%分割：
</p>
	定义段
	%%
	规则段
	%%
	用户代码段
<p>
	例如以下一个用于统计文件字符、词以及行数的例子：
</p>
	%option noyywrap
	%{
	int chars = 0;
	int words = 0;
	int lines = 0;	
	%}
	
	%%
	[a-zA-Z]+ { words++; chars += strlen(yytext);}
	\n { chars++; lines++;}
	.  { chars++;}
	%%

	main(int argc, char **argv)
	{
		if(argc > 1){
			if(!(yyin = fopen(argv[1], "r"))) {
	            perror(argv[1]);
	            return (1);
	        }
	        yylex();
	        printf("%8d%8d%8d\n", lines, words, chars);
		}
	}
<p>
	该解释器读取文件内容，根据规则段定义的规则进行处理，规则后面大括号中包含的是动作，也就是匹配到该规则程序执行的动作，这个例子中的匹配动作时记录下文件的字符，词以及行数信息并打印出来。其中的规则使用正则表达式描述。
</p>
<p>
	回到PHP的实现，PHP以前使用的是flex，后来PHP的词法解析改为re2c,$PHP_SRC/Zend/zend_language_scanner.l文件是re2c的规则文件，所以如果修改该规则文件需要安装re2c才能重新编译。
</p>
#####Yacc/Bison
***
<p>
	Bison和flex类似，也是使用%%作为分界不过Bison接受的标记(token)序列，根据定义的语法规则，来执行一些动作，Bison使用巴科斯范式(BNF)来描述语法。
</p>
<p>
	下面以php中echo语句的编译为例：echo可以接受多个参数， 这几个参数之间可以使用逗号分隔，在PHP的语法规则如下：
</p>
	echo_expr_list:
			echo_expr_list ',' expr { zend_do_echo(&$3 TSRMLS_CC); }
		|	expr					{ zend_do_echo(&$1 TSRMLS_CC); }	
	;
<p>
	其中echo_expr_list规则为一个递归规则，这样就允许接受多个表达式作为参数。在上例中匹配到echo时会执行zend_do_echo函数，函数中的参数可能看起来比较奇怪，其中的$3表示前面规则的第三个定义，也就是expr这个表达式的值，zend_do_echo函数则根据表达式的信息编译opcode，其他的语法规则也类似。这和C语言或者JAVA的编译器类似，不过GCC等编译器时将代码编译为机器码，Java编译器将代码编译为字节码。
</p>
####opcode
<p>
	opcode是计算机指令中的一部分，用于指定要执行的操作，指令的格式和规范由处理器的指令规范指定。除了指令本身以外通常还有指令所需要的操作数，可能有的指令不需要显示的操作数。这些操作数可能是寄存器中的值，堆栈中的值，某块内存的值或者IO端口中的值等等。
</p>
<p>
	通常opcode还有另一种称谓: 字节码(byte codes)。 例如Java虚拟机(JVM)，.NET的通用中间语言(CIL: Common Intermeditate Language)等等。
</p>
#####PHP的opcode
***
<p>	
	PHP是构建在Zend虚拟机(Zend VM)之上的。PHP的opcode就是Zend虚拟机中的指令。
</p>
<p>
	在PHP实现内部，opcode由如下的结构体表示：
</p>
	struct _zend_op {
		opcode_handler_t handler;		//执行该opcode时调用的处理函数
		znode result;
		znode op1;
		znode op2;
		ulong extended_value;
		uint lineno;
		zend_uchar opcode;			// opcode代码
	}
<p>
	和CPU的指令类似，有一个标示指令的opcode字段，以及这个opcode所操作的操作数，PHP不像汇报那么底层，在脚本实际执行的时候可能还需要其他更多的信息，extended_value字段就保存了这类信息，其中的result域则是保存该指令执行完成后的结果。
</p>
<p>
	例如如下代码是在编译器遇到print语言的时候进行编译的函数：
</p>
	void zend_do_print(znode *result, const znode *arg TSRMLS_DC)
	{
		zend_op *opline = get_next_op(CG(active_op_array) TSRMLS_CC);

		opline->result.op_type = IS_TMP_VAR;
	    opline->result.u.var = get_temporary_variable(CG(active_op_array));
	    opline->opcode = ZEND_PRINT;
	    opline->op1 = *arg;
	    SET_UNUSED(opline->op2);
	    *result = opline->result;
	}
<p>
	这个函数新创建一条zend_op，将返回值的类型设置为临时变量(IS_TMP_VAR)，并为临时变量申请空间， 随后指定opcode为ZEND_PRINT，并将传递进来的参数赋值给这条opcode的第一个操作数。这样在最终执行这条opcode的时候， Zend引擎能获取到足够的信息以便输出内容。
</p>
<p>
	下面这个函数是在编译器遇到echo语句的时候进行编译的函数:
</p>
	void zend_do_echo(const znode *arg TSRMLS_DC)
	{
	    zend_op *opline = get_next_op(CG(active_op_array) TSRMLS_CC);
	 
	    opline->opcode = ZEND_ECHO;
	    opline->op1 = *arg;
	    SET_UNUSED(opline->op2);
	}
<p>
	可以看到echo处理除了指定opcode以外，还将echo的参数传递给op1，这里并没有设置opcode的result结果字段。 从这里我们也能看出print和echo的区别来，print有返回值，而echo没有，这里的没有和返回null是不同的， 如果尝试将echo的值赋值给某个变量或者传递给函数都会出现语法错误。
</p>
<p>
	PHP脚本编译为opcode保存在op_array中，其中内部存储的结构如下：
</p>
	struct _zend_op_array {
		/* Common elements */
		zend_uchar type;
		char *function_name;		// 如果是用户定义的函数则，这里将保存函数的名字
		zend_class_entry *scope;
		zend_uint fn_flags;
		union _zend_function *prototype;
		zend_uint num_args;
		zend_uint required_num_args;
		zend_arg_info *arg_info;
		zend_bool pass_rest_by_reference;
		unsigned char return_reference;
		/* END of common elements */

		zend_bool done_pass_two;
		
		zend_uint *refcount;

		zend_op *opcode;		//opcode数组
		
		zend_uint last, size;
	
		zend_compiled_variable *vars;
		int last_var, size_var;
	}
<p>
	如上面的注释，opcodes保存在这里，在执行的时候由下面的execute函数执行：
</p>
	ZEND_API void execute(zend_op_array *op_array TERMLS_DC)
	{
		// …… 循环执行op_array中的opcode或者执行其他op_array中的opcode
	}
<p>
	PHP有三种方式进行opcode的处理：CALL，SWITCH和GOTO，PHP默认使用CALL的方式，也就是函数调用的方式，由于opcode执行是每个PHP程序频繁需要进行的操作，可以使用SWITCH或者GOTO的方式分发，通常GOTO的效率相对高一些，不过效率是否提高依赖于不同的CPU。
</p>
####opcode处理函数查找
#####Debug法
***
<p>
	在学习研究PHP内核的过程中，经常通过opcode来查看代码的执行顺序，opcode的执行由在文件Zend/zend_vm_execute.h中的execute函数执行。
</p>
	ZEND_API void execute(zend_op_array *op_array TSRMLS_DC)
	{
		...
		zend_vm_enter;
		...
		if ((ret = EX(opline)->handler(execute_data TSRMLS_CC)) > 0){
			switch (ret){
				case 1:
					EG(in_execution) = original_in_execution;
					return;
				case 2:
					op_array = EG(active_op_array);
					return;
				case 3:
					execute_data = EG(current_execute_data);
				default:
					break;
			}
		}
		...
	}
<p>
	在执行的过程中，EX(opline)->handler（展开后为 *execute_data->opline->handler）存储了处理当前操作的函数指针。使用gdb调试，在execute函数处增加断点，使用p命令可以打印出类似这样的结果：
</p>
	(gdb) p *execute_data->opline->handler
	$l = {int (zend_execute_data *)} 0x10041f394 <ZEND_NOP_SPEC_HANDLER>
<p>
	这样就可以方便的知道当前要执行的处理函数了，这种debug的方法。这种方法比较麻烦，需要使用gdb来调试。
</p>
#####计算法
***
<p>
	在PHP内部有一个函数用来快速的返回特定opcode对应的opcode处理函数指针：zend_vm_opcode_handler()函数：
</p>
	static opcode_handler_t
	zend_vm_get_opcode_handler(zend_uchar opcpde, zend_op* op)
	{
		static const int zend_vm_decode[] = {
			 _UNUSED_CODE, /* 0              */
            _CONST_CODE,  /* 1 = IS_CONST   */
            _TMP_CODE,    /* 2 = IS_TMP_VAR */
            _UNUSED_CODE, /* 3              */
            _VAR_CODE,    /* 4 = IS_VAR     */
            _UNUSED_CODE, /* 5              */
            _UNUSED_CODE, /* 6              */
            _UNUSED_CODE, /* 7              */
            _UNUSED_CODE, /* 8 = IS_UNUSED  */
            _UNUSED_CODE, /* 9              */
            _UNUSED_CODE, /* 10             */
            _UNUSED_CODE, /* 11             */
            _UNUSED_CODE, /* 12             */
            _UNUSED_CODE, /* 13             */
            _UNUSED_CODE, /* 14             */
            _UNUSED_CODE, /* 15             */
            _CV_CODE      /* 16 = IS_CV     */
		};
		return zend_opcode_handlers[
			opcode * 25 + zend_vm_decode[op->opl.op_type] * 5
					+ zend_vm_decode[op->op2.op_type]];
	}
<p>
	由上面的代码可以看到，opcode到php内部函数指针的查找是由下面的公式来进行的：
</p>
	opcode * 25 + zend_vm_decode[op->opl.op_type] * 5
					+ zend_vm_decode[op->op2.op_type]];
<p>
	然后将其计算的数值作为索引到zend_init_opcodes_handlers数组中进行查找。不过这个数组实在是太大了，有3851个元素，手动查找和计算都比较麻烦。
</p>
#####命名查找法
***
<p>
	上面的两种方法其实都是比较麻烦的，在定位某一opcode的实现执行代码的过程中，都不是不对程序进行执行或者计算中间值。而在追踪的过程中，发现处理函数名称是有一定规则的。这里以函数调用的opcode为例，调用某函数的opcode及其对应在php内核中实现的处理函数如下：
</p>
	//函数调用：
	DO_FCALL ==> ZEND_DO_FCALL_SPEC_CONST_HANDLER
	
	//变量赋值：
	ASSIGN =>	ZEND_ASSIGN_SPEC_VAR_CONST_HANDLER
				ZEND_ASSIGN_SPEC_VAR_TMP_HANDLER
                ZEND_ASSIGN_SPEC_VAR_VAR_HANDLER
                ZEND_ASSIGN_SPEC_VAR_CV_HANDLER 

	//变量加法：
	ASSIGN_SUB =>	ZEND_ASSIGN_SUB_SPEC_VAR_CONST_HANDLER,
                    ZEND_ASSIGN_SUB_SPEC_VAR_TMP_HANDLER,
                    ZEND_ASSIGN_SUB_SPEC_VAR_VAR_HANDLER,
                    ZEND_ASSIGN_SUB_SPEC_VAR_UNUSED_HANDLER,
                    ZEND_ASSIGN_SUB_SPEC_VAR_CV_HANDLER,
                    ZEND_ASSIGN_SUB_SPEC_UNUSED_CONST_HANDLER,
                    ZEND_ASSIGN_SUB_SPEC_UNUSED_TMP_HANDLER,
                    ZEND_ASSIGN_SUB_SPEC_UNUSED_VAR_HANDLER,
                    ZEND_ASSIGN_SUB_SPEC_UNUSED_UNUSED_HANDLER,
                    ZEND_ASSIGN_SUB_SPEC_UNUSED_CV_HANDLER,
                    ZEND_ASSIGN_SUB_SPEC_CV_CONST_HANDLER,
                    ZEND_ASSIGN_SUB_SPEC_CV_TMP_HANDLER,
                    ZEND_ASSIGN_SUB_SPEC_CV_VAR_HANDLER,
                    ZEND_ASSIGN_SUB_SPEC_CV_UNUSED_HANDLER,
                    ZEND_ASSIGN_SUB_SPEC_CV_CV_HANDLER,
<p>
	在上面的命名就会发现，其实处理函数的命名是有以下规律的：
</p>
	ZEND_[opcode]_SPEC_(变量类型1)_(变量类型2)_HANDLER
<p>
	这里的变量类型1和变量类型2是可选的，如果同时存在，那就是左值和右值，归纳有下几类：VAR TMP CV UNUSED CONST 这样可以根据相关的执行场景来判定。 
</p>
#####日志记录法
***
<p>
	这种方法是上面计算法的升级，同时也是比价精准的方式。在zend_vm_opcode_handler方法中添加以下代码：
</p>
	static opcode_handler_t
	zend_vm_get_opcode_handler(zend_uchar opcode, zend_op* op)
	{
		static const int zend_vm_decode[] = {
			_UNUSED_CODE, /* 0              */
            _CONST_CODE,  /* 1 = IS_CONST   */
            _TMP_CODE,    /* 2 = IS_TMP_VAR */
            _UNUSED_CODE, /* 3              */
            _VAR_CODE,    /* 4 = IS_VAR     */
            _UNUSED_CODE, /* 5              */
            _UNUSED_CODE, /* 6              */
            _UNUSED_CODE, /* 7              */
            _UNUSED_CODE, /* 8 = IS_UNUSED  */
            _UNUSED_CODE, /* 9              */
            _UNUSED_CODE, /* 10             */
            _UNUSED_CODE, /* 11             */
            _UNUSED_CODE, /* 12             */
            _UNUSED_CODE, /* 13             */
            _UNUSED_CODE, /* 14             */
            _UNUSED_CODE, /* 15             */
            _CV_CODE      /* 16 = IS_CV     */
		}
		//很显然，我们把opcode和相对应的写到了/tmp/php.log文件中
		int op_index;
		op_index = opcode * 25 + zend_vm_decode[op->opl.op_type] * 5 + zend_vm_decode[op->op2.op_type];
		
		FILE *strem;
		if((stream = fopen("/tmp/php.log", "a+")) != NULL){
			fprintf(stream, "opcode: %d, zend_opcpde_handler_index:%d\n", opcode, op_index);
		}
		fclose(stream);

		return zend_opcode_handlers[
			opcode * 25 + zend_vm_decode[op->op1.op_type] * 5
					+ zend_vm_decode[op-op2.op_type>]];
	}
<p>
	然后，就可以在/tmp/php.log文件中生成类似如下结果：
</p>
	opcode: 38 , zend_opcode_handlers_index:970
<p>
	前面的数字是opcode的，我们可以这里查到：http://php.net/manual/en/internals2.opcodes.list.php 后面的数字是static const opcode_handler_t_labels[]索引，里面对应了处理函数的名称，对应源码文件是Zend/zend_vm_execute.h(第30077行左右)。这是个超大的数组，PHP5.3.4中有3851元素。
</p>
###第三章 变量及数据类型
<p>
	变量具有三个基本组成部分：
	<p>
	1、名称 变量的标识符
	</p>
	<p>
	2、类型 变量的类型
	</p>
	<p>
	3、值内容 这个标示所代表的具体内容
	</p>
</p>
<p>
	PHP中组成变量的字母可以是英文字母a-z,A-Z,还可以是ASCII字符从127到255(0x7f-0xff)。变量名是区分大小写的。
</p>
<p>
	除了变量本身，在PHP中我们经常会接触到与变量相关的一些概念，比如：常量，全局变量，静态变量以及类型转换等。 本章我们将介绍这些与变量相关的实现。其中包括PHP本身的变量底层存储结构、弱类型系统的实现以及这些类型之间的相互转换等。
</p>
###第一节 变量的结构和类型
####一.PHP变量类型及存储结构
***
<p>
	PHP中，存在8种变量类型，可以分为三类：
	<p>
	标量类型：boolean,integer,float(double),string
	</p>
	<p>
	复合类型：array,object
	</p>
	<p>
	特殊类型：resource,NULL
	</p>
</p>
#####1.变量存储结构
***
<p>
变量的值存储到以下所示zval结构体中。zval结构体定义在Zend/zend.h文件，其结构如下：
</p>
	typedef struct _zval_struct zval;
	struct _zval_struct {
		/* Variable information */
		zvalue_value value;		/* value */
		zend_uint refcount__gc;
		zned_uchar type;		/* active type */
		zend_uchar is_ref_gc;
	}
<p>
	zval结构体中有四个字段，其含义分别为：
</p>
<table>
<thead>
<tr>
  <th align="left">属性名</th>
  <th align="left">含义</th>
  <th align="center">默认值</th>
</tr>
</thead>
<tbody>
<tr>
  <td align="left">refcount__gc</td>
  <td align="left">表示引用计数</td>
  <td align="center">1</td>
</tr>
<tr>
  <td align="left">is_ref__gc</td>
  <td align="left">表示是否为引用</td>
  <td align="center">0</td>
</tr>
<tr>
  <td align="left">value</td>
  <td align="left">存储变量的值</td>
  <td align="center"></td>
</tr>
<tr>
  <td align="left">type</td>
  <td align="left">变量具体的类型</td>
  <td align="center"></td>
</tr>
</tbody>
</table>
<p>
	在PHP5.3之后，引入了新的垃圾收集机制，引用计数和引用的字段名改为refcount__gc和is_ref__gc。在此之前为refcount和is__ref。
</p>
#####2.变量类型：
***
<p>
	zval结构体的type字段就是实现弱类型最关键的字段了，type的值可以为：IS_NULL、IS_BOOL、IS_DOUBLE、IS_STRING、IS_ARRAY、IS_RESOURCE之一。从字面上就很好理解，他们只是类型的唯一标示，根据类型的不同将不同点的值存储到value字段。除此之外，和他们定义在一起的类型还有IS_CONSTANT和IS_CONSTANT_ARRAY。
</p>
#####二.变量的值存储
***
	typedef union _zvalue_value {
		long lval;			/* long value */
		double dval;		/* double value */
		struct {
			char *val;
			int len;
		} str;
		HashTable *ht;		/* hash table value */
		zend_object_value obj;
	} zvalue_value;
<p>
	这里使用联合体而不是用结构体是出于空间利用率的考虑，因为一个变量同时只能属于一种类型。如果使用结构体的话将会不必要的浪费空间，而PHP中的所有逻辑都围绕变量来进行的，这样的话，内存浪费将是十分大的。这种做法成本小但收益非常大。
</p>
<p>
	各种类型的数据会使用不同的方法来进行变量值的存储，其对应赋值方式如下：
</p>
<p>
	一般类型
</p>
<table>
<thead>
<tr>
  <th align="left">变量类型</th>
  <th align="center">宏</th>
  <th align="left"></th>
</tr>
</thead>
<tbody>
<tr>
  <td align="left">boolean</td>
  <td align="left">ZVAL_BOOL</td>
  <td align="left" rowspan='3'>
    布尔型/整型的变量值存储于(zval).value.lval中，其类型也会以相应的IS_*进行存储。
    <pre class="c"> Z_TYPE_P(z)=IS_BOOL/LONG;  Z_LVAL_P(z)=((b)!=0); </pre>
</td>
</tr>
<tr>
  <td align="left">integer</td>
  <td align="left">ZVAL_LONG</td>
</tr>
<tr>
  <td align="left">float</td>
  <td align="left">ZVAL_DOUBLE</td>
</tr>
<tr>
  <td align="left">null</td>
  <td align="left">ZVAL_NULL</td>
  <td align="left" >
    NULL值的变量值不需要存储，只需要把(zval).type标为IS_NULL。
    <pre class="c"> Z_TYPE_P(z)=IS_NULL; </pre>
    </td>
</tr>
<tr>
  <td align="left">resource</td>
  <td align="left">ZVAL_RESOURCE</td>
  <td align="left" >
    资源类型的存储与其他一般变量无异，但其初始化及存取实现则不同。
    <pre class="c"> Z_TYPE_P(z) = IS_RESOURCE;  Z_LVAL_P(z) = l; </pre>
    </td>
</tr>
</tbody>
</table>
<p>
字符串String
</p>
<p>
	字符串的类型标示和其他数据类型一样，不过在存储字符串时多了一个字符串长度的字段。
</p>
	struct {
		char *val;
		int len;
	}
<p>
	C中字符串是以\0结尾的字符数组，这里多存储了字符串的长度，这和我们在设计数据库时增加的冗余字段异曲同工。因为要实时获取到字符串的长度的时间复杂度是0(n),而字符串的操作在PHP是非常频繁的，这样能避免重复计算字符串的长度，这能节省大量的时间，是空间换时间的做法。
</p>
<p>
	这么看在PHP中的strlen()函数可以在常数时间内获取到字符串的长度。计算机语言字符串的操作都非常之多，所以大部分高级语言中都会存储字符串的长度。
</p>
<p>
	数组Array
</p>
<p>
	数组是PHP中最常用，也是最强大变量类型，它可以存储其他类型的数据，而且提供各种内置操作函数。数组的存储相对于其他变量要复杂一些， 数组的值存储在zvalue_value.ht字段中，它是一个HashTable类型的数据。 PHP的数组使用哈希表来存储关联数据。哈希表是一种高效的键值对存储结构。PHP的哈希表实现中使用了两个数据结构HashTable和Bucket。 PHP所有的工作都由哈希表实现.
</p>
<p>
	对象Object
</p>
<p>
	在面向对象语言中，我们能自己定义自己需要的数据类型，包括类的属性，方法等数据。而对象则是类的一个具体实现。 对象有自身的状态和所能完成的操作。
</p>
<p>
	PHP的对象是一种复合型的数据，使用一种zend_object_value的结构体来存放。其定义如下：
</p>
	typedef struct _zend_object_value {
		zend_object_handle handle; 			//unsigned int类型， EG(objects_store).object_buckets的索引
		zend_object_handlers *handlers;
	} zend_object_value;
<p>
	PHP的对象只有在运行时才会被创建，前面的章节介绍了EG宏，这是一个全局结构体用于保存在运行时的数据。 其中就包括了用来保存所有被创建的对象的对象池，EG(objects_store)，而object对象值内容的zend_object_handle域就是当前 对象在对象池中所在的索引，handlers字段则是将对象进行操作时的处理函数保存起来。 这个结构体及对象相关的类的结构_zend_class_entry.
</p>
<p>	
	PHP的弱变量容器的实现方式是兼容并包的形式体现，针对每种类型的变量都有其对应的标记和存储空间。 使用强类型的语言在效率上通常会比弱类型高，因为很多信息能在运行之前就能确定，这也能帮助排除程序错误。 而这带来的问题是编写代码相对会受制约。
</p>
<p>
	PHP主要的用途是作为Web开发语言，在普通的Web应用中瓶颈通常在业务和数据访问这一层。不过在大型应用下语言也会是一个关键因素。 facebook因此就使用了自己的php实现。将PHP编译为C++代码来提高性能。不过facebook的hiphop并不是完整的php实现， 由于它是直接将php编译为C++，有一些PHP的动态特性比如eval结构就无法实现。当然非要实现也是有方法的， hiphop不实现应该也是做了一个权衡。
</p>
####哈希表(HashTable)
<p>
	哈希表在实践中使用的非常广泛，例如编译器通常会维护的一个符号表来保存标记，很多高级语言中也显示的支持哈希表。哈希表通常提供查找(Search)，插入(insert)，删除(Delete)等操作，这些操作在最坏的情况下和链表的性能一样为0(n)。不过通常并不会这么坏，合理设计的哈希算法能有效的避免这类情况，通常哈希表的这些操作时间复杂度为0(1)。这也是它被钟爱的原因。
</p>
#####基本概念
***
<p>
	哈希表是一种通过哈希函数，将特定的键映射到特定的值得一种数据结构，它维护键和值之间——对应关系。
	<p>
		键(key)：用于操作数据的标示，例如PHP数组中的索引，或者字符串键等等。
	</p>
	<p>
		槽(slot/bucket)：哈希表中用于保存数据的一个单元，也就是数据真正存放的容器。
	</p>
	<p>
		哈希函数(hash function)：将key映射(map)到数据应该存放的slot所在位置的函数。
	</p>	
	<p>
		哈希冲突(hash collision)：哈希函数将两个不同的key映射到同一个索引的情况。
	</p>	
</p>
<p>
	哈希表可以理解为数组的扩展或者关联数组，数组使用数字下标来寻址，如果关键字(key)的范围较小且是数字的话，我们可以直接使用数组来完成哈希表，而如果关键字范围太大，如果直接使用数组我们需要为所有可能的key申请空间。很多情况下这是不现实的。即使空间不足够，空间利用率也会很低，这并不理想。同时键也可能并不是数字，在PHP中尤为如此，所以人们使用一种映射函数(哈希函数)来将key映射到特定的域中：
</p>
	h(key) -> index
<p>
	通过合理设计的哈希函数，我们就能将key映射到合适的范围，因为我们的key空间可以很大(例如字符串key)， 在映射到一个较小的空间中时可能会出现两个不同的key映射被到同一个index上的情况， 这就是我们所说的出现了冲突。 目前解决hash冲突的方法主要有两种：链接法和开放寻址法。
</p>
####冲突解决
***
#####链接法
<p>
	链接法通过使用一个链表来保存slot值得方式来解决冲突，也就是当不同的key映射到一个槽中的时候使用链表来保存这些值。所以使用链接法是在最坏的情况下，也就是所有key都映射到同一个槽中了，这样哈希表就退化成了一个链表，这样的话操作链表的时间复杂度则成了0(n),这样哈希表的性能优势就没有了，所以选择一个合适的哈希函数是最为关键的。
</p>
<p>
	由于目前大部分的编程语言的哈希表实现都是开源的，大部分语言的哈希算法都是公开的算法，虽然目前的哈希算法都能良好的将key进行比较均匀的分布，而这个假使的前提是key是随机的，正是由于算法的确定性，这就导致了别有用心的黑客能利用已知算法的可确定性来构造一些特殊的key，让这些key都映射到同一个槽位导致哈希表退化成单链表，导致程序性能急剧下降，尤其是对于高并发的应用影响很大，通过大量类似的请求可以让服务器遭受Dos,这个问题一直就存在着，只是最近才被各个语言重视起来。
</p>
<p>
	哈希冲突攻击利用的哈希表最根本的弱点是：开源算法和哈希实现的确定性以及可预测性，这样攻击者才可以利用特殊构造的key来进行攻击。要解决这个问题的方法则是让进攻者无法轻易构造能进行攻击的key序列。
</p>
#####开放寻址法
***
<p>
	通常还有另外一种解决冲突的方法：开放寻址法。使用开放寻址法是槽本身直接存放数据，在插入数据时如果key所映射到的索引已经有数据了，这说明发生了冲突，这是会寻找下一个槽，如果该槽也被占用了则继续寻找下一个槽，直到寻找没有占用的槽，在查找时也使用同样的策略来进行。
</p>
<p>
	由于开放寻址法处理冲突的时候占用的是其他槽位的空间，这可能导致后续的key在插入的时候更加容易出现哈希冲突，所以采用开放寻址法的哈希表的装载因子不能太高，否则容易出现性能下降。
</p>
	装载因子是哈希表保存的元素数量和哈希表容量的比，通常采用链接法解决冲突的哈希表的装载因子最好不要大于1，而采用开放寻址法的哈希表最好不要大于0.5.
####哈希表的实现
***
<p>
	在了解到哈希表的原理之后要实现一个哈希表也很容易，主要需要完成的工作只有三点：
	<p>
		1、实现哈希函数
	</p>
	<p>
		2、冲突的解决
	</p>
	<p>
		3、操作接口的实现
	</p>
</p>
####数据结构
***
<p>
	首先需要一个容器来保存我们的哈希表，哈希表需要保存的内容主要是保存进来的数据，同时为了方便的得哈希表中存储的元素个数，需要保存一个大小字段，第二个需要的就是保存数据的容器啦。作为实例，下面将实现一个简易的哈希表。基本的数据结构主要有两个，一个用于保存哈希表本身，另一个就是用于实际保存数据的单链表了，定义如下：
</p>
	typedef struct _Bucket
	{
		char *key;
		void *value;
		struct _Bucket *next;
	}

	typedef struct _HashTable
	{
		int size;
		int elem_num;
		Bucket** buckets;		
	} HashTable;
<p>
	上面的定义和PHP中的实现类似，为了便于理解裁剪了大部分无关的细节，在本节中为了简化，key的数据类型为字符串，而存储的数据类型可以为任意类型。
</p>
<p>
	Bucket结构体是一个单链表，这是为了解决多个key哈希冲突的问题，也就是前面所提到的链接法。当多个key映射到同一个index的时候将冲突的元素链接起来。
</p>
####哈希函数实现
***
<p>
	哈希函数需要尽可能的将不同的key映射到不同的槽(slot或者bucket)中，首先我们采用一种最为简单的哈希算法实现：将key字符串的所有字符串加起来，然后以结果对哈希表的大小取模，这样索引就能落在数组索引的范围之内了。
</p>
	static int hash_str(char *key)
	{
		int hash = 0;
		char *cur = key;
		
		while(*cur != '\0'){
			hash += *cur;
			++cur;
		}

		return hash;
	}
	// 使用这个宏来求的key在哈希表的索引
	#define HASH_INDEX(ht, key) (hash_str((key)) % (ht)->size )
####操作接口的实现
***
<p>
	为了操作哈希表，实现了如下几个操作接口函数：
</p>
	int hash_init(HashTable *ht);									//初始化哈希表
	int hsah_lookup(HashTable *ht, char *key, void **result);		//根据key查找内容
	int hash_insert(HashTable *ht, char *key, void *value);			//将内容插入到哈希表中
	int hash_remove(HashTable *ht, char *key);						//删除key所指向的内容
	int hash_destroy(HashTable *ht);	
<p>
	下面以初始化、插入和获取操作函数为例：
</p>
	int hash_init(HashTable *ht)
	{
		ht->size		= HASH_TABLE_INIT_SIZE;
		ht->elem_num	= 0;
		ht->buckets		= (Bucket **)calloc(ht->size, sizeof(Bucket *));

		if(ht->buckets == NULL) return FAILED;

		LPG_MSG("[init]\tsize; %i\n", ht->size);

		return SUCCESS;
	}
<p>
	初始化的主要工作是为哈希表申请存储空间，函数中使用calloc函数的目的是确保数据存储的槽为都初始化为0，以便后续在插入和查找时确认该槽为是否被占用。
</p>
	int hash_insert(HashTable *ht, char *key, void *value)
	{
		//check if we need to resize the hashtable
		reszie_hash_table_if_needed(ht);

		int index = HASH_INDEX(ht, key);

		Bucket *org_bucket = ht->buckets[index];
		Bucket *tmp_bucket = org_bucket;

		// check if the key exits already
		while(tmp_bucket)
		{
			if(strcmp(key, tmp_bucket->key) == 0)
			{
				LOG_MSG("[update\tkey: %s\n]", key);
				tmp_bucket->value = value;

				return SUCCESS;
			}
			tmp_bucket = tmp_bucket->next;
		}

		
		Bucket *bucket = (Bucket *)malloc(sizeof(Bucket));

		bucket->key		= key;
		bucket->value	= value;
		bucket->next	= NULL;

		ht->elem_num += 1;
		
		if(org_bucket != NULL)
		{
			LOG_MSG("[collision]\tindex:%d key:%s\n", index, key);
			bucket->next = org_bucket;
		}
		
		ht->buckets[index] = bucket;

		LOG_MSG("[index]\tindex:%d key:%s\tht(num:%d)\n", index, key, ht->elem_num);

		return SUCCESS;
		
	}
<p>
	上面这个哈希表的插入操作比较简单，简单的以key做哈希，找到元素应该存储的位置，并检查该位置是否已经有了内容，如果发生碰撞则将新元素链接到原有元素链表头部。
</p>
<p>
	由于在插入过程中可能会导致哈希表的元素个数比较多，如果超过了哈希表的容量，则说明肯定会出现碰撞，出现碰撞则会导致哈希表的性能下降，为此如果出现元素容量达到容量则需要进行扩容。由于所有的key都进行了哈希，扩容后哈希表不能简单的扩容，而需要重新将原有已插入的预算插入到新的容器中。
</p>
	static void resize_hash_table_if_needed(HashTable *ht)
	{
		if(ht->sizre - ht->elem_num <1)
		{
			hash_resize(ht);
		}
	}

	static int hash_resize(HashTable *ht)
	{
		// double the size
		int org_size = ht->size;
		ht->size = ht->sie *2;
		ht->elem_num = 0;
		
		LOG_MSG("[resize]\torg size: %i\tnew size: %i\n", org_size, ht->size);

		Bucket **bucket = (Bucket **)calloc(ht->size, sizeof(Bucket *));

		Bucket **org_bucket = ht->buckets;
		ht->bucket = bucket;

		int i = 0;
		for(i=0; i < org_size; ++i)
		{
			Bucket *cur = org_buckets[i];
			Bucket *tmp;
			
			while(cur)
			{
				//rehash: insert again
				hash_insert(ht, cur->key, cur->value);
			
				// free the org bucket, but not the element
				tmp = cur;
				cur = cur->next;
				free(tmp); 
			}
		}
		free(org_buckets);

		LOG_MSG("[resize] done\n");

		return SUCCESS;
	}
<p>
	哈希表的扩容首先申请一块新的内存，大小为原来的2倍，然后重新将元素插入到哈希表中，读者会发现扩容的操作的代价0(n)，不过这个问题不大，因为只有在到达哈希表容量的时候才会进行。
</p>
<p>
	在查找时也使用插入同样的策略，找到元素所在的位置，如果存在元素，则将该链表的所有元素的key和要查找的key依次对比，直到找到一致的元素，否则说明该值没有匹配的内容。
</p>
	int hash_lookup(HashTable *ht, char *key, void **result)
	{
		int index = HASH_INDEX(ht, key);
		Bucket *bucket = ht->bucket[index];

		if(bucket == NULL) goto failed;

		while(bucket)
		{
			if(strcmp(bucket->key, key) == 0)
			{
				LOG_MSG("[lookup]\t found %s\tindex:%i value: %p\n", key, index, bucket->value);
				*result = bucket->value;
				return SUCCESS;
			}
			
			bucket = bucket->next;
		}

		failed:
			LOG_MSG("[lookup]\t key:%s\tfailed\t\n", key);
			return FAILED;	
	}
<p>
	PHP中数组是基于哈希表实现的，依次给数组添加元素时，元素之间是有先后顺序的，而这里的哈希表在物理位置上显然是接近平均分布的，这样是无法根据插入的先后顺序获取到这些元素的，在PHP的实现中Bucket结构体还维护了另一个指针字段来维护元素之间的关系。
</p>
###PHP的哈希表实现
####PHP的哈希实现
***
<p>
	PHP内核中的哈希表示十分重要的数据结构，PHP的大部分的语言特性都是基于哈希表实现的，例如：变量的作用域、函数表、类的属性、方法等，Zend引擎内部的很多数据都是保存在哈希表中的。
</p>
####数据结构及说明
***
<p>
	PHP中的哈希表是使用拉链法来解决冲突的，具体点讲就是使用链表来存储哈希到同一个槽位的数据，Zend为了保存数据之间的关系使用了双向链表来链接元素。
</p>
#####哈希表结构
***
<p>
	PHP中的哈希表实现在Zend/zend_hash.c中。 PHP使用如下两个数据结构来实现哈希表，HashTable结构体用于保存整个哈希表需要的基本信息， 而Bucket结构体用于保存具体的数据内容，如下：
</p>
	typedef struct _hashtable { 
	    uint nTableSize;        // hash Bucket的大小，最小为8，以2x增长。
	    uint nTableMask;        // nTableSize-1 ， 索引取值的优化
	    uint nNumOfElements;    // hash Bucket中当前存在的元素个数，count()函数会直接返回此值 
	    ulong nNextFreeElement; // 下一个数字索引的位置
	    Bucket *pInternalPointer;   // 当前遍历的指针（foreach比for快的原因之一）
	    Bucket *pListHead;          // 存储数组头元素指针
	    Bucket *pListTail;          // 存储数组尾元素指针
	    Bucket **arBuckets;         // 存储hash数组
	    dtor_func_t pDestructor;    // 在删除元素时执行的回调函数，用于资源的释放
	    zend_bool persistent;       //指出了Bucket内存分配的方式。如果persisient为TRUE，则使用操作系统本身的内存分配函数为Bucket分配内存，否则使用PHP的内存分配函数。
	    unsigned char nApplyCount; // 标记当前hash Bucket被递归访问的次数（防止多次递归）
	    zend_bool bApplyProtection;// 标记当前hash桶允许不允许多次访问，不允许时，最多只能递归3次
	#if ZEND_DEBUG
	    int inconsistent;
	#endif
	} HashTable;
<p>
	nTableSize字段用于标示哈希表的容量，哈希表的初始容量最小为8.首先看看哈希表的初始化函数：
</p>
	ZEND_API int _zend_hash_init(HashTable *ht, uint nSize, hash_func_t pHashFunction,
                    dtor_func_t pDestructor, zend_bool persistent ZEND_FILE_LINE_DC)
	{
	    uint i = 3;
	    //...
	    if (nSize >= 0x80000000) {
	        /* prevent overflow */
	        ht->nTableSize = 0x80000000;
	    } else {
	        while ((1U << i) < nSize) {
	            i++;
	        }
	        ht->nTableSize = 1 << i;
	    }
	    // ...
	    ht->nTableMask = ht->nTableSize - 1;
	 
	    /* Uses ecalloc() so that Bucket* == NULL */
	    if (persistent) {
	        tmp = (Bucket **) calloc(ht->nTableSize, sizeof(Bucket *));
	        if (!tmp) {
	            return FAILURE;
	        }
	        ht->arBuckets = tmp;
	    } else {
	        tmp = (Bucket **) ecalloc_rel(ht->nTableSize, sizeof(Bucket *));
	        if (tmp) {
	            ht->arBuckets = tmp;
	        }
	    }
	 
	    return SUCCESS;
	}
<p>
	例如如果设置初始大小为10，则上面的算法将会将大小调整为16.也就是始终将大小调整为接近初始大小的2的整数次方。
</p>
<p>
	例如大小为8的哈希表，哈希值为100， 则映射的槽位索引为: 100 % 8 = 4，由于索引通常从0开始， 所以槽位的索引值为3，在PHP中使用如下的方式计算索引：
</p>
	h = zend_inline_hash_func(arKey, nKeyLength);
	nIndex = h & ht->nTableMask;
<p>
	从上面的_zend_hash_init()函数中可知，ht->nTableMask的大小为ht->nTableSize-1。这里使用&操作而不是使用取模，这是因为是相对来说取模操作的消耗和按位与的操作大很多。
</p>
	mask的作用就是将哈希值映射到槽位所能存储的索引范围内。 例如：某个key的索引值是21， 哈希表的大小为8，则mask为7，则求与时的二进制表示为： 10101 & 111 = 101 也就是十进制的5。 因为2的整数次方-1的二进制比较特殊：后面N位的值都是1，这样比较容易能将值进行映射， 如果是普通数字进行了二进制与之后会影响哈希值的结果。那么哈希函数计算的值的平均分布就可能出现影响。
<p>
	设置好哈希表大小之后就需要为哈希表申请存储数据的空间了，如上面初始化的代码，根据是否持久保存而调用了不同的内存申请方法。如前面PHP生命周期里介绍的是需要持久保存体现在：持久内容能在多个请求之间访问，而非持久存储是会在请求结束时释放占用的空间。
</p>
<p>
	HashTable中的nNumOfElements字段很好理解，每插入一个元素或者unset删除元素时会更新这个字段。这样在进行count()函数统计数组元素个数就能快速的返回。
	nNextFreeElement字段非常有用。先看一段PHP代码：
</p>
	<?php
		$a = array(10 => 'Hello');
		$a[] = 'TIPI';
		var_dump($a);

		// output
		array(2) {
			[10] =>	
			string(5) "Hello",
			[11] =>
			string(4) "TIPI"
		} 
<p>
	PHP中可以不指定索引值向数组中添加元素，这时将默认使用数字作为索引，和C语言中的枚举类似，而这个元素的索引到底是多少就由nNextFreeElement字段决定了。 如果数组中存在了数字key，则会默认使用最新使用的key + 1，例如上例中已经存在了10作为key的元素， 这样新插入的默认索引就为11了。
</p>
#####数据容器：槽位
***
<p>
	下面看看保存哈希表数据的槽位数据结构：
</p>
	typedef struct bucket {
		ulong h;					// 对char *key进行hash后的值，或者是用户指定的数字索引值
		uint nKeyLength;			// hash关键字的长度，如果数组索引为数字，此值为0
		void *pData;				// 指向value，一般是用户数据的副本，如果是指针数据，则指向pDataPtr
		void *pDataPtr;				// 如果是指针数据，此值会指向真正的value,同时上面pData会指向此值
		struct bucket *pListNext;	// 整个hash表的下一个元素
		struct bucket *pListLast;	// 整个哈希表该元素的上一个元素
		struct bucket *pNext;		// 存放在同一个hash Bucket内的下一个元素
		struct bucket *pLast;		// 同一个哈希bucket的上一个元素
		// 保存当前值所对于的key字符串，这个字段只能定义在最后，实现变长结构体
		char arKey[1];		
	} Bucket;
<p>
	如上面各字段的注释。h字段保存哈希表key哈希后的值。这里保存的哈希值而不是在哈希表中的索引值，这是因为索引和哈希表的容量有直接关系，如果哈希表扩容了，那么这些索引还得重新进行哈希在进行索引映射，这也是一种优化手段。在PHP中可以使用字符串或者数字作为数组的索引。数字索引直接可以作为哈希表的索引，数字也无需进行哈希处理。h字段后面的nKeyLength字段是作为key长度的标示，如果索引是数字的话，则nKeyLength为0.在PHP数组中如果索引字符串可以被转换成数字也会被转换成数字索引。所以在PHP中例如'10','11'这类的字符索引和数字索引10,11没区别。
</p>
<p>
	上面结构体的最后一个字段用来保存key的字符串，而这个字段却申明为只有一个字符的数组， 其实这里是一种长见的变长结构体，主要的目的是增加灵活性。 以下为哈希表插入新元素时申请空间的代码
</p>
	p = (Bucket *) pemalloc(sizeof(Bucket) - 1 + nKeyLength, ht->persistent);
	if (!p) {
	    return FAILURE;
	}
	memcpy(p->arKey, arKey, nKeyLength);
<p>
	如代码，申请的空间大小加上了字符串key的长度，然后把key拷贝到新申请的空间里。 在后面比如需要进行hash查找的时候就需要对比key这样就可以通过对比p->arKey和查找的key是否一样来进行数据的 查找。申请空间的大小-1是因为结构体内本身的那个字节还是可以使用的。
</p>
<div class="book-img" style="text-align: center;">
<img src="/img/03-01-02-zend_hashtable1.png" alt="Zend引擎哈希表结构和关系">
<div class="book-img-desc">Zend引擎哈希表结构和关系</div>
</div>
<p>
	<ul>
		<li>Bucket结构体维护了两个双向链表，pNext和pLast指针分别指向槽位所在的链表的关系</li>
		<li>而pListNext和pListLast指针指向的则是整个哈希表所有的数据之间的链接关系。HashTable结构体中的pListHead和pListTail则维护整个哈希表的头元素指针和最后一个元素的指针。</li>
	</ul>
</p>
<p>
	PHP中数组的操作函数非常多，例如：array_shift()和array_pop()函数，分别从数组的头部和尾部弹出元素。哈希表中保存了头部和尾部指针，这样在执行这些操作时就能在常数时间内找到目标。PHP中还有一些使用的相对不那么多的数组操作函数：next(),prev()等的循环中,哈希表的另外一个指针就能发挥作用了：pInternalPointer，这个用于保存当前哈希表内部的指针。 这在循环时就非常有用。
</p>
<p>
	如图中左下角的假设，假设依次插入了Bucket1，Bucket2，Bucket3三个元素：
	<p>
		1、插入Bucket1时，哈希表为空，经过哈希后定位到索引为1的槽位。此时的1槽位只有一个元素Bucket1.其中Bucket1的pData或者pDataPtr指向的是Bucket1所存储的数据。此时由于没有链接关系。pNext,pLast,pListNext,pListLast指针均为空。同时在HashTable结构体中也保存了整个哈希表的第一个元素指针，和最后一个元素指针，此时HashTable的pListHead和pListTail指针均指向Bucket1.
	</p>
	<p>
		2、插入Bucket2时，由于Bucket2的key和Bucket1的key出现冲突，此时将Bucket2放在双链表的前面。由于Bucket2后插入并置于链表的前端，此时Bucket2.pNext指向Bucket1，由于Bucket2后插入。
		Bucket1.pListNext指向Bucket2，这时Bucket2就是哈希表的最后一个元素，这是HashTable.pListTail指向Bucket2.
	</p>
	<p>
		3、插入Bucket3，该key没有哈希到槽位1，这时Bucket2.pListNext指向Bucket3，因此Bucket3后插入。同时HashTable.pListTail改为指向Bucket3。
	</p>
	简单来说就是哈希表的Bucket结构维护了哈希表中插入元素的先后顺序，哈希表结构维护了整个哈希表的头和尾。在操作哈希表的过程中始终保持预算之间的关系。
</p>
####哈希表的操作接口
***
<p>
	将简单介绍PHP哈希表的操作接口实现。提供了如下几类操作接口：
	<ul>
		<li>初始化操作，例如zend_hash_init()函数，用于初始化哈希表接口，分配空间等</li>
		<li>查找，插入，删除和更新接口，这是比较常规的操作</li>
		<li>迭代和循环，这类的接口用于循环对哈希表进行操作</li>
		<li>复杂，排序，倒置和销毁等操作</li>
	</ul>
</p>
<p>
	本小节选取其中的插入操作进行介绍。在PHP中不管是对数组的添加操作(zend_hash_add)，还是对数组的更新操作(zend_hash_update)，其最终都是调用_zend_hash_add_or_update函数完成，这在面向对象编程中相当于两个公有方法和一个公共的私有方法的结构，以实现一定程度上的代码复用。
</p>
	ZEND_API int _zend_hash_add_or_update(HashTable *ht, const char *arKey, uint nKeyLength, void *pData, uint nDataSize, void **pDest, int flag ZEND_FILE_LINE_DC)
	{
	     //...省略变量初始化和nKeyLength <=0 的异常处理
	 
	    h = zend_inline_hash_func(arKey, nKeyLength);
	    nIndex = h & ht->nTableMask;
	 
	    p = ht->arBuckets[nIndex];
	    while (p != NULL) {
	        if ((p->h == h) && (p->nKeyLength == nKeyLength)) {
	            if (!memcmp(p->arKey, arKey, nKeyLength)) { //  更新操作
	                if (flag & HASH_ADD) {
	                    return FAILURE;
	                }
	                HANDLE_BLOCK_INTERRUPTIONS();
	 
	                //..省略debug输出
	                if (ht->pDestructor) {
	                    ht->pDestructor(p->pData);
	                }
	                UPDATE_DATA(ht, p, pData, nDataSize);
	                if (pDest) {
	                    *pDest = p->pData;
	                }
	                HANDLE_UNBLOCK_INTERRUPTIONS();
	                return SUCCESS;
	            }
	        }
	        p = p->pNext;
	    }
	 
	    p = (Bucket *) pemalloc(sizeof(Bucket) - 1 + nKeyLength, ht->persistent);
	    if (!p) {
	        return FAILURE;
	    }
	    memcpy(p->arKey, arKey, nKeyLength);
	    p->nKeyLength = nKeyLength;
	    INIT_DATA(ht, p, pData, nDataSize);
	    p->h = h;
	    CONNECT_TO_BUCKET_DLLIST(p, ht->arBuckets[nIndex]); //Bucket双向链表操作
	    if (pDest) {
	        *pDest = p->pData;
	    }
	 
	    HANDLE_BLOCK_INTERRUPTIONS();
	    CONNECT_TO_GLOBAL_DLLIST(p, ht);    // 将新的Bucket元素添加到数组的链接表的最后面
	    ht->arBuckets[nIndex] = p;
	    HANDLE_UNBLOCK_INTERRUPTIONS();
	 
	    ht->nNumOfElements++;
	    ZEND_HASH_IF_FULL_DO_RESIZE(ht);        /*  如果此时数组的容量满了，则对其进行扩容。*/
	    return SUCCESS;
	}
<p>
	整个写入或更新的操作流程如下：
	<p>
		1、生成hash值，通过与nTableMask执行与操作，获取在arBuckets数组中的Bucket。
	</p>
	<p>	
		2、如果Bucket中已经存在元素，则遍历整个Bucket，查找是否存在相同的key值元素，如果有并且是update调用，则执行update数据操作。
	</p>
	<p>
		3、创建新的Bucket元素，初始化数据，并将新元素添加到当前hash值对应的Bucket链表的最前面（CONNECT_TO_BUCKET_DLLIST）。
	</p>
	<p>
		4、将新的Bucket元素添加到数组的链接表的最后面（CONNECT_TO_GLOBAL_DLLIST）。
	</p>
	<p>
		5、将元素个数加1，如果此时数组的容量满了，则对其进行扩容。这里的判断是依据nNumOfElements和nTableSize的大小。 如果nNumOfElements > nTableSize则会调用zend_hash_do_resize以2X的方式扩容（nTableSize << 1）。
	</p>
</p>
####链表简介
<p>
	   
</p>