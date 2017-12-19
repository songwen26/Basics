#PHP与MySQL程序设计
##第三章 PHP基础
<p>配置php.ini中的short_open_tag开启短标签</p>
<p>快速输出一些动态文本，使用短路语法的输出形式：</p>
	<?= 'This is another PHP example';?>
<p>
	PHP还支持一种主流的界定形式：
</p>
	<script language="php">
		print "This is another PHP example";
	<script>
<p>
	Microsoft ASP页面使用了一种类似的策略，使用预定义的字符模式将静态内容和动态内容分开：动态语法以<%开头,以%>结束。
</p>
	<%
		print "This is another PHP example";
	%>
<p>
	变量的变量
	<p>
		有时候，希望使用一个变量，它的内容本身可以动态的视为变量。
	</p>
	$recipe = "spaghetti";
	<p>
	有趣的是，接下来可以在原变量名前加上一个$,再为它赋另一个值，这就会将其值spaghetti作为一个变量：
	</p>
	$$recipe = "& meatballs";
	<p>
	其作用是把& meatballs赋给名为spaghetti的变量。
	</p>
</p>
####break语句和goto语句
<p>
	如果遇到一个break语句，将立即结束。跳出循环。
</p>
<p>
	通过添加goto语句，可以直接跳到一个循环或条件之外的某个特定位置。
</p>
####continue语句
<p>
	contnue语句使当前循环迭代执行结束，并从下一次迭代开始执行。
</p>
##第四章 函数
####按引用传递参数
<p>
	在函数内对参数所做的修改也可以体现在函数作用域外，按引用传递参数就可以满足这种需要。按引用传递参数(也称传引用)要在参数前加上&符号。
</p>
##第五章 数组
####用list()提取数组
	$str = 'Nino Sanzi|professional golger|green';
	list($name,$ocupation,$color) = explode("|",$str);
####在数组头添加元素 array_unshift(array array, mixed variable)
####在数组尾添加元素 array_push(array array, mixed variable)
####从数组头删除元素 array_shift(array array)
####从数组尾删除元素 array_pop(array array)
####搜索关联数组键 array_key_exists(mixed key, array array)
####搜索关联数组值 array_search(mixed needle, array haystack)\
####获取数组键 array_keys()
####获取数组值 array_values()
####获取当前数组键 key(array array)
####获取当前数组值 current(array array)
####获取当前数组键和值 each(array array)
####将指针移动到下一个数组位置 next(array array)
####将指针移动到前一个数组位置 prev(array array)
####将指针移到第一个数组位置 reset(array array)
####将指针移动到最后一个数组位置 end(array array)
####向函数传递数组值 array_walk(array &array, callback function)
####确定数组的大小 count(array array)
####统计数组元素出现的频率 array_count_values(array array)
####删除数组中重复的值 array_unique(array array)
####逆置数组元素顺序 array_reverse(array array)
####置换数组键和值 array_flip(array array)
####由低到高排序 sort(array array)
####保持键/值对的条件对数组排序 asort(array array)
####以逆序对数组排序 rsort(array array)
####保持键/值对的条件下以逆序对数组排序 arsort(array array)
####数组自然排序 natsort(array array)
####不区分大小写的自然排序 natcasesort(array array)
####按键值对数组排序 ksort(array array)
####以逆序对数组键排序 krsort(array array)
####根据用户自定义规则排序 usort(array array, callback function_name)
####合并数组 array_merge(array array1, array array2[,..])
####递归追加数组 array_merge_recursive(arrary array1, array array2[,..])
####合并两个数组 array_combine(array keys, array values)
####拆分数组 array_slice(array array, int offset,[,int lenght])
####接合数组 array_splice(array array, int offset,[,int lenght])
####求数组的交集 array_intersect(array array1, array array2)
####求关联数组的交集 array_intersect_assoc(array array1, array array2)
####求数组的差集 array_diff(array array1, array array2)
####求关联数组的差集 array_diff_assoc(array array1, array array2)
####返回一组随机的键 array_rand(array array[, int num_entries])
####随机洗牌数组元素 shuffle(array input_array)
####对数组中的值求和 array_sum(array array)
####划分数组 array_chunk(array array, int size)
##第六章 面向对象的PHP
###OOP的好处
<p>oop的3个基本概念：封装，继承，多态</p>
<p>
	属性重载：
	<p>
	属性重载可以进一步保护属性，它强制通过公共方法访问和操作属性，同时还允许像访问公共属性一样访问数据。这些方法称为访问方法(accesssor)和修改方法(mutator)，或非正式地称为获取方法(getter)和设置方法(setter),它们将分别在访问或操作属性时自动触发。
	</p>
