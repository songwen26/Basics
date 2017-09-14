<?php
/*
 *  约瑟夫环问题
 *  相关题目：输入m,n输出最后那个大王 的编号
 *  @param int $n
 *  @param int $m
 *  @return int
 */
function get_king_mokey($n, $m){
    $arr = range(1, $n);

    $i = 0;
    while (count($arr) > 1){
        $i++;
        $survice = array_shift($arr);
        if ($i % $m != 0){
            array_push($arr,$survice);
        }
    }
    return $arr[0];
}
