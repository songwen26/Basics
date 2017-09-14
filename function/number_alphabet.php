<?php
/*
 *  给一个有数字和字母的字符串，让连着的数字和字母对应
 *
 */
function number_alphabet($str){
    $number = preg_split('/[a-z]/', $str, -1, PREG_SPLIT_NO_EMPTY);
    $alphabet = preg_split('/\d+/', $str, -1, PREG_SPLIT_NO_EMPTY);
    $n = count($number);
    for ($i = 0;$i < $n;$i++){
        echo $number[$i] . ':' . $alphabet[$i]. '</br>';
    }
}
$str = '1sd33afjkl345rc';
number_alphabet($str);