</p>
<p>
	用__set()方法设置属性：
	<p>
	修改方法(mutator),或称为设置方法(setter),负责隐藏属性的赋值实现，并在为类属性赋值之前验证类数据。其形式为：
	</p>
	<p>boolean __set([string property_name],[mixed value_to_assign])</p>
	<p>它接受一个属性名和相应的值作为输入，如果方法成功执行就返回TRUE，否则返回FALSE。</p>
</p>
<p>
	用__get()方法获取属性：
	<p>
	访问方法(acessor)或获取方法(getter)负责封装获得类变量所需的代码。
	</p>
	<p>boolen __get([string property_name])</p>
	<p>
	它接受一个属性名作为输入参数，即要获取该属性的值。它在成功执行时返回TRUE，否则返回FALSE。
	</p>
</p>
##第七章 高级OOP特性
####实现多个接口
	<?php
		interface IEmployee {
			public function getIEmployee();
			public function getName();
		}
		interface IDeveloper {…}
		interface IPillage {…}

		class Employee implements IEmployee, IDeveloper, IPillage {
			……
		}

		class Contractor implements IEmployee, IDeveloper {
			……
		}
	?>
####抽象类
<p>
	抽象类是不能被实例化的类，只能作为由其他类继承的基类。
</p>
	什么时候应当用使用接口，什么时候该用抽象类？
	1、如果要创建一个模型，这个模型将由一些紧密相关的对象采用，就可以使用抽象类。如果要创建将由一些不相关对象采用的功能，就使用接口。
	2、如果必须从多个来源继承行为，就使用接口。PHP类可以继承多个接口，但不能扩展多个抽象类。
	3、如果知道所有类都会共享一个公共的行为实现，就使用抽象类，并在其中实现该行为。在接口中无法实现行为。
##第八章 错误和异常处理
###配置指令
<p>
1、设置你想要的错误敏感级别
	<p>
	error_reporting指令确定报告的敏感级别。共有16个不同的级别，这些级别的任何组合都是有效的。
	</p>
</p>
<p>
2、在浏览器上显示错误
	<p>
	启动display_errors时，将显示满足error_reporting所定义规则的所有错误。应当只在测试期间启用此指令，并在网站投入使用时将其禁用。
	</p>
</p>
<p>
3、显示启动错误
	<p>
	启用display_startup_errors指令会显示PHP引擎初始化时遇到的所有错误。与display_errors类似，应当在测试时启用此指令，并在网站投入使用时将其禁用。
	</p>
</p>
<p>
4、记录错误
	<p>
	错误应当记录在每个实例中，因为这些记录能为确定应用程序和PHP引擎特定的问题提供最有价值的信息。因此应当始终启用log_errors。这些日子语句记录的具体位置取决于error_log指令设置。
	</p>
</p>
<p>
5、标识日志文件
	<p>
	错误可以发送给系统日志后台程序，或者送往由管理员通过error_log指令指定的文件。如果此指令设置为日志后台程序，在Linux上错误语句将发往syslog，而在Windows上错误将被发送到事件日志。
	</p>
</p>
<p>
6、设置日志行的最大长度
	<p>
	log_errors_max_len指令设置每个日志项最大长度(以字节为单位)。默认值为1024字节。将此指令设置为0标识不指定最大长度。
	</p>
</p>
<p>
7、忽略重复的错误
	<p>
	启用ignore_repeated_errors指令将使PHP忽略同一文件中或同一行上发生的重复的错误信息。
	</p>
</p>
<p>
8、忽略相同位置发生的错误
	<p>
	启用ignore_repeated_source指令将使PHP忽略不同文件中或同一文件中不同行上发生的重复的错误消息。
	</p>
