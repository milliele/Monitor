<?php
if(isset($_POST['event']) && $_POST['event']=='sendfibdown')
{
	//$_POST['ip']里存放了IP信息
	//$_SERVER['HTTP_REFERER']里存了哪个站点发来的,格式是“ICNi”
	$fib_location = "FIBready.txt";	//$fib_location里存放要发的FIB表的路径
	echo file_get_contents($fib_location);
}
?>