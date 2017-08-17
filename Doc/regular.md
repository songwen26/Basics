<style type="text/css">
code {
    background-color: #f5f5f5;
    border: 1px solid #ccc;
    border-radius: 3px;
    display: inline-block;
    font-family: "Courier New",sans-serif;
    font-size: 12px;
    line-height: 1.8;
    margin: 1px 5px;
    padding: 0 5px;
    vertical-align: middle;
}
</style>
#正则环视
##一、环视的概念
环视，在不同的地方又称之为零宽断言，简称断言。<br/>
环视强调的是它所在的位置，前面或者后面，必须满足环视表达式中的匹配情况，才能匹配成功。<br/>
环视可以认为是虚拟加入到它所在位置的附加判断条件，并不消耗正则的匹配字符<br/>
###（一）环视概念与匹配过程示例
####示例一：简单环视匹配过程
例如，对于源字符串<code>ABC</code>，正则<code>(?=A)[A-Z]</code>匹配的是：<br/>
1.<code>(?=A)</code>所在的位置，后面是<code>A</code><br/>
2.表达式<code>[A-Z]</code>匹配<code>A-Z</code>中任意一个字母<br/>
根据两个的先后位置关系，组合在一起，那就是：<br/>
<code>(?=A)</code>所在的位置，后面是<code>A</code>，而且是<code>A-Z</code>中任意一个字母，因此，上面正则表达式匹配一个大写字母<code>A</code>。<br/>
从例子可以看出来，从左到右，正则分别匹配了环视<code>(?=A)</code>和<code>[A-Z]</code>，由于环视不消耗正则的匹配字符，因此，<code>[A-z]</code>还能对<code>A</code>进行匹配，并得到结果<br/>
###（二）什么是消耗正则的匹配字符？
####示例二：一次匹配消耗匹配字符匹配过程
例如，对于源字符串<code>ABCD</code>，正则<code>A[A-Z]</code>匹配的过程是：<br/>
<p>
	1.正则<code>A</code>：因为没有位置限定，因此是从源字符串开始位置开始，也就是正则里的<code>^</code>，这个<code>^</code>是虚拟字符，表示匹配字符串开始位置，也就是源字符串<code>ABCD</code>里的<code>A</code>前面的位置，因为正则<code>A</code>能够匹配源字符串<code>A</code>，匹配成功，匹配位置从源字符串<code>^</code>的位置后移一位，到达<code>A</code>后面，即此时源字符串<code>ABCD</code>的<code>A</code>这个字符已经被消耗，接下来的正则匹配从<code>A</code>后面开始。
</p>
<p>
	2.正则<code>[A-Z]</code>：当前匹配位置为第一个<code>A</code>字母后面位置，正则<code>[A-Z]</code>对源字符串<code>ABCD</code>里的<code>B</code>字母进行匹配，匹配成功，位置后移到<code>B</code>字母后面的位置。至此，由于正则已经匹配完成，因此，正则<code>A[A-Z]</code>匹配结果是<code>AB</code>。<br/>
js支持g模式修饰符，也就是全局匹配，那么上面例子中，正则匹配1次成功之后，将会从匹配成功位置（<code>B</code>字母后面位置）开始，再从头进行匹配一次正则，直到源字符串全部消耗完为止。
</p>
####示例三：多次匹配消耗匹配字符串匹配过程
<p>
	因此，全局匹配的过程补充如下：<br/>
	1.正则<code>A</code>：当前匹配位置为<code>B</code>字母后面位置，正则<code>A</code>去匹配源字符串中的<code>C</code>，匹配失败，匹配位置后移一位，此时<code>C</code>被消耗。<br>
	2.正则<code>A</code>：当前匹配位置为<code>C</code>字母后面位置，正则<code>A</code>去匹配源字符串中的第二个<code>A</code>字母，匹配成功，匹配位置后移一位，此时<code>A</code>被消耗了。<br/>
	3.正则<code>[A-Z]</code>：当前匹配位置为第二好<code>A</code>字母后面位置，正则<code>[A-Z]</code>对源字符串<code>ABCAD</code>里的<code>D</code>字母进行匹配，匹配成功，位置后移到<code>D</code>字母后面的位置，此时<code>D</code>被消耗了。<br/>
	4.由于正则里还有个源字符串结束位置，也就是正则里的<code>$</code>，这个<code>$</code>也是虚拟字符，因此，还要继续进行匹配：正则<code>A</code>：当前匹配位置为<code>D</code>字母后面的位置，正则<code>A</code>去匹配源字符串的结束位置，匹配失败，匹配结束。
</p>
##二、环视类型
环视的类型有两类：<br/>
###（一）肯定和否定
<p>
	1.肯定：<code>(?=exp)</code>和<code>(?<=exp)</code><br/>
	2.否定：<code>(?!exp)</code>和<code>(?&lt!exp)</code>
</p>
###（二）顺序和逆序
<p>
	1.顺序：<code>(?=exp)</code>和<code>(?<=exp)</code><br>
	2.逆序：<code>(?!exp)</code>和<code>(?&lt!exp)</code>
</p>
####两种类型名称组合
<ul>
	<li>
		1、肯定顺序：<code>(?=exp)</code>
	</li>
	<li>
		2、否定顺序：<code>(?!exp)</code>
	</li>
	<li>
		3、肯定逆序：<code>(?<=exp)</code>
	</li>
	<li>
		4、否定逆序：<code>(?&lt!exp)</code>
	</li>
</ul>
####四种组合用法
四种组合，根据正则于环视位置的不同，又可以组合出8种不同的摆放方式。<br/>
一般来说，顺序的环视，放在正则后面，认为是常规用法，而放在正则前面，对正则本身的匹配起到了限制，则认为是变种的用法。<br/>
而逆序的环视，常规用法是环视放在正则前面，变种用法放在正则后面。
<p>总结：</p>
<p>
常规用法，环视不对正则本身做限制。<br/>
但是，无论常规和变种都是非常常见的用法。
</p>
#####四种组合正则与环视的摆放位置
	1、肯定顺序常规：[a-z]+(?=;)			字母序列后面跟着；
	2、肯定顺序变种：(?=[a-z]+&).+$		字母序列
	3、肯定逆序常规：(?<=:)[0-9]+			:后面的数字
	4、肯定逆序变种：\b[0-9]\b(?<=[13579]) 	0-9中的奇数
	5、否定顺序常规：[a-z]+\b(?!;)		不以；结尾的字母排序
	6、否定顺序变种：(?!.*?[lo0])\b[a-z0-9]+\b	不包含1/o/0的字母数字系列
	7、否定逆序常规：(?<!age)=([0-9]+)	参数名不为age的数据
	8、否定逆序变种：\b[a-z]+(?<!z)\b		不以z结尾的单词
