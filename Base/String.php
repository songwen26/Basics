<?php
/*
 * 特定类型的测试函数
 */
is_scalar();    //检查该变量是否是标量，即，一个整数、布尔值、字符串或者浮点数
is_numeric();   //检查该变量是否是任何类型的数字或者数字字符串
is_callable();  //检查该变量是否有效的函数名称

/*
 *
 * 测试变量状态
 */
isset($var);      //判断变量是否存在，存在返回true 不存在返回false

empty($var);      //判断变量是否存在，null\0\'0'\false 返回true 存在变量返回false

//以C语言风格使用反斜线转义字符串的字符
addcslashes($str, $charlist);       //charlist : 列表中的字符前都加上了反斜线

//使用反斜线引用字符串
addslashes($str);       //字符是单引号（'）、双引号（"）、反斜线（\）与 NUL（NULL 字符）

//函数把包含数据的二进制字符串转换为十六进制值
bin2hex($str);

//返回指定的字符
chr($ascii);        //返回字符（输入int）

/*
 *  body : 要分割的字符
 *  chunklen : 分割尺寸
 *  end : 行尾序列符合
 * */
//将字符串分割成小块
chunk_split($body, $chunklen, $end);

//解码一个uuencode编码的字符串
convert_uudecode($str);

//使用uuencode编码一个字符串
convert_uuencode($str);

/*
 *  mode : 参数
 *  0 - 以所有的每个字节值作为键名，出现次数作为值的数组
 *  1 - 与 0 相同，但只列出出现次数大于零的字节值
 *  2 - 与 0 相同，但只列出出现次数等于零的字节值
 *  3 - 返回由所有使用了的字节值组成的字符串
 *  4 - 返回由所有未使用的字节值组成的字符串
 * */
//返回字符串所用字符的信息
count_chars($str, $mode);

//计算一个字符串的crc32多项式
crc32($str);

//单向字符串散列
crypt($str, $salt);     //salt 参数是可选的。然而，如果没有salt的话，crypt()创建出来的会是弱密码。 php 5.6及之后的版本会在没有它的情况下抛出一个 E_NOTICE 级别的错误。为了更好的安全性，请确保指定一个足够强度的盐值。

//转换十六进制字符串为二进制字符串
hex2bin($data);

/*
 *  flags   参数
 *  ENT_COMPAT              会转换双引号，不转换单引号
 *  EXT_QUOTES              既转换双引号也转换单引号
 *  ENT_NOQUOTES            单/双引号都不转换
 *  ENT_IGNORE              静默丢弃无效的代码单元序列，而不是返回空字符串。 不建议使用此标记， 因为它» 可能有安全影响
 *  ENT_SUBSTITUTE          替换无效的代码单元序列为 Unicode 代替符（Replacement Character）， U+FFFD (UTF-8) 或者 &#xFFFD; (其他)，而不是返回空字符串
 *  ENT_DISALLOWED          为文档的无效代码点替换为 Unicode 代替符（Replacement Character）： U+FFFD (UTF-8)，或 &#xFFFD;（其他），而不是把它们留在原处。 比如以下情况下就很有用：要保证 XML 文档嵌入额外内容时格式合法
 *  ENT_HTML401             以 HTML 4.01 处理代码
 *  ENT_XML1                以 XML 1 处理代码
 *  ENT_XHTML               以 XHTML 处理代码
 *  ENT_HTML5               以 HTML 5 处理代码
 * */
//将字符转换为HTML转义字符
htmlentities($str, $flags);

//将特殊的HTML实体转换回普通字符
htmlspecialchars_decode($str, $flags);

//将特殊字符转换为HTML实体
htmlspecialchars($str);

//使一个字符串的第一个字符小写
lcfirst($str);

//在字符串所有新行之前插入HTML换行标记
nl2br($str);

//返回字符的ASSCOO码值
ord($str);

//转义元字符集
quotemeta($str);

//计算字符串的sha2散列值
sha1($str);

