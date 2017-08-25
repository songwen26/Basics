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
####生命周期和Zend引擎
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
####单进程SAPI生命周期
***
<p>
	CLI/CGI模式的PHP属于单进程的SAPI模式。这类的请求在处理一次请求后就关闭。也就是只会经过如下几个环节：开始-》请求开始-》请求关闭-》结束 SAPI接口实现就完成了其生命周期。如图2.1所示：
</p>
<div class="book-img" style="text-align: center;">
<img src="/img/02-01-01-cgi-lift-cycle.png" alt="图2.1 单进程SAPI生命周期">
<div class="book-img-desc">图2.1 单进程SAPI生命周期</div>
</div>
