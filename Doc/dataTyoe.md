#PHP 数据类型
##四种标量类型：
Integer     （整型）
Boolean     （布尔型）
Float       （浮点型，也称作Double）
String      （字符串）
两种复合类型：
Array       （数组）
Object      （对象）
特殊类型：
Resource    （资源）
NULL        （空）


##一、PHP变量类型及存储结构
###1、变量存储结构
变量的值存储到如下zval容器中。zval结构定义在Zend/zend.h文件，结构如下：

    typedef struc _zval_struct zval:
    struct _zval_struct {
        /* Variable information */
        zvalue_value value;
        zend_unit refcount_gz;
        zend_uchar type;
        zend_uchar is_ref__gc;
    }

zval结构体中有四个字段，起含义分别为：
属性                      含义                         默认值
refcount_gc               表示引用计数                    1
is_ref_gc                 表示是否为引用                  0
value                     存储变量的值
type                      变量具体的类型

在PHP5.3之后，引入了新的垃圾回收机制，引用计数和引用的字段名改为refcout_gc和is_ref_gc在此之前为refcout和is_ref

变量的值存储在另一个机构体zvalue_value，值存储如下。

###2、变量类型
zval结构体的type字段就是实现弱类型关键的字段，type的值为：IS_NULL、IS_BOOL、IS_LONG、IS_DOUBLE、IS_STRING、IS_OBJECT和IS_RESOURCE之一。
除此之外和他们定义在一起的类型还有IS_CONSTANT、IS_CONSTANT_ARRAY。

##二、变量的值存储
前面提到变量的值存储在zvalue_value联合体中，结构体定义如下：

    typedef union _zval_value {
        long lval;      /* long vlaue */
        double dval;    /* double value */
        struct {
            char *val;
            int len;
        } str;
            HashTable *ht;      /* hash table value */
        zend_object_value obj;
    } zvalue_value;
各种类型的数据会使用不同的方法来进行变量值得存储，其对应赋值方式如下：
一般类型：
变量类型                宏               中文解析
boolean               ZVAL_BOOL         布尔型/整型的变量值存储于(zval).value.lval中，
integer               ZVAL_LONG         以相应的IS_*进行存储
float                 ZVAL_DOUBLE       Z_TYPE_P(z)=IS_BOOL/LONG;Z_LVAL_P(z)=(b)!=0;
null                  ZVAL_NULL         NULL值的变量值不需要存储，只需要把(zval).type标为IS_NULL
                                        Z_TYPE_P(z)=IS_NULL;
resource              ZVAL_RESOURCE     资源类型的存储与其他一般变量无异，但其初始化及存储实现则不同Z_TYPR_P(z)=IS_RESOURCE;Z_LVAL_P(z)=1;

字符串
字符串的类型标示和其他数据类型一样，不过在存储字符串时多了一个字符串长度的字段。
struct {
    char *val;
    int  len;
}
(存储字符串长度是因为字符串的操作十分频繁，有利于节省时间，是空间换时间的做法)

数组Array
数组是PHP中最常用也是最强大变量类型。数组的值存储在zvalue_value.ht字段中，他是一个HashTable类型的数据。
PHP数组使用哈希表来存储关联数据。PHP的哈希表实现中使用了两个数据结构Hash Table和 Bucket。PHP所有的工作都是由哈希表实现。

对象Object
PHP的对象是一种复合型的数据，使用一种zend_object_value的结构体来存放，其定义如下

	typedef struct _zend_object_value {
	    zend_object_handle handle;      // unsigned int类型，EG(objects_store).object_buckets的索引
    	zend_object_handlers *handlers;
	} zned_object_value;

PHP的对象只有在运行时才会被创建，前面介绍了EG宏，这是一个全局结构体由于保存在运行时的数据。其中就包括了用来保存所有
被创建的对象的对象池，EG(object_store)，而对象值内容的zend_object_handle域就是当前对象在对象池中所在的索引，handlers
字段则是将对象进行操作时的处理函数保存起来。

PHP的弱变量容器的实现方式是兼容并包的形式体现。

##三、变量与类型相关扩展
http://php.net/manual/zh/refs.basic.vartype.php



