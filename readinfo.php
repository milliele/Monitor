<?php
	
	$error['Fail']=0;
	//连接数据库并选中数据库
	$con = mysql_connect("localhost","root","11");
	if (!$con)
	{
		$error['Fail']=1;
		$error['Type']=0;
		echo json_encode($error);
		die();
	}
	mysql_select_db("ICN_monitor", $con);
	
	$re233 = mysql_query("SELECT * FROM node");
	if (!$re233)
	{
		$error['Fail']=1;
		$error['Type']=1;
		echo json_encode($error);
		mysql_close($con);
		die();
	}
	$output = $output.'var pkuid=-1;var thuid=-1;';
	//如果有这两个学校，记录并放在前面
	$res = mysql_query("SELECT * FROM node WHERE node_name = '北京大学'");
	if (!$res)
	{
		$error['Fail']=1;
		$error['Type']=1;
		echo json_encode($error);
		mysql_close($con);
		die();
	}
	if(mysql_num_rows($res)!=0)
	{
		$tmp = 	mysql_fetch_array($res);		
		$output = $output.'pkuid='.$tmp['node_id'].';';
	}
	$res = mysql_query("SELECT * FROM node WHERE node_name = '清华大学'");
	if (!$res)
	{
		$error['Fail']=1;
		$error['Type']=1;
		echo json_encode($error);
		mysql_close($con);
		die();
	}
	if(mysql_num_rows($res)!=0)
	{
		$tmp = 	mysql_fetch_array($res);		
		$output = $output.'thuid='.$tmp['node_id'].';';
	}
	while($row = mysql_fetch_array($re233)){
		$node = $row['node_name'];
		$node_num = $row['node_id'];
		$warning[$node_num]['err'] = 0;
		$warning[$node_num]['error'] = Array();
		$fibs[$node_num]=Array();
		$nodes[$node_num]['name']="";$nodes[$node_num]['remark']="";$nodes[$node_num]['password']="";
		$infos[$node_num]['ip']=Array();$infos[$node_num]['cpu']=Array();$infos[$node_num]['mem']=Array();$infos[$node_num]['hd']=Array();
		$infos[$node_num]['net']=Array();$infos[$node_num]['uptime']=Array();
		//记录结点的信息
		$nodes[$node_num]['name'] = $row['node_name'];
		$nodes[$node_num]['remark'] = $row['remark'];
		$nodes[$node_num]['password'] = $row['password'];
		$infos[$node_num]['ip'] = $row['ip'];
		// 读取FIB表内容并且显示
		if(!file_exists("FIBup/FIB".$node_num.".txt"))
		{
			$warning[$node_num]['err']=1;
			$temp['type']=1;$temp['remark']='FIB not exist.';
			array_push($warning[$node_num]['error'],$temp);
			continue;
		}
		else {
			if(filemtime("FIBup/FIB".$node_num.".txt")<(time()-30*60))//如果已经半小时没有更新过了，判断router出现问题
			{
				if($warning[$node_num]['err']==0) $warning[$node_num]['err']=2;
				$temp['type']=2;$temp['remark']='Out-of-date FIB.';
				array_push($warning[$node_num]['error'],$temp);
			}
			$fib = file("FIBup/FIB".$node_num.".txt");
			if(!$fib) {
				$warning[$node_num]['err']=1;
				$temp['type']=1;$temp['remark']='Can not read FIB.';
				array_push($warning[$node_num]['error'],$temp);
				continue;
			}
			//记录结点的FIB表							
			foreach($fib as $line)
			{
				sscanf($line,"%s %s",$content_name,$next_hop);
				$urltmp = explode('/',$next_hop);
				if($content_name=='default') continue;
				//存到一个数组里
				$md_content = md5($content_name);
				$fibs[$node_num][$md_content]['next'] = $urltmp[0];
				$fibs[$node_num][$md_content]['name'] = $content_name;								
			}
			if(!file_exists("SystemInfo/Info".$node_num.".txt"))
			{
				$warning[$node_num]['err']=1;
				$temp['type']=1;$temp['remark']='Systeminfo not exist.';
				array_push($warning[$node_num]['error'],$temp);
			}
			else {
				if(filemtime("SystemInfo/Info".$node_num.".txt")<(time()-30*60))//如果已经半小时没有更新过了，判断router出现问题
				{
					if($warning[$node_num]['err']==0) $warning[$node_num]['err']=2;
					$temp['type']=2;$temp['remark']='Out-of-date System Info.';
					array_push($warning[$node_num]['error'],$temp);
				}
				$info = file("SystemInfo/Info".$node_num.".txt");
				if(!$info) {
					$warning[$node_num]['err']=1;
					$temp['type']=1;$temp['remark']='Can not read System Info.';
					array_push($warning[$node_num]['error'],$temp);
					continue;
				}
				$infos[$node_num]['cpu']=json_decode(str_replace("'",'"',substr($info[0],0,-1)));
				$infos[$node_num]['mem']=json_decode(str_replace("'",'"',substr($info[1],0,-1)));
				$infos[$node_num]['hd']=json_decode(str_replace("'",'"',substr($info[2],0,-1)));
				$infos[$node_num]['net']=json_decode(str_replace("'",'"',substr($info[3],0,-1)));
				$infos[$node_num]['uptime']=json_decode(str_replace("'",'"',substr($info[4],0,-1)));
			}
		}
	}
	$output = $output."nodes = ".json_encode($nodes).";";
	$output = $output."fibs = ".json_encode($fibs).";";
	$output = $output."infos = ".json_encode($infos).";";
	$output = $output."warning = ".json_encode($warning).";";
	//delete file
	if (file_exists("js/my.js"))
		if(!unlink("js/my.js"))
		{
			$error['Fail']=1;
			$error['Type']=3;
			echo json_encode($output);
			mysql_close($con);
			die();
		}
	if(!file_put_contents("js/my.js",$output))
	{
		$error['Fail']=1;
		$error['Type']=4;
		echo json_encode($output);
		mysql_close($con);
		die();
	}
	echo json_encode($error);
	mysql_close($con);
?>
