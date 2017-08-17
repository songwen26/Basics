<?php
/*
 * 最大的连续子数组和
 */
function maxArr($array){
    //最大值
    $max = $array[0];
    //以KEY结尾的最大值
    $endMax = $array[0];

    foreach ($array as $key => $value){
        if ($key == 0){
            //以0结尾作为开始，这里直接跳过
            continue;
        }
        //以$key结尾的数组最大值
        $endMax = ($endMax >0) ? ($endMax + $value) : $value;
        //总的最大值
        $max = ($endMax > $max) ? $endMax: $max;
    }
    return $max;
}
//测试用例
$array = array(-1,4,5,6,-10,12,-2,1);
echo maxArr($array);