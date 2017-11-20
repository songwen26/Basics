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
</p>


	