![image](http://secache.googlecode.com/files/secache-logo.png)

This class can be used to store and retrieve cached values from single file.
It can store one or more keys in a single cache file.
The class can also look up for a given key and retrieve the store value.

It can lock the file during access to prevent corruption, but there is also a sub-class that can be used to access the cache file without locking it.

It can automatically clean-up the least recently used entries to free space

# php编写的文件型缓存解决方案
 * 纯php实现, 无须任何扩展，支持php4 / 5
 * 使用lru算法自动清理过期内容
 * 可以安全用于多进程并发
 * 最大支持1G缓存文件
 * 使用hash定位，读取迅速

[用法样例](test.php)


```
require('../secache/secache.php');
$cache = new secache;
$cache->workat('cachedata');

$key = md5('test'); //必须自己做hash，前4位是16进制0-f,最长32位。
$value = '值数据'; //必须是字符串

$cache->store($key,$value);

if($cache->fetch($key,$return)){
    echo '<li>'.$key.'=>'.$return.'</li>';
}else{
    echo '<li>Data get failed! <b>'.$key.'</b></li>';
}
```

## 基于性能考虑,几点约束
 * 键需要自己做hash处理,最长32位.
 * 值必须是字符串。如果要存对象，请自己serialize

## 应用的项目
 * [shopex购物系统](http://www.shopex.cn)


[![image](http://www.phpclasses.org/award/innovation/nominee.gif)<br />April 2010 Number 4](http://www.phpclasses.org/package/6078-PHP-Store-and-retrieve-cached-values-from-single-file.html)


---


# 有关增加缓存时间ttl的说明文档
Horse Luke / 2011-07-19

## 原理

* 存储缓存（store）时增加存储过期时间。
* 在读取缓存（fetch）时，如果发现过期，就直接返回false。
* 此时由于没有执行任何lru操作，故可以认为该缓存已废弃，等待lru的自动清理操作。
* 此改动不破坏原有lru算法，修改成本低，适用于希望cache系统自行处理缓存过期问题，
* 同时解决可能出现的“secache频繁读取事实上已废弃的缓存导致lru不会清理”的问题。


## 使用方法

* 和以前相似，不过在调用store参数时，可选增加第三个参数$ttl，单位为秒。
* 默认存储30天，当然也可以为0（即理想中的永久）。但考虑到如果经常不用此缓存也会被lru清理掉的特性，不建议这么做。
* store的参数说明、以及和以前相比传参的改动如下：
```
    /**
     * 存入一个缓存
     * @param string $key 缓存键值。和以前版本保持一致，请自行hash
     * @param mixed $data 待缓存数据。和以前版本不同，可以存入任何类型值（但不建议存布尔值false，因为这会导致{@link secache::fetch()}的缓存判断失误）
     * @param integer $ttl 缓存时间，单位为秒。新增参数。默认为30天（2592000秒），可以为0但不建议（因为如果很少使用该缓存一样会被lru清理掉），也就是说不要把secache当永久缓存使用
     * @return bool
     */
    function store($key,$data,$ttl=2592000){}
```