</p>
<p>
9、在变量中存储最近发生的错误
	<p>
	启用track_errors指令会使PHP在变量$php_errormsg中存储最近发生的错误消息。一旦注册，就可以随心所欲地使用此变量数据，例如输出、存储到数据库或其他可以对变量做的事情。
	</p>
</p>
###错误日志
<p>
	如果要在单独的文本文件中记录错误日志，那么web服务器进程所有者必须有足够的权限来写这个文件。此外，要确保将这个文件存放在文档根之外，以减少遭到攻击的可能性，避免攻击者因碰巧发现这个文件而看到一些对暗中进入服务器有用的信息。
</p>
<p>
1、初始化PHP的日志工具
	<p>
	define_syslog_variables()函数初始化一些常量，这些常量是使用openlog()、closelog()和syslog()函数时所必需的。其形式为：
	</p>	
	<p>
		void define_syslog_variables(void)
	</p>
</p>
<p>
2、打开日志链接
	<p>
	openlog()函数打开一个与所在平台上系统日志器的连接，通过指定几个将在日志上下文使用的参数，为向系统日志插入消息做好准备：
	</p>
	<p>
		int openlog(string ident, int option, int facility)
	</p>
	<p>
	ident,增加到每一项开始处的消息标识符。通常将这个值设置为程序名。
	</p>
	<p>
	option,确定生成消息时使用哪些日志选项。
	</p>
	<p>
	facility,有助于确定记录消息日志的程序属于哪一类。
	</p>
</p>
<p>
	3、关闭日志连接
	<p>
	syslog()函数负责向syslog发生一条定制消息。其形式为:
	</p>	
	<p>
		int syslog(int priority, string message)
	</p>
</p>
###异常处理
<p>
	异常处理4个步骤：
	<ul>
		<li>(1)应该程序尝试做一些操作</li>
		<li>(2)如果尝试失败，则异常处理特性抛出一个异常</li>
		<li>(3)指定的处理器捕获该异常，完成必要的任务</li>
		<li>(4)异常处理特性清除在尝试期间占用的资源</li>
	</ul>
</p>
####PHP的异常处理实现
<p>
	1.扩展基本异常类
	<p>
	默认构造函数：默认的异常构造函数不带参数。throw new Exception();
	</p>
	<p>
	重载构造函数：重载构造函数可以接受3个可选参数，由此提供默认构造函数所没有的其他功能。
		<p>
		message。作为一个对用户友好的解释，可以通过getMessage()方法传递给用户。	
		</p>
		<p>
		error code。 用于保存错误标识符，可以映射到某个标识符一消息。错误代码通常用于国际化和本地化。这个错误代码可以通过getCode()方法得到。
		</p>
		<p>
		previous。这个可选参数可以用来传入导致抛出当前异常的异常，这个特性称为异常串链，也称为异常嵌套。利用这个有用的选项，可以很容易地创建轨迹，利用这些轨迹能够诊断代码中出现的复杂问题。
		</p>
	</p>
	<p>
	方法：
	<li>getCode().返回传递构造函数的错误代码。</li>
	<li>getFile().返回抛出异常的文件名</li>
	<li>getLine().返回抛出异常的行号</li>
	<li>getMessage().返回传递给构造函数的消息</li>
	<li>getPrevious.这个方法会返回前一个异常</li>
	<li>getTrace().返回一个数组，其中包括出现错误的上下文的有关信息。</li>
	<li>getTraceAsString().返回与getTrace()完全相同的信息，只是返回的信息是一个字符串而不是数组</li>
	</p>
</p>
##第九章 字符串和正则表达式
<p>
	1、中括号：[]用来表述要匹配的一定范围的字符或字符列表。
</p>
<p>
	2、量词：表达字符出现的频率或位置寻找字符。
</p>
<p>
	3、预定义字符范围(字符类)
</p>
###Perl风格
<p>
	1、修饰符：
	<li>I 完成不区分大小写的搜索</li>
	<li>G 查找所有出现（完成全局搜索）</li>
	<li>M 将一个字符串视为多行（m就表示multiple）。默认情况下，^和$字符匹配字符串的最开始和最末尾。使用m修饰符使^和$匹配字符串中每行的开始</li>
	<li>S 将一个字符串视为一行，忽略其中的所有换行符；它与m修饰符正好作用相反。</li>
	<li>X 忽略正则表达式中的空白和注释</li>
	<li>U 第一次匹配后停止。</li>