//根据指定格式解析输入的字符
sscanf($str, $format);      //format : 解析格式

//使用另一个字符串填充字符串为指定长度
str_pad($str, $pad_length, $pad_str);     // pad_length : 指定长度 , pad_str : 填充字符串

//重复一个字符串
str_repeat($str, $multiplier);      //multiplier : 重复次数

//字符串替换
str_replace($search, $replace, $subject);

//随机打乱一个字符串
str_shuffle($str);

//将字符串转换为数组
str_split($str, $split_length);         //split_length : 每一段的长度

/*
 *  format : 参数
 *  0 - 返回单词数量
 *  1 - 返回一个包含 string 中全部单词的数组
 *  2 - 返回关联数组。数组的键是单词在 string 中出现的数值位置，数组的值是这个单词
 * */
//返回字符串中单词的使用情况
str_word_count($str, $format);

//二进制安全比较字符串（不区分大小写）
strcasecmp($str1, $str2);

//二进制安全比较字符串
strcmp($str1, $str2);

//基于区域设置的字符串比较
strcoll($str1, $str2);

//获取不匹配遮罩的起始字符串的长度
strcspn($str1, $str2);

//从字符串中去除HTML 和 PHP 标记
strip_tags($str);

//查找字符串首次出现的位置（不区分大小写）
stripos($haystack, $needle);    //hanystack : 在该字符串中查找，  needle : 首次出现的数字位置

//反引用一个引用字符串
stripslashes($str);

//获取字符串长度
strlen($str);

//使用“自然顺序”算法比较字符串（不区分大小写）
strnatcasecmp($str1, $str2);
strnatcmp($str1, $str2);    //(区分大小写)

//二进制安全比较字符串开头的若干个字符（不区分大小写）
strcasecmp($str1, $str2);
strncmp($str1, $str2);

//在字符串中查找一组字符的任何一个字符
strbrk($haystack, $char_list);      //在haystack字符串中查找char_list中的字符

//查找字符首次出现的位置
strpos($haystack, $needle);     //返回needle在haystack中首次出现的数字位置

//查找指定字符在字符串中的最后一次出现
strrchr($haystack, $needle);    //返回haystack字符串中的一部分，这部分以needle的最后出现位置开始，直到haystack末尾

//反转字符串
strrev($str);

//计算指定字符串在目标字符串中最后一次出现的位置（不区分大小写）
strripos($haystack, $needle);   //以不区分大小写的方式查找指定字符串在目标字符串中最后一次出现的位置。
strrpos($haystack, $needle);    //(区分大小写)

//计算字符串中全部字符都存在于指定字符集合中的第一段子串的长度
strspn($subject, $mask);    //subject中全部字符仅存在于mask中的第一组连续字符（子字符串）的长度

//查找字符串首次出现
strstr($haystack, $needle);     //haystack字符串从needle第一次出现的位置开始到haystack结尾的字符串

//标记分割字符串
strtok($str, $token);       //strtok() 将字符串 str 分割为若干子字符串，每个子字符串以 token 中的字符分割。这也就意味着，如果有个字符串是 "This is an example string"，你可以使用空格字符将这句话分割成独立的单词。

//将字符串转化为小写
strtolower($str);

//将字符串转化为大写
strtoupper($str);

//转换指定字符
strtr($str, $from, $to);        //该函数返回 str 的一个副本，并将在 from 中指定的字符转换为 to 中相应的字符

//计算字串出现的次数
substr_cont($haystack, $needle);    //字符串needle 在字符串 haystack 中出现的次数

//替换字符串的子串
substr_replace($str, $replace, $start);     //输入字符串、替换字符串、起始位置、长度

//返回字符串的子串
substr($str, $start);

//去除字符串首尾处的空白字符（或者其他字符）
trim($str);

//将字符串的首字母转换为大写
ucfirst($str);

//将字符串中每个单词的首字母转换为大写
ucwords($str);

