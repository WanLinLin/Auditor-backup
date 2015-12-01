<?php
require_once "src/vendor/multi-array/MultiArray.php";
require_once "src/vendor/multi-array/Factory/MultiArrayFactory.php";
require_once "src/class/Jieba.php";
require_once "src/class/Finalseg.php";

use Fukuball\Jieba;
use Fukuball\Finalseg;

Jieba::init();
Finalseg::init();

$seg_list = Jieba::cut("怜香惜玉也得要看对象啊！");
var_dump($seg_list);

$seg_list = jieba.cut("我来到北京清华大学", true)
print "Full Mode:", "/ ".join(seg_list) #全模式

$seg_list = jieba.cut("我来到北京清华大学", false)
print "Default Mode:", "/ ".join(seg_list) #默認模式

$seg_list = jieba.cut("他来到了网易杭研大厦")
print ", ".join(seg_list)
?>
