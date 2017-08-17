<?php
//数组排序
$array = array(12,3,43,1,45,66);
//对数组排序，从最低到最高进行排序（数字、字母均可）
sort($array);
//反向排序
rsort($array);

//从低到高（保持索引关系）
asort($array);
//反向排序
arsort($array);

//按照数组键进行排序 从低到高
ksort($array);
//反向排序
krsort($array);

$array = array(
    array('TIR','tir',100),
    array('OIL','oil',24),
    array('SPK','spk',6)
);
//多维数组排序
//正序排列
function compare($x, $y){
    if ($x[2] == $y[2]){
        return 0;
    }else if ($x[2] < $y[2]){
        return -1;
    }else{
        return 1;
    }
}
//倒叙排列
function reverse_compare($x, $y){
    if ($x[1] == $y[1]){
        return 0;
    }else if ($x[1] < $y[1]){
        return 1;
    }else{
        return -1;
    }
}
usort($array, 'compare');

//对数组进行重新排序
//打乱数组
shuffle($array);

//返回单元顺序相反的数组
array_reverse($array);

//执行其他数组操作
each($array);       //返回数组中当前的键/值对并将数组指针向前移动一步
current($array);    //返回数组中的当前单元
reset($array);      //将数组的内部指针指向第一个单元
end($array);        //将数组的内部指针指向最后一个单元
next($array);       //将数组中的内部指针向前移动一位
pos($array);        //current 别名
prev($array);       //将数组的内部指针倒回一位

//使用用户自定义函数对数组中的每一个元素做回调处理
array_walk($array, 'callable');

//统计数组元素个数的函数
count($array);      //对数组中的元素个数进行统计
sizeof($array);     //count 别名
array_count_values($array);     //统计数组中所有的值

/*
 *  flags:
 *  EXTR_OVERWRITE      如果有冲突，覆盖已有的变量
 *  EXTR_SKIP           如果有冲突，不覆盖已有的变量
 *  EXTR_PREFIX_SAME    如果有冲突，在变量名前加上前缀prefix
 *  EXTR_PREFIX_ALL     给所有变量名加上前缀prefix
 *  EXTR_PREFIX_INVALID 仅在非法/数字的变量名前加上前缀 prefix
 *  EXTR_IF_EXISTS      仅在当前符号表中已有同名变量时，覆盖它们的值。其它的都不处理。 举个例子，以下情况非常有用：定义一些有效变量，然后从 $_REQUEST 中仅导入这些已定义的变量。
 *  EXTR_PREFIX_IF_EXISTS  仅在当前符号表中已有同名变量时，建立附加了前缀的变量名，其它的都不处理。
 *  EXTR_REFS           将变量作为引用提取。这有力地表明了导入的变量仍然引用了 array 参数的值。可以单独使用这个标志或者在 flags 中用 OR 与其它任何标志结合使用。
 */
//将数组转换成标量
extract($array, $flags);

//改变数组中所有键的情况
array_change_key_case($array, $case);

//将一个数组分割成多个
array_chunk($array, $size);     //size 个数

//返回数组中制定的一列
array_column($array, $column_key);

//创建一个数组，用一个数组的值作为其键名，另一个数组的值作为其值
array_combine($keys, $values);

//带索引检查计算数组的差值
array_diff_assoc($array1, $array2);

//使用键名比较计算数组的差集
array_diff_key($array1, $array2);

//用用户提供的回调函数做索引检查来计算数组的差集
array_diff_uassoc($array1, $array2, $callable);     //$callable 回调方法名

//用回调函数对键名比较计算数组的差集
array_diff_ukey($array1, $array2, $callable);

//计算数组的差集
array_diff($array1, $array2);

//使用指定的键和值填充数组
array_fill_keys($keys, $values);

//用给定的值填充数组
array_fill($start_index, $num, $values);

//用回调函数过滤数组中的单元
array_filter($array, $callbcak, $flag);     //callback: 回调方法    flag: callback接受的参数形式

//交换数组中的键跟值
array_flip($array);

//带索引检查计算数组的交集
array_intersect_assoc($array1, $array2);

//使用键名比较计算数组的交集
array_intersect_key($array1, $array2);

