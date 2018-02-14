##PHP基础知识点
###OOP基础
<p>
OOP的3个基础概念：封装、集成、多态
</p>
<p>
	<ul>
		类的属性：
		<li>public 公有的	（公有的）</li>
		<li>private 私有的 （不能被子类直接使用）</li>
		<li>protected 保护属性 （在继承的子类中可以访问和操作）</li>
		<li>fianl 作用域时，将阻止子类中覆盖</li>
	</ul>
</p>
<p>
	属性重载：可以进一步保护属性。它强制通过公共方法访问和操作属性，同时还允许像访问公共属性一样访问。这些方法称为访问方法（assessor）和修饰方法（mutator）。或非正式称为获取方法（gertter）和设置方法（setter）包括将会分别在访问或操作属性时自动触发。（PHP没有）
</p>
<p>
	通过__set, __get 方法使用
</p>
<p>
	类里常量：用const NAME = "VALUE";	
</p>
<p>
	方法作用域：public、private、protected、abstract、final、static
</p>
<p>
	<p>__construct 构造函数</p>
	<p>__destruct 构造函数</p>
</p>
<p>
	instanceof 接口 可能要重复地调用某个函数，但希望根据给定的对象类型调整函数的行为。可以使用instanceof
</p>
###辅助函数
<p>
	创建类别名：class_alias (创建一个类别名，允许用多个名来引用一个类)
	<p>
		bool class_alias (string $original, string $alias [, bool $autoload = TRUE])
	</p>
</p>
<p>
	确定类是否存在： class_exists (存在返回TRUE，不存在FALSE)
	<p>
		bool class_exists (string $class_name)
	</p>
</p>
<p>
	确定对象上下文：get_class_ 返回object所属类的类名
</p>
<p>
	了解类的方法：get_class_methods 返回array包含类中定义方法名
</p>
<p>
	了解类属性：get_class_vars 关联数组包含定义属性及值
</p>
<p>
	了解声明类：get_declared_classes 返回数组
</p>
<p>
	了解对象属性：get_object_vars 返回关联array 定义属性及值
</p>
<p>
	了解父类： get_parent_class 返回对象或类的父类名称 
</p>
<p>
	确定接口是否存在： interface_exists
</p>
###自动加载对象
<p>
	__autoload函数：只要一次调用一个类时，就会调用此函数
</p>
<p>
	PHP不支持高级OOP：以下均不支持
	<p>
		方法重载、操作符重载、多重继承
	</p>
</p>
<p>
	对象克隆： clone 复制对象（包括对象本身的属性）
</p>
<p>
	继承
	<p>
		类继承：通过extends关键字实现
	</p>
	<p>
		继承与延迟静态绑定： self:: -> 自己调用自己属性。相当于$this
	</p>
</p>
<p>
	接口：定义了实现某种服务的一般规范，声明了必要函数和常量但不指定如何实现。（PS：接口中不定义成员！类成员的定义完全交给类来完成）
</p>
<p>
	抽象类：是不能被实例化得类，只能作为其他类继续的基类。
</p>
<p>
	抽象类还是接口：
	<p>
		1、如果要创建一个模型，这个模型将由一些紧密相关的对象采用，就可以使用抽象类。如果要创建一个些不相关对象采用的功能，就使用接口。
	</p>
	<p>
		2、如果必须从多个来源继承行为，就使用接口。
	</p>
	<p>
		3、如果知道所有类都公共一个公共的行为实现，就使用抽象类，并在其中实现该行为。在接口中无法实现行为。
	</p>
</p>
<p>
	命名空间：
	<p>
		当中的类名重复了需要使用命名空间		
	</p>
	<P>
		<p>
			例如： namespace com\wjil\Libray;
		</p>		
		<p>
			namespace com\Thir\Datacleaner;
		</p>		
		加载两个文件：	   
		<p>
			require Libray.php;			
		</p>
		<p>
			require Datacleaner.php
		</p>
		<p>
			use com\wjil\Libray as WJG;			
		</p>
		<p>
			use com\Thir\Libray as TP;
		</p>
		实例化同个类
		<p>
			$a = new WJG\clean();
		</p>
		<p>
			$b = new TP\clean();
		</p>
	</P>
</p>
<p>
	错误和异常处理
	<p>
		1、配置指令：
		<p>
			error_reporting = E_ALL & E_STRICT
		</p>	
		<p>
			error_get_last 函数返回关联数组，包含最后出现的错误
		</p>
		<p>
			error_log 函数一般用法 error_log("信息",3,"文件地址")
		</p>
		<p>
			标识日志文件，在Linux错误发送到syslog			
		</p>
		<p>
			启用ignore_repeated_errors指令将使PHP忽略同一文件中同一行上发生的重复的错误。
		</p>
		<p>
			初始化PHP的日志工具： define_syslog_variables()
		</p>
		<p>
			打开日志连接：openlog()
		</p>
		<P>
			向日志发送消息 syslog()
		</P>
	</p>
	<p>
		2、异常处理，4个步骤：try/catch
		<ul>
			<li>应用程序尝试做一些操作</li>
			<li>如果尝试失败，则异常处理特性抛出一个异常</li>
			<li>指定的处理器捕获异常，完成必要的任务</li>
			<li>异常处理特性清除在尝试期间占用的资源</li>
		</ul>
	</p>	