</p>	
<p>
	2、元字符
	<li>\A 只匹配字符串开头</li>
	<li>\b 匹配单词边界</li>
	<li>\B 匹配除单词边界之外的任意字符</li>
	<li>\d 匹配数字字符。它与[0-9]相同</li>
	<li>\D 匹配非数字字符</li>
	<li>\s 匹配空白字符</li>
	<li>\S 匹配非空白字符</li>
	<li>[] 包围一个字符类</li>
	<li>() 包围一个字符分组或定义一个反引用</li>
	<li>$ 匹配行尾</li>
	<li>^ 匹配行首</li>
	<li>. 匹配除换行之外的任意字符</li>
	<li>\ 引出下一个元字符</li>
	<li>\w 匹配任何只包含下划线和字母数字字符的字符串。它与[a-zA-Z0-9_]相同</li>
	<li>\W 匹配字符串，忽略下划线和字母数字字符</li>
</p>
<p>
	3、PHP的正则表达式函数(Perl兼容)
	<p>
	搜索数组：preg_grep(string patten, array input[,int flags])
	</p>
	<p>
	搜索模式：函数根据搜索模式搜索字符串，如果存在返回TRUE，否则返回FALSE。preg_match(string patten, string string)
	</p>
	<p>
	匹配所有出现的模式：preg_match_all(string patten, string string, array matches)
	</p>
	<p>
	界定特殊的正则表达式字符：preg_quote(string str[, string delimiter])在每个对于正则表达式而言有特殊含义的字符前插入一个反斜线。
	</p>
	<p>
	替换匹配模式的所有字符串：preg_replace(minxed pattern, mixed replacement, mixed str[, int limit[, int count]])函数会用replacement的内容替换与pattern匹配的所有字符串，并返回修改后的结果。
	</p>	
	<p>
	以不区分大小写的方式将字符串划分为不同的元素：preg_split(string patten, string string[, int limit[, int flags]])函数与split()函数相同，只是pattern也可以按正则表达式定义。
	</p>
</p>
###其他字符串函数
####确定字符串长度：strlen(string str)
####比较两个字符串
<p>
	1、以区分大小写的方式比较两个字符串
	<p>
		int strcmp(string str1, string str2)
	</p>	
	<p>
		相等返回0，str1大于str2返回1，str1小于str2返回-1
	</p>
	2、以不区分大小写的方式比较两个字符串
	<p>
		int strcasecnp(strng str1, string str2)
	</p>
	3、求两个字符串相同的部分
	<p>
		int strspn(string str1, string str2[, int start[, int length]])
	</p>
	4、求两个字符串的不同部分
	<p>
		int strcspn(string str1, string str2)
	</p>
</p>
####处理字符串大小写
<p>
	1.将字符串全部转换成小写：strolower(string str)
</p>
<p>
	2.将字符串全部转换成大写：strtoupper(string str)
</p>
<p>
	3.将字符串的第一个字符大写：ucfirst(string str)
</p>
<p>
	4.将字符串中每个单词的首字母变为大写：ucwords(string str)
</p>
####字符串与HTML相互交换
<p>
	1.将换行符转换为HTML终止标签：nl2br(string str)
</p>
<p>
	2.将特殊字符转换为HTML等价形式：htmlentities(string str)
</p>
<p>
	3.将特殊的HTML字符用于其他目的：htmlspecialchars(string str)
</p>
<p>
	4.将文本转换为HTML等价形式：get_html_translation_table（）
</p>
<p>
	5.创建一个自定义的转换清单：strtr(string str, array replacements)
</p>
<p>
	6.将HTML转换为纯文本：strip_tags(string str)
</p>
####正则表达式函数的替代函数
<p>
	1.根据预定义的字节对字符串进行词法分析：strtok(string str, string tokens)
</p>
<p>
	2.根据预定义的定界符分解字符串：explode(string separator, string str)
</p>
<p>
	3.将数组转交成字符串：implode(string delimiter, array pieces)