//计算数组的交集
array_intersect($array1, $array2);

//检查数组里是否有指定的键名或索引
array_key_exists($key, $array);

//返回数组中部分的或所有键名
array_keys($array);

//为数组的每个元素应用回调函数
array_map($callback, $array);

//递归合并一个或多个数组
array_merge_recursive($array1, $array2);

//合并一个或者多个数组
array_merge($array1, $array2);

/*
 *  array : 数组
 *  array_sort_order : 顺序   ps:SORT_ASC  上升顺序(默认)   SORT_DESC  下降顺序
 *  array_sort_flags :
 *      SORT_REGULAR - 将项目按照通常方法比较（不修改类型） PS:默认值
 *      SORT_NUMERIC - 按照数字大小比较
 *      SORT_STRING - 按照字符串比较
 *      SORT_LOCALE_STRING - 根据当前的本地化设置，按照字符串比较。 它会使用 locale 信息，可以通过 setlocale() 修改此信息。
 *      SORT_NATURAL - 以字符串的"自然排序"，类似 natsort()
 *      SORT_FLAG_CASE - 可以组合 (按位或 OR) SORT_STRING 或者 SORT_NATURAL 大小写不敏感的方式排序字符串。
 * */
//对多个数组或者多维数组进行排序
array_multisort($array1, $array1_sort_order, $array1_sort_flags);

//以指定长度将一个值填充进数组
array_pad($array, $size, $values);

//弹出数组最后一个单元（出栈）
array_pop($array);

//计算数组中所有值得乘积
array_product($array);      //如果$array为空的话，乘积为1

//将一个或多个单元压入数组的末尾（入栈）
array_push($array, $values);

//从一个数组中随机取出一个或者多个单元
array_rand($array, $num);

//用回调函数迭代将数组简化为单一的值
array_reduce($array, $callable);

//使用递归的数组递归替换第一个数组的元素
array_replace_recursive($array1, $array2);      //使用array2 替换 array1

//使用传递的数组替换第一个数组的元素
array_replace($array1, $array2);

//返回单元顺序相反的数组
array_reverse($array, $preserve_keys);      //preserve_keys true 会保留数字的键， flase 不保留键

//在数组中搜索给定的值，如果成功则返回首个相应的键名
array_search($needle, $array);      //needle 搜索的值

//将数组开头的单元移出数组
array_shift($array);

//从数组中取出一段
array_slice($array, $offset, $length);      //array 数组， offset 位置， length 个数

/*
 *  array : 输入数组
 *  offset : 位置
 *  length : 个数
 *  replacement : 替换值
 * */
//去掉数组中的某一部分并用其他值取代
array_splice($array, $offset, $length, $replacement);

//对数组中所有值求和
array_sum($array);

//带索引检查计算数组的差集，用回调函数比较数据
array_udiff_assoc($array1, $array2, $value_compare_func);     //value_compare_func : 在第一个参数小于，等于或大于第二个参数时，该比较函数必须相应地返回一个小于，等于或大于 0 的整数。

//带索引检查计算数组的差集，用回调函数比较数据和索引
array_udiff_uassoc($array1, $array2, $value_compare_func);    //value_compare_func : 对键名（索引）的检查也是由回调函数 key_compare_func 进行的。这和 array_udiff_assoc() 的行为不同，后者是用内部函数比较索引的。

//用回调函数比较数据来计算数组的差集
array_udiff($array1, $array2, $value_compare_func);           //value_compare_func : 在第一个参数小于，等于或大于第二个参数时，该比较函数必须相应地返回一个小于，等于或大于 0 的整数。

//移除数组中重复的值
array_unique($array);

//在数组开头插入一行或多个单元
array_unshift($array, $values);

//返回数组中所有的值
array_values($array);

//使用用户自定义函数对数组中的每个元素做回调处理
array_walk($array, $callback);

//建立一个数组，包括变量名和他们的值
compact($varname1);         //varname1 : compact() 接受可变的参数数目。每个参数可以是一个包括变量名的字符串或者是一个包含变量名的数组，该数组中还可以包含其它单元内容为变量名的数组， compact() 可以递归处理。

