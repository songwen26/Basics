##Rdeis
##第一部分
###简单的动态字符串
redis自己构建了一个简单的动态字符串（SDS）的抽象类型，并将SD用作Redis的默认字符串表示。<br/>
SDS除了用来保存数据库中的字符串值外，SDS还被用作缓冲区(buffer)：AOF模块中的AOF缓冲区，以及客户端状态中的输入缓冲区，都是由SDS实现的。<br>
#####SDS的定义
每一个sds.h/sdshdr结构表示一个SDS值：<br>

	struct sdshdr {
		//记录buf数组中使用字节的数量
		//等于SDS所保存字符串的长度
		int len;    //n表示这个SDS保存了一个N字节长的字符串
		//记录buf数组中未使用字节的数量
		int free;   //0表示这个SDS没有分配任何未使用空间。
		//字节数组，用于保存字符串
		char buf[];     //遵循C字符串以空字符结尾的惯例，保存空字符的1字节不计算在SDS的属性里面。
	}
######常数复杂度获取字符串长度
因为C字符串不记录自身的长度信息，所以为了获取一个C字符串长度需要变量整个字符串。<br>
SDS本身记录字符串长度。<br>
#####杜绝缓冲区溢出
C字符串不记录自身长度容易造成缓冲区溢出。<br>
SDS空间分配策略完全杜绝了发生缓冲溢出的可能性：档SDS API需要对SDS进行修改时，API会先检查SDS的空间十分满足修改所需的要求，如果不满足，API会自动将SDS的空间扩展至执行修改所需大小。<br>
#####减少修改字符串时带来的内存重分配次数

#####C字符串和SDS之间的区别
<p>
    C字符串
    <p>获取字符串长度的复杂度为0(N)</p>
    <p>API是不安全的，可能会造成缓冲区溢出</p>
    <p>修改字符串长度N次必须执行N次内存重分配</p>
    <p>只能保存文本数据</p>
    <p>可以使用所有&lt;string.h&gt;库中的函数</p>
</p>
<p>
    SDS
    <p>获取字符串长度的复杂度为0(1)</p>
    <p>API是安全的，不会造成缓冲区溢出</p>
    <p>修改字符串长度N次最多需要执行N次内存重分配</p>
    <p>可以保存文本或者二进制数据</p>
    <p>可以使用一部分&lt;string.h&gt;库中的函数</p>
</p>
###链表
链表提供了高效的节点重排能力，以及顺序性的节点访问方式，并且可以通过增删节点来灵活的调整链表的长度。<br>
#####链表和链表节点的实现
每个链表节点使用一个adlist.h/listNode结构来表示：

    typedef struct listNode {
        //前置节点
        struct listNode * prev;
        //后置节点
        struct listNode * next;
        //节点的值
        void * value;
    }listNode;
使用adlies.h/list来持有链表操作更方便：

    typedef struct list {
        //表头节点
        listNode * head;
        //表尾节点
        listNode * tail;
        //链表所包含的节点数量
        unsigned long len;
        //节点值复制函数
        void * (*dup) (void * ptr);
        //节点值释放函数
        void (*free) (void * ptr);
        //节点值对此函数
        int (*match) (void * ptr, void * key);
    }list;
list结构为链表表提供了表头指针head、表尾指针tail，以及链表长度计数器len，而dup、free和match成员则是用于实现多态链表所需的类型特定函数：<br>
dup函数用于复制链表节点所保存的值;<br>
free函数用于释放链表节点所保存的值;<br>
match函数则用于对吧链表节点所保存的值和另一个输入值是否相等。<br>
<p>
Redis的链表实现的特征可以总结如下：
    <p>双端：链表节点带有prev和next指针，获取某个节点的前置节点和后置节点的复杂度都是O(1)</p>
    <p>无环：表节点的prev指针和表结尾节点的next指针都指向NULL，对链表的访问以NULL为终点</p>
    <p>带表头指针和表尾指针：通过list结构的head指针和tail指针，程序获取链表的表头节点和尾节点的复杂度O(1)</p>
    <p>带链表长度计数器：程序使用list结构的len属性来对list持有的链表节点进行计数，程序获取链表节点数量的复杂度为O(1)</p>
    <p>多态：链表节点使用void*指针来保存节点值，并且可以通过list结构的dup、free、match三个属性节点值设置类型特定函数，所有链表可以用于保存各种不同类型的值</p>
</p>

###字典
字典，又称为符号表(symbol table)、关联数组(associative array)或映射(map)，是一种用于保存键值对的抽象数据结构。<br>
####字典的实现
Redis的字典使用哈希表作为底层实现一个哈希表里面可以有多个哈希表节点，而每个哈希表节点就保存了字典中的一个键值对。
#####哈希表
Redis字典所使用的哈希表由dict.h/dictht结构定义：

    typedef struct dictht {
        //哈希表数组
        dictEntry **table;
        //哈希表大小
        unsigned long size;
        //哈希表大小掩码，用于计算索引值
        //总是等于size-1
        unsigned long sizemask;
        //该哈希表已有节点的数量
        unsigned long used;
    }dictht;
#####哈希表节点
哈希表节点使用dictEntry结构表示，每个dictEntry结构都保存着一个键值对：
    
    typedef struct dictEntry {
        //键
        void *key;
        //值
        union{
            void *val;
            uint64_tu64;
            int64_ts64;
        }
        //指向下个哈希表节点，形成链表
        struct dictEntry *next;
    }dictEntry;
#####字典
Redis中的字典由dict.h/dict结构表示

    typedef struct dict {
        //类型特定函数
        dictType *type;
        //私有数据
        void *privdata;
        //哈希表
        dictht ht[2];
        //rehash 索引
        //当rehash不在进行时，值为-1
        in trehashidx; /* rehashing not in progress if rehashidx == -1 */        
    }dict;
####哈希算法

####解决键冲突
当有两个或以上数量的键被分配到了哈希表数组的同一个索引上面时，我们称这些键发生了冲突。<br>
Redis的哈希表使用链地址法来解决键冲突，每个哈希表节点都有一个next指针，多个哈希表节点可以用next指针构成一个单向链表，被分配到同一个索引上的多个节点可以用这个单向链表连接起来，这就解决了键冲突的问题。
####rehash

###跳跃表
跳跃表是一种有序数据结构，它通过在每个节点中维持多个指向其他节点的指针，从而达到快速访问节点的目的。<br>
跳跃表支持平均O(logN)、最坏O(N)复杂的节点查找，还可以通过顺序性操作来批量处理节点。<br>
Redis使用跳跃表作为有序集合键的底层实现之一，如果一个有序集合包含的元素数量比较多，又或者有序集合中元素的成员是比较长的字符串时，Redis就会使用跳跃表来作为有序集合键的底层实现。<br>

###整数集合

###压缩列表

###对象
