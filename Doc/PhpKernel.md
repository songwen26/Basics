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
