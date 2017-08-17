<?php
/*
 * 文件处理函数
 */

//打开文件
fopen($filename, $mode);

//写文件
fwrite($handle, $string, $length);       //fputs() 别名
file_put_contents($filename, $string, $length);

//关闭文件
fclose($handle);

//测试文件指针是否到了文件结束的位
feof($handle);          //如果到文件末尾返回true

//每次读取一行数据
fgets($handle, $length);
fgetss($handle, $length);   //去掉任何HTML PHP标签

//读取整个文件
readfile($filename);
fpassthru($handle);     //输出文件指针处的所有剩余数据
file($filename);        //把整个文件读入一个数组

//读取一个字符
fgetc($handle);

//读取任意长度
fread($handle, $length);

//查看文件是否存在
file_exists($filename);

//确定文件大小
filesize($filename);

//删除一个文件
unlink($filename);

//在文件中定位
rewind($handle);        //倒回文件指针的位置
fseek($handle);         //在文件指针中定位
ftell($handle);         //返回文件指针读/写的位置

//文件锁定
/*
 * operation :
 * LOCK_SH 取得贡献锁定（读取的程序）
 * LOCK_EX取得独占锁定（写入的程序）
 * LOCK_UN释放锁定（无论共享或独占）
 * LOCK_NB防止在请求加锁时发生阻塞（Win 下不支持）
 */
flock($handle, $operation);




