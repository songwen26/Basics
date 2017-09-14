<?php
/**
 * 打乱数组
 *  @param array $arr
 * @return array
 */
function custom_shuffle($arr){
    $n = count($arr);
    for ($i = 0;$i < $n;$i++){
        $rand_pos = mt_rand(0,$n);
        if ($rand_pos != $i){
            $temp = $arr[$i];
            $arr[$i] = $arr[$rand_pos];
            $arr[$rand_pos] = $temp;
        }
    }
    return $arr;
}