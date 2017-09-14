<?php
/*
 *  给定一个有序整数序列，找到绝对值最小的元素
 *
 *  获取绝对值最小的元素
 *  @param array $arr
 *  @return int
 */
function get_min_abs_value($arr){
    $n = count($arr);
    //如果符号相同，直接返回
    if (is_same_sign($arr[0], $arr[$n-1])){
        return $arr[0] >= 0 ? $arr[0] : $arr[$n-1];
    }

    //二分查找
    $left = 0;
    $right = $n - 1;

    while($left <= $right){
        if ($left + 1 === $right){
            return abs($arr[$left]) < abs($arr[$right]) ? $arr[$left] : $arr[$right];
        }

        $mid = intval(($left+$right)/2);

        if ($arr[$mid] < 0){
            $left = $mid +1;
        }else{
            $right = $mid - 1;
        }
    }

}

/*
 *  判断符号是否相同
 *  @param int $a
 *  @param int $b
 *  @return boolean
 */
function is_same_sign($a, $b){
    if ($a * $b > 0){
        return true;
    }else{
        return false;
    }
}