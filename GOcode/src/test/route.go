package main

import (
	"fmt"
	"time"
)

func test_print(i int) {
	fmt.Println(i)
}

func main() {
	for i := 0; i < 10; i++ {
		go test_print(i) //调用的函数前加go 表示开启了并发
	}
	time.Sleep(time.Second) //
}