</p>
<p>
	4.解析复杂的字符串(区分大小写，查找第一次出现的位置)：strpos(string str, string substr)
</p>
<p>
	5.找到字符串最后一次出现的位置：strrpos(string str, char substr)
</p>
<p>
	6.用另一个字符串替换字符串的所有实例：str_replace(string occurrence, mixed replacement, mixed str)
</p>
<p>
	7.获取字符串的一部分：strstr(string str, string occurrence)
</p>
<p>
	8.根据预定义的偏移返回字符串的一部分：substr(string str, int start[, int length])
</p>
<p>
	9.确定字符串出现的频率：substr_count(string str, string substring)
</p>
<p>
	10.用另一个字符串替换一个字符串的一部分：substr_replace(string str, string replacement, int start [,int length])
</p>
####填充和剔除字符串
<p>
	1.从字符串开始处裁剪字符：ltrim(string str [,string charlist])
</p>
<p>
	2.从字符串两端裁剪字符：trim(string str[, string charlist])
</p>
<p>
	3.从字符串末尾裁剪字符：rtrim(string str[, string charlist])
</p>
<p>
	4.填充字符串：str_pad(string str, int length[, string pad_string[, int pad_type]])
</p>
####统计字符和单词个数
<p>
	1.统计字符串个数：count_chars(string str[, int mode])
</p>
<p>
	2.统计字符串单词总数：str_word_count(string str[, int format])
</p>
####使用PEAR：Validate_US
<p>
	安装Validate_US
</p>
	pear install Validate_US-beta
<p>
	使用Validate_US。实例化类Validate_US()，调用适当的验证方法。
</p>
##第十章 处理文件和操作系统
###了解文件和目录
####解析目录路径
<p>
	1.获取路径的文件名：basename(string path[, string suffix])
</p>
<p>
	2.获取路径的目录:dirname(string path)
</p>
<p>
	3.了解更多关于路径的信息：pathinfo(string path)
</p>
<p>
	4.确定绝对路径：realpath(string path)
</p>
####计算文件、目录和磁盘大小
<p>
	1.确定文件的大小：filesize(string filename)
</p>
<p>
	2.计算磁盘的可以空间：disk_free_space(string directory)
</p>
<p>
	3.计算磁盘的总容量：disk_total_space(string directory)
</p>
<p>
	4.获取目录大小：
</p>
	function directorySize($directory){
	    $directorySize = 0;
	
	    //打开目录读取其内容
	    if ($dh = @opendir($directory)){
	        //迭代处理每个目录项
	        while (($filename = readdir($dh))) {
	            //过滤掉一些目录项
	            if ($filename != "." && $filename != "..") {
	                //文件，确定大小并添加到总大小
	                if (is_file($directory.'/'.$filename)) 
	                    $directorySize += filesize($directory.'/'.$filename)
	                //新目录，开始递归
	                if (is_dir($directory.'/'.$filename))
	                    $directorySize += directorySize($directory.'/'.$filename);
	            }
	        }
	    }
	    @closedir($dh);
	    return $directorySize;
	}
####确定访问和修改时间
<p>
	1.确定文件的最后访问时间：fileatime(string filename)
</p>
<p>
	2.确定文件的最后改变时间：filectime(string filename)
</p>
<p>
	3.确定文件的最后修改时间：filemtine(string filename)
</p>
###文件处理
####打开和关闭文件
<p>
	1.打开文件：fopen(string resource, string mode[, int use_include_path[, resource context]])
</p>
<p>
	2.关闭文件：fclose(resource filehandle)
</p>
####读取文件
<p>
	1.将文件读入数组：file(string filename[int use_include_path[, resource context]])
</p>
<p>
	2.将文件内容读入字符串变量：file_get_contents(string filename[, int use_include_path[, resource context[,int offset[, int maxlen]]]])
</p>
<p>
	3.将CSV文件读入数组：fgetcsv(resource handle[, int length[, string delimiter[, string enclosure]]])
</p>
<p>
	4.读取指定数目的字符：fgets(resource handle[,int length])
</p>
<p>
	5.从输入中剔除标签：fgetss(resource handle, int length[, string allowable_tags])
