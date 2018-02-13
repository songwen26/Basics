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
