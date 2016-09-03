<?php
	if(!file_exists("FIBup/FIB4.txt"))
		{
			echo 'not exit<br>';
		}
	if(filemtime("FIBup/FIB4.txt")<(time()-30*60))//如果已经半小时没有更新过了，判断router出现问题
	{
		echo 'not edit<br>';
	}
	$fib = file("FIBup/FIB4.txt");
	if(!$fib) echo 'false';
?>

