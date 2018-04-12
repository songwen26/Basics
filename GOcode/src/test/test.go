/*package main

type Color int

const (
	Black Color = iota
	Red
	Blue
)

func test(c Color) {}

func main() {
	c := Black
	test(c)

	x := 1
	test(x)		//会报错
	test(1)
}
*/
package main

import (
	"fmt"
	//"sort"
)

/*func main() {
	//var array [5]int
	//array = [5]int{1, 2, 3, 4, 5}
	array := [5]int{1, 2, 3, 4, 5}
	fmt.Println(array)
}*/

//数组
/*func main() {
	array := [5]int{1: 1, 3: 4}
	//fmt.Printf("%d\n", array)
	array[2] = 9
	//fmt.Println(array[2])
	for i, v := range array {
		fmt.Printf("索引:%d,值:%d\n", i, v)
	}
}*/

//切片slice
/*func main() {
	slice := []int{1, 2, 3, 4, 5}
	slice1 := slice[:]
	slice2 := slice[0:]
	slice3 := slice[:4]
	//切片公用一个底层数组，当更改任意一个切片内的值时所有的切片将都修改
	slice3[2] = 1

	fmt.Println(slice1)
	fmt.Println(slice2)
	fmt.Println(slice3)
	fmt.Println(slice)
}*/
/*func main() {
	slice := []int{1, 2, 3, 4, 5}
	newSlice := slice[1:3]

	newSlice = append(newSlice, 10)
	fmt.Println(newSlice)
	fmt.Println(slice)
}*/
//迭代切片
/*func main() {
	slice := []int{1, 2, 3, 4, 5}
	//用_忽略索引
	for _, v := range slice {
		fmt.Printf("值:%d\n", v)
	}
	for i := 0; i < len(slice); i++ {
		fmt.Printf("值:%d\n", slice[i])
	}
}*/
//在函数间传递切片
//两个切片地址不一样，所以切片在函数之间传递是复制的。而修改一个索引的值后原切片的值也修改了。说明它们公用一个底层数组
/*func main() {
	slice := []int{1, 2, 3, 4, 5}
	fmt.Printf("%p\n", &slice)
	modify(slice)
	fmt.Println(slice)
}
func modify(slice []int) {
	fmt.Printf("%p\n", &slice)
	slice[1] = 10
}*/

//映射map
/*func main() {
	dict := make(map[string]int)
	dict["integral"] = 56
	dict := map[string]int{"intg": 26}
	integral, exists := dict["integral"]
	fmt.Println(integral)
	fmt.Println(exists)
	//fmt.Println(dict)
}*/
/*func main() {
	dict := map[string]int{"王五": 60, "张三": 45, "阿达": 56}
	//非排序的
	for key, value := range dict {
		fmt.Println(key, value)
	}
	//排序版
	var names []string
	for name := range dict {
		names = append(names, name)
	}
	sort.Strings(names) //排序
	for _, key := range names {
		fmt.Println(key, dict[key])
	}

}*/

//方法调用
type person struct {
	name string
}

func (p person) String() string {
	return "the person name is " + p.name
}
func main() {
	p := person{name: "张三"}
	fmt.Println(p.String())
}
