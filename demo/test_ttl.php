<?php
/**
  * 带缓存时间自行处理的secache演示脚本
  * @author Horse Luke
  * @version $Id: test_ttl.php 7 2011-07-19 10:32:48Z horselukeCN@gmail.com $
  */

require('../secache.php');
$cache = new secache;
$cache->workat('cachedata');

$key = md5('test_arr_ttl_store'); //You must *HASH* it by your self（缓存键值。和以前版本保持一致，请自行hash）
$ttl = 5; //缓存时间，单位为秒。新增参数。默认为30天（2592000秒），可以为0但不建议（因为如果很少使用该缓存一样会被lru清理掉），也就是说不要把secache当永久缓存使用

$value = null;
if($cache->fetch($key,$value)){
	echo 'find cache:<br />';
	print_r($value);
}else{
	$value = createRandArray();    //待缓存数据。和以前版本不同，可以存入任何类型值（但不建议存布尔值false，因为这会导致{@link secache::fetch()}的缓存判断失误）
	$cache->store($key,$value,$ttl);    //带缓存时间自行处理的secache使用方法
	echo 'cache not found and recreated, refresh to see';
}

//status show
echo '<hr />';
$curBytes = $totalBytes = 0;
$_status = $cache->status($curBytes,$totalBytes);
echo 'totalBytes:'. ($totalBytes / 1024). ' KB ; curBytes:'. ($curBytes / 1024). ' KB';
echo '<br />';
print_r($_status);


//兼容php4并且仅为演示，故没有采取更安全的mt_rand
function createRandArray(){
	$return = array();
	for($i=1;$i<=10;$i++){
		$return[$i] = rand(1, 100000);
	}
	return $return;
}
