#API接口设计要求
<p>
	URL不能包含动词，因为“资源”表示一种实体，所以应该是名称，URL不应该有动词，动词应该放在HTTP协议中。
</p>
<p>
	举例：某个URL是/posts/show/1，其中show是动词，这个URL就设计错了，正确的写法应该是/posts/1，然后用GET方法表示show
</p>
<p>
	如果某些动作是HTTP动词表示不了的，你就应该把动作做成一种资源。比如网上汇款，从账户1向账户2汇款500元，错误的URI是：
</p>
	POST /accounts/1/transfer/500/to/2
<p>
	正确的写法是把动词transfer改为transaction，资源不能是动词，但是可以是一种服务：
</p>
	POST /transaction HTTP/1.1
	Host: 127.0.0.1

	from=1&to=2&amount=500.00
<p>
	另一个设计误区，就是在URL中加入版本号：
</p>
	http:www.example.com/app/1.0/foo
	http:www.example.com/app/1.1/foo
	http:www.example.com/app/2.0/foo
<p>
	因为不同版本，可以理解成同一种资源的不同表现形式，所以应该采用同一个URL。版本号可以在HTTP请求头信息的Accept字段中进行区分：
</p>
	Accept: vnd.example-com.foo+json; version=1.0
	Accept: vnd.example-com.foo+json; version=1.1
	Accept: vnd.example-com.foo+json; version=2.0
#规范
##域名
<p>
	域名规范：专有域名。
<p>
<p>例如：http://api.example.com</p>
<p>
	如果确定API很简单，不会有进一步扩展，可以考虑放在主域名下。
</p>
	https://example.com/api/
##版本
<p>
	1、可以放入到url里：http://api.example/v1
</p>
<p>
	2、将版本放入HTTP头信息里。
</p>
##路径
<p>
	网址地表一种资源，所以网址中不能有动词，只能有名词，而且所用的名称往往与数据库的表格名对应。一般来说，数据库中的表都是同种记录的“集合”，所以API中的名词也应该使用复数。
</p>
##HTTP动词
<p>
	对于资源的具体操作类型，由HTTP动词表示。
</p>
<p>
	常用的HTTP动词有下面五个（括号对应得SQL命令）
</p>
	

        GET（SELECT）：从服务器取出资源（一项或多项）。
        POST（CREATE）：在服务器新建一个资源。
        PUT（UPDATE）：在服务器更新资源（客户端提供改变后的完整资源）。
        PATCH（UPDATE）：在服务器更新资源（客户端提供改变的属性）。
        DELETE（DELETE）：从服务器删除资源。

<p>
	还有两个不常用的HTTP动词：
</p>
	

        HEAD：获取资源的元数据。
        OPTIONS：获取信息，关于资源的哪些属性是客户端可以改变的。
##过滤信息
<p>
	如果记录数量很多，服务器不可能都将它们返回给用户。API应该提供参数，过滤返回结果。下面是一些常见的参数。
</p>
	

        ?limit=10：指定返回记录的数量
        ?offset=10：指定返回记录的开始位置。
        ?page=2&per_page=100：指定第几页，以及每页的记录数。
        ?sortby=name&order=asc：指定返回结果按照哪个属性排序，以及排序顺序。
        ?animal_type_id=1：指定筛选条件
<p>
	参数的设计允许存在冗余，即允许API路径和URL参数偶尔有重复。
</p>
##状态码
<p>
	服务器向用户返回的状态码和提示信息。常见的有以下一些（方括号是该状态码对应的HTTP动词）
</p>
	

        200 OK - [GET]：服务器成功返回用户请求的数据，该操作是幂等的（Idempotent）。
        201 CREATED - [POST/PUT/PATCH]：用户新建或修改数据成功。
        202 Accepted - [*]：表示一个请求已经进入后台排队（异步任务）
        204 NO CONTENT - [DELETE]：用户删除数据成功。
        400 INVALID REQUEST - [POST/PUT/PATCH]：用户发出的请求有错误，服务器没有进行新建或修改数据的操作，该操作是幂等的。
        401 Unauthorized - [*]：表示用户没有权限（令牌、用户名、密码错误）。
        403 Forbidden - [*] 表示用户得到授权（与401错误相对），但是访问是被禁止的。
        404 NOT FOUND - [*]：用户发出的请求针对的是不存在的记录，服务器没有进行操作，该操作是幂等的。
        406 Not Acceptable - [GET]：用户请求的格式不可得（比如用户请求JSON格式，但是只有XML格式）。
        410 Gone -[GET]：用户请求的资源被永久删除，且不会再得到的。
        422 Unprocesable entity - [POST/PUT/PATCH] 当创建一个对象时，发生一个验证错误。
        500 INTERNAL SERVER ERROR - [*]：服务器发生错误，用户将无法判断发出的请求是否成功。
##错误处理
<p>
	如果状态码是4XX，就应该向用户返回出错信息。一般来说，返回的信息将error作为键名，出错信息作为键值即可。
</p>
	{
		error:"Invalid API key"
	}
##返回结果
<p>
	针对不同操作，服务器向用户返回的结果应该符合以下规范
</p>
	

        GET /collection：返回资源对象的列表（数组）
        GET /collection/resource：返回单个资源对象
        POST /collection：返回新生成的资源对象
        PUT /collection/resource：返回完整的资源对象
        PATCH /collection/resource：返回完整的资源对象
        DELETE /collection/resource：返回一个空文档
##Hypermedia API
<p>
RESTful API最好做到Hypermedia，即返回结果中提供链接，连向其他API方法，使得用户不查文档，也知道下一步应该做什么。
比如，当用户向api.example.com的根目录发出请求，会得到这样一个文档
</p>
	
    {"link": {
      "rel":   "collection https://www.example.com/zoos",
      "href":  "https://api.example.com/zoos",
      "title": "List of zoos",
      "type":  "application/vnd.yourformat+json"
    }}
<p>
上面代码表示，文档中有一个link属性，用户读取这个属性就知道下一步该调用什么API了。rel表示这个API与当前网址的关系（collection关系，并给出该collection的网址），href表示API的路径，title表示API的标题，type表示返回类型。

Hypermedia API的设计被称为HATEOAS。Github的API就是这种设计，访问api.github.com会得到一个所有可用API的网址列表。
</p>
	
    {
      "current_user_url": "https://api.github.com/user",
      "authorizations_url": "https://api.github.com/authorizations",
      // ...
    }
<p>
从上面可以看到，如果想获取当前用户的信息，应该去访问api.github.com/user，然后就得到了下面结果。
</p>
	
    {
      "message": "Requires authentication",
      "documentation_url": "https://developer.github.com/v3"
    }
<p>
上面代码表示，服务器给出了提示信息，以及文档的网址。
</p>