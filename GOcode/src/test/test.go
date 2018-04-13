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
/*type person struct {
	name string
}

func (p person) String() string {
	return "the person name is " + p.name
}
func main() {
	p := person{name: "张三"}
	fmt.Println(p.String())
}*/
/*func main() {
	p:=person{name:"张三"}
	p.modify() //值接收者，修改无效
	fmt.Println(p.String())
}
type person struct {
	name string
}
func (p person) String() string{
	return "the person name is "+p.name
}
func (p person) modify(){
	p.name = "李四"
}*/
/*func main() {
	p := person{name: "张三"}
	p.modify()
	fmt.Println(p.String())
}

type person struct {
	name string
}

func (p person) String() string {
	return "the person name is " + p.name
}

// 使用一个指针作为接收者就会起作用了。
// 因为指针接收者传递的是一个指向原值指针的副本，
// 指向的还是原来类型的值所以修改时，同时也会影响原来类型变量的值

func (p *person) modify() {
	p.name = "李四"
}*/
/*
* 在调用方法的时候，传递的接收者本质上都是副本，只不过一个是这个值副本，
* 一是指向这个值指针的副本。指针具有指向原有值得特性，所以修改了指针指向的值
* 也就修改了原有的值。
* 可以简单的理解为值接收者使用的是值得副本来调用方法，而指针接收者使用实际的值来调用方法。
 */

//可变参数
/*func main() {
	fmt.Println("1", "2", "3")
}*/
/*func main() {
	print("1", "2", "3")
}
func print(a ...interface{}) {
	for _, v := range a {
		fmt.Print(v)
	}
	fmt.Println()
}*/

//接口
//值接收者实现
/*func main() {
	var c cat
	invoke(c)
}*/
/*func main() {
	var c cat
	invoke(&c)
}

//需要一个animal接口作为参数
func invoke(a animal) {
	a.printInfo()
}

type animal interface {
	printInfo()
}
type cat int

//值接收者实现animal接口
func (c cat) printInfo() {
	fmt.Println("a cat")
}*/
