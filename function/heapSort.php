<?php
/*
 * 堆排序算法与PHP实现
 *堆排序有点小复杂，分成三块：
 *第一堆，什么是堆，什么是最大堆
 *第二块，怎么讲堆调整为最大堆，这部分是重点
 *第三块，堆排序介绍
 *
 * 第一堆，什么是堆，什么是最大堆
 *  什么是堆
 *  这里的堆(二叉堆)，指得不是堆栈的那个堆，而是一种数据结构
 *
 *      堆可以视为一棵完全的二叉树，完全二叉树的一个“优秀”的性质是，
 *  除了最底层之外，每一层都是满的，这使得堆可以利用数组来表示，
 *  每一个节点对应数组中的一个元素
 *
 * 二叉堆一般分为两种：最大堆和最小堆
 *  什么是最大堆
 *  堆中每个父节点的元素值都是大于等于其孩子节点(如果存在，这样的堆就是一个最大堆
 *  因此，最大堆中的最大元素值出现在根节点(堆项)
 *  节点与数组索引关系
 *  对于给定的某个节点的下标i，可以很容易的计算出这个节点的父节点，孩子节点的下标
 *  而且计算公式简约
 *  PARENT(i) = intval(i/2);
 *  LEFT(i) = 2i;
 *  RIGHT(i) = 2i+1;
 *
 * 第二块，怎么将堆调整为最大堆，这部分是重点
 *  谁小谁往下方交换位置直到调整到由大到小的向下排序。
 *
 * 第三块，堆排序介绍
 *  堆排序就是把堆项的最大数取出，将剩下的堆继续调整为最大堆，
 *  具体过程如第二块，以递归实现。
 *  剩余部分调整为最大堆后，再次将堆项的最大数取出，再将剩余部分调整为最大堆，
 *  这个过程持续到剩余数，只有一个时结束
 *
 *
 * */

//堆排序 （对简单选择排序的改进）
function swap($arr, $a, $b){
    $temp = $arr[$a];
    $arr[$a] = $arr[$b];
    $arr[$b] = $temp;
}

//调整$arr[$start]的关键字，使$arr[$start]、$arr[$start+1]、、、$arr[$end]成为一个大根堆(根节点最大的完全二叉树)
//注意这里节点s的左右孩子是2*s + 1 和 2*s + 2 （数组开始下标为0时）
function HeadAdjset($arr, $start, $end){
    $temp = $arr[$start];
    //沿关键字较大的孩子节点向下筛选
    //左右孩子计算（我这里数组开始下标识 0）
    //左孩子2 * $start + 1，右孩子2* $start + 2
    for($j = 2*$start +1;$j<$end;$j = 2*$j+1){
        if ($j != $end && $arr[$j] < $arr[$j+1]){
            $j++;//转化为右孩子
        }
        if ($temp >= $arr[$j]){
            break;  //已经满足大根堆
        }
        //将根节点设置为子节点的较大值
        $arr[$start] = $arr[$j];
        //继续往下
        $start = $j;
    }
    $arr[$start] = $temp;
}

function heapSort($arr){
    $count = count($arr);
    //先将数组构造成大根堆(由于完全是二叉树，所以这里用floor($count/2)-1,下标小于或等于这个数的节点都是有孩子的节点)
    for ($i = floor($count/2)-1; $i >= 0; $i --){
        HeadAdjset($arr,$i,$count);
    }
    for($i = $count -1; $i >= 0; $i --){
        //将堆项元素与最后一个元素交换，获取到最大元素（交换后的最后一个元素），将最大元素放到数组末尾
        swap($arr,0,$i);
        //经过交换，将最后一个元素（最大元素）脱离大根堆，并将未经排序的新树（$arr[0...$i-1]）重新调整为大根堆
        HeadAdjset($arr,0,$i-1);
    }
}

//测试
$arr = array(9,1,5,8,3,7,4,6,2);
heapSort($arr);
var_dump($arr);