</p>
<p>
	6.以一次读取一个字符的方式读取文件：fgetc(resource handle)
</p>
<p>
	7.忽略换行符：fread(resource handle, int length)
</p>
<p>
	8.读取整个文件：readfile(string filename[, int use_include_path])
</p>
<p>
	9.根据预定义的格式读取文件：fscanf(resource handle, string format[, string varl])
</p>
####将字符串写入文件
<p>
	fwrite(resource handle, string string[, int length])
</p>
####移动文件指针
<p>
	1.将文件指针移到偏移量指定的位置：fseek(resource handle, int offset[, int whence])
</p>
<p>
	2.获取文件指针移回至文件开始处：rewind(resource handle)
</p>
<p>
	3.将文件指针移回至文件开始处：rewind(resource handle)
</p>
####读取目录内容
<p>
	1.打开目录句柄：fopen(string path[, resource context])
</p>
<p>
	2.关闭目录句柄：closedir(resource directory_handle)
</p>
<p>
	3.解析目录内容：readdir([resource dirextory_handle])
</p>
<p>
	4.将目录读入数组：scandir(string directory[, int sorting_order[, resource context]])
</p>
####执行shell命令
<p>
	1.删除目录：rmdir(string dirname)
</p>
<p>
	2.重命名文件：rename(string oldname, string newname)
</p>
<p>
	3.触摸文件(设置文件最后修改时间)：touch(string filename[, int time])
</p>
####系统及程序执行
<p>
	1.界定输入：escapeshellarg(string arguments)
</p>
<p>
	2.转义可能危险的输入：escapeshellcmd(string command)
</p>
####PHP的程序执行函数
<p>
	1.执行系统级命令：exec(string command [,array &output[, int &return_var]])
</p>
<p>
	2.获取系统命令的结果：system(string command[, int return_var])
</p>
<p>
	3.返回二进制输出：passthru(string command[, int &return_var])
</p>
<p>
	4.可代替反引号的函数：shell_exec(string command)
</p>
##第十二章 日期和时间
###PHP的日期和时间库
<p>
	1.验证日期：checkdate(int month, int day, int year)
</p>
<p>
	2.格式化日期和时间：date(string format[, int timestamp])
</p>
<p>
	3.了解当前时间的更多信息：gettimeofday([boolean return_float])
</p>
<p>
	4.将时间戳转换为用户友好的值：getdate([int timestamp])
</p>
<p>
	5.根据特定的日期和时间创建时间戳：mktime(int hour[, int minute[, int second[, int month[, int day[, int year]]]]])
</p>
####日期函数
<p>
	1.设置默认的本地化环境：setlocale(integer category, string locale)
</p>
<p>
	2.本地化日期和时间：strftime(string format[, int timestamp])
</p>
##第十六章 网络
###DNS、服务器和服务
####DNS
<p>
	1.检查DNS记录是否存在：checkdnsrr(string host[, string type])
</p>
<p>
	2.DNS资源记录：dns_get_record(string hostname[, int type[, array &authns, array &addtl]])
</p>
<p>
	3.获取MX记录：getmxrr(string hostname, array &mxhosts[, array &weight])
</p>
####服务
<p>
	1.获取服务器的端口号：getservbyname(string service, string protocol)
</p>
<p>
	2.获取端口号的服务名：getservbyport(int port, string protocol)
</p>
####建立套接字连接
<p>
	fsockopen(string target, int port[, int errno [, string errstring[, float timeout]]])
</p>
<p>
	fsockopen()函数在端口port上建立与target所表示资源的连接，在可以选参数errno和errstring中返回错误信息。可选参数timeout设置时间限值，以秒为单位，表示函数在失败前多长时间内会继续尝试建立连接。
</p>
	<?php
		//在端口80上与www.mall.com建立连接
		$http = fsockopen("www.mall.com");
		
		//给服务器发送一个请求
		$req = "GET / HTTP/1.1\r\n";
		$req .= "Host: www.mall.com\r\n";
		$req .= "Connection: Close\r\n\r\n";
		
		fputs($http, $req);
		
		//输出请求结果
		while (!feof($http)) {
		    echo fgets($http, 1024);
		}
		
		//关闭连接
		fclose($http);
	?>