</p>
<p>
	PHP的异常处理实现
	<p>
		<ul>
			1、扩展基本异常类
			<li>throw new Exception();</li>
			<li>message解释， error code 用于保存错误标识符， previous 异常的异常</li>
			2、扩展异常类
			<li>自己写类去继承 Exception</li>
			3、捕获多个异常
			<li>SOL异常：标准PHP库。 LogicExcetion, RuntimeException</li>
		</ul>
	</p>
</p>
###正则
<p>
	^ 起始位置 $ 终止位置
</p>
<p>
	PHP的正则表达式函数
	<ul>
		1、以区分大小写的方式搜索
		<li>bool ereg() 函数根据定义的模式以区分大小写的方式搜索</li>
		2、以不区分大小写的方式搜索
		<li>eregi() 搜索一个匹配预定义模式的字符串时不区分大小写</li>、
		3、以区分大小写的方式替换文本
		<li>ereg_replace(string $pattern, string $replacement, string $string)</li>
		4、以不区分大小写的形式替换文本
		<li>ereg_replace(string $pattern, string $replacement, string $string)</li>
		5、以区分大小写的方式将字符串划分为不同元素
		<li>array split(string $pattern, string $string[, int $limit])</li>
		6、以不区分大小的方式将字符串划分为不同元素
		<li>spliti(string $pattern, string $string[, int $limit = -1])</li>
		7、调节只支持区分大小写的正则表达式
		<li>sql_regcase(string $string)</li>		
	</ul>	
</p>
<p>
	正则表达式语法（perl风格）
	<p>
		<p>
			1、修饰符：
			<ul>
				<li>i 完全不区分大小写</li>
				<li>g 查找所有出现（完全全面搜索）</li>
				<li>s 忽略所有换行符 （m 与 s相反）</li>
				<li>x "贪婪" 一直匹配 （忽略注释和空白）</li>
				<li>u 不贪婪</li>
			</ul>
		</p>
		<p>
			2、元字符： \A  \b  \B  \d  \D  \s  \S  []  () $
		</p>
		<p>
			3、PHP的正则表达式函数 （perl风格）
		</p>
	</p>
</p>
###文件处理，操作系统
<p>
	1、获取路径的文件
	<p>basename()</p>	
</p>
<p>
	2、获取路径的目录
	<p>dirname()</p>
</p>
<p>
	3、pathinfo()函数创建一个关联数组
</p>
<p>
	4、确定绝对路径
	<p>
		realpath()
	</p>
</p>
<p>
	确定文件的大小： int filesize(string $filename)
</p>
<p>
	计算磁盘的可以空间：disk_free_space()
</p>
<p>
	确定文件的最后访问时间：fileatime()
</p>
<p>
	文件的最后改变时间： filectime()
</p>
<p>
	文件的最后修改时间： fielmtime()
</p>
<p>
	打开文件： fopen(string $resource, string $mode [, bool $use_include_path = false [, resource $context ]]) R, rt, w, wt
</p>
<p>
	关闭文件：fclose()
</p>
<P>
将文件读入数组： file()
</P>
<p>
	将文件内容读入字符串变量：file_get_contents(string filename)
</p>
<p>
	将字符串变量写入到文件中：file_put_contents( string $filename , mixed $data [, int $flags = 0 [, resource $context ]]) flags:FILE_APPEND  	如果文件 filename 已经存在，追加数据而不是覆盖
</p>
<p>
	将CSV文件读入数组：fgetcsv()
</p>
<p>
	读取指定数目的字符：fgets(resource $handle [, int $length ])
</p>
<p>
	从输入中删除标签：fgetss( resource $handle [, int $length [, string $allowable_tags ]])
</p>
<p>
	一次读取一个字符的方式： fgetc()
</p>
<p>
	忽略换行符：fread()
</p>
<p>
	读取整个文件：readfile()
</p>
<p>
	根据预定义的格式读取文件：fscanf(资源, "%d-%s-")	
</p>
<p>
	将字符串写入文件：fwrite()
</p>
<p>
	将文件指针移到指定位置：fseek()
</p>
<p>
	获取当前指针的偏移量：ftell()
</p>
<p>
	将文件指针移回文件开始处：rewind()
</p>
<p>
	打开目录：opendir()
</p>
<p>
	关闭目录：closedir()
</p>
<p>
	解析目录内容：readdir()
</p>
<p>
	将目录读入数组：scandir()
</p>
<p>
	界定输入：escapeshellarg() 用单引号界定给的参数
</p>
###时间和日期
<p>
	date 函数：date("t") t 几天 	date("F") 月份 ，当月
</p>
<p>
	
</p>