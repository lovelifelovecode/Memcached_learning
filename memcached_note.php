（1）作为前台程序运行：
从终端输入以下命令，启动memcached:
/usr/local/memcached/bin/memcached -p 11211 -m 64m -vv


（2）作为后台服务程序运行：
# /usr/local/memcached/bin/memcached -p 11211 -m 64m -d

启动选项：

-d是启动一个守护进程；
-m是分配给Memcache使用的内存数量，单位是MB；
-u是运行Memcache的用户；
-l是监听的服务器IP地址，可以有多个地址；
-p是设置Memcache监听的端口，，最好是1024以上的端口；
-c是最大运行的并发连接数，默认是1024；
-P是设置保存Memcache的pid文件。




set 命令的基本语法格式如下：

set key flags exptime bytes [noreply] 
value 
参数说明如下：

key：键值 key-value 结构中的 key，用于查找缓存值。
flags：可以包括键值对的整型参数，客户机使用它存储关于键值对的额外信息 。
exptime：在缓存中保存键值对的时间长度（以秒为单位，0 表示永远）
bytes：在缓存中存储的字节数
noreply（可选）： 该参数告知服务器不需要返回数据
value：存储的值（始终位于第二行）（可直接理解为key-value结构中的value）

ps:
add xj 1 0 4
jing



add 命令的基本语法格式如下：

add key flags exptime bytes [noreply]
value
参数说明如下：

key：键值 key-value 结构中的 key，用于查找缓存值。
flags：可以包括键值对的整型参数，客户机使用它存储关于键值对的额外信息 。
exptime：在缓存中保存键值对的时间长度（以秒为单位，0 表示永远）
bytes：在缓存中存储的字节数
noreply（可选）： 该参数告知服务器不需要返回数据
value：存储的值（始终位于第二行）（可直接理解为key-value结构中的value）

ps:
add king 1 0 7
kingboy


get 命令的基本语法格式如下：
get key



delete 命令的基本语法格式如下：
delete key [noreply]



incr 与 decr 命令用于对已存在的 key(键) 的数字值进行自增或自减操作。

incr 命令的基本语法格式如下：
incr key increment_value


decr 命令的基本语法格式如下：
decr key decrement_value



stats 命令用于返回统计信息例如 PID(进程号)、版本号、连接数等。
stats 命令的基本语法格式如下：
stats


flush_all 命令用于用于清理缓存中的所有 key=>value(键=>值) 对。
flush_all 命令的基本语法格式如下：

flush_all [time] [noreply]


//PHP 连接 Memcached的基本用法，这些基本够了。其他更多复杂的用法请参照手册Memcached类
<?php
	class Aclass{
		public $a='good boy';
		private $b='bbb';
		protected $c='cccc';
		public function say(){
			return 'hellow wold';
		}
	}

	$m = new Memcached();
	$m ->addServer('localhost',11211);

	// 向一个新的key下面增加一个元素
	$m -> add('str','xiaojing');
	$m -> add('num',77);
	$m -> add('arr',['name'=>'king','age'=>27]);
	$m -> add('obj',new Aclass);

	// 检索一个元素
	var_dump($m->get('str'));
	var_dump($m->get('num'));
	var_dump($m->get('arr'));
	var_dump($m->get('obj'));
	echo '<hr>';

	//存储一个元素
	$m->set('str','The string is change!!!!');
	var_dump($m->get('str'));
	echo '<hr>';

	//删除一个元素
	$m->delete('str');
	var_dump($m->get('str'));

	//作废缓存中的所有元素
	$m->flush();
?>



在实际应用中，通常会把数据库查询的结果集保存到 memcached 中，下次访问时直接从 memcached 中获取，而不再做数据库查询操作，这样可以在很大程度上减轻数据库的负担。通常会将 SQL 语句 md5() 之后的值作为唯一标识符 key。
<?php
$m = new Memcached();
$m ->addServer('localhost',11211);

$sql = 'SELECT * FROM users';
$key = md5($sql); //memcached 对象标识符
if ( !($datas = $m->get($key)) ) {
	// 在 memcached 中未获取到缓存数据，则使用数据库查询获取记录集。
	echo "n".str_pad('Read datas from MySQL.', 60, '_')."n";

	$conn = mysql_connect('localhost', 'test', 'test');
	mysql_select_db('test');
	$result = mysql_query($sql);
	while ($row = mysql_fetch_object($result))
	$datas[] = $row;

	// 将数据库中获取到的结果集数据保存到 memcached 中，以供下次访问时使用。
	$m->add($key, $datas);
} else {
	echo "n".str_pad('Read datas from memcached.', 60, '_')."n";
}
var_dump($datas);
?>



php的cache数据如何在数据有变化时实现自动更新???

1.删除比更新好，如果你的请求里面包含了多次数据更新，由此会触发多次缓存更新，但实际上只有最后一次更新的缓存才是有效的。如果更新缓存的执行成本较高的话可能在偶发的高频更新下会引发执行效率的问题。

2.一般方式是在模型层做处理，因为数据的增删改都是在模型层进行的。有很多php框架都支持事件绑定，所以也可以使用事件机制处理。