<p>
	使用fsockopen()创建端口扫描器
</p>
	//给脚本足够的时间来完成任务
	ini_set("max_execution_time", 120);
	
	//定义扫描范围
	$rangStart = 0;
	$rangStop = 1024;
	
	//扫描哪个服务器？
	$target = "localhost";
	
	//创建端口值得一个数组
	$range = range($rangStart, $rangStop);
	
	echo "<p>Scan results for $target</p>";
	
	//执行扫描
	foreach ($range as $port){
	    $result = @fsockopen($target, $port, $errno, $errstr, 1);
	    if ($result)  echo "<p>Socket open at port $port</p>";
	}
####常见网络任务
<p>
	1.连接服务器
</p>
	<?php 

	    //ping 哪个服务器
	    $server = "www.mall.com";
	
	    //ping服务器多少次
	    $count = 3;
	
	    //执行任务
	    echo "<pre>";
	    system("/bin/ping -c $count $server");
	    echo "</pre>";
	
	    //杀死任务
	    system("killall -q ping");
	
	?>
<p>
	2.创建端口扫描器：
</p>
	<?php 

	    $target = "www.mall.com";
	    echo "<pre>";
	    system("/usr/bin/nmap $target");
	    echo "</pre>";
	
	    //杀死任务
	    system("killall -q nmap");
	
	?>
<p>
	3.创建子网转换器：
</p>
	<form>
	    <p>
	        IP Address:<br/>
	        <input type="text" name="ip[]" size="3" maxlength="3" value="">.
	        <input type="text" name="ip[]" size="3" maxlength="3" value="">.
	        <input type="text" name="ip[]" size="3" maxlength="3" value="">.
	        <input type="text" name="ip[]" size="3" maxlength="3" value="">
	    </p>
	
	    <p>
	        Subnet Mask:<br/>
	        <input type="text" name="sm[]" size="3" maxlength="3" value="">.
	        <input type="text" name="sm[]" size="3" maxlength="3" value="">.
	        <input type="text" name="sm[]" size="3" maxlength="3" value="">.
	        <input type="text" name="sm[]" size="3" maxlength="3" value="">
	    </p>
	    <input type="submit" name="submit" value="Calculate">
	</form>
	
	<?php 
	    if (isset($_POST['submit'])) {
	        //连接IP组成部分并转换为IPv4格式
	        $ip = implode('.', $_POST['ip']);
	        $ip = ip2long($ip);
	
	        //连接网络掩码组成部分并转换为IPv4格式
	        $netmask = implode('.', $_POST['sm']);
	        $netmask = ip2long($netmask);
	
	        //计算网络地址
	        $na = ($ip & $netmask);
	        //计算广播地址
	        $ba = $na | (~$netmask);
	
	        //重转换地址为点格式并显示
	        echo "Addressing Information :<br/>";
	        echo "<ul>";
	        echo "<li>IP Address:".long2ip($ip)."</li>";
	        echo "<li>Subnet Mask:".long2ip($netmask)."</li>";
	        echo "<li>Network Address:".long2ip($na)."</li>";
	        echo "<li>Broadcast Address:".long2ip($ba)."</li>";
	        echo "<li>Total Available Hosts:".($ba - $na - 1)."</li>";
	        echo "<li>Host Ranga:".long2ip($na + 1)." - ".long2ip($ba - 1)."</li>";
	        echo "</ul>";
	    }
	?>
<p>
	4.测试用户宽带：
</p>
	<?php 

	    //检索要发送用户的数组
	    $data = file_get_contents("textfile.txt");
	
	    //确定数据总大小，以千字节为单位
	    $fsize = filesize("textfile.txt") / 1024;
	
	    //确定起始时间
	    $start = time();
	
	    //发送数据给用户
	    echo "<!-- $data -->";
	
	    //确定终止时间
	    $stop = time();
	
	    //计算发送数据所耗时间
	    $duration = $stop - $start;
	    //用文件大小除以传输时间（以秒计）
	    $speed = round($fsize / $duration, 2);
	
	    //显示计算得出的速度（Kbit/s）
	    echo "Your network speed : $speed KB/sec.";
	
	?>











