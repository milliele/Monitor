<?php
$output['Fail']=0;

if(isset($_POST['event']) && $_POST['event']=='sendalive')
{
	//连接数据库并选中数据库
	$con = mysql_connect("localhost","root","11");
	if (!$con)
	{
		$output['Fail']=1;
		$output['Type']=0;
		echo json_encode($output);
		die();
	}
	mysql_select_db("ICN_monitor", $con);
	
	$res = mysql_query("SELECT * FROM node WHERE ip = '".$_POST['ip']."'");
	if (!$res)
	{
		$output['Fail']=1;
		$output['Type']=1;
		echo json_encode($output);
		mysql_close($con);
		die();
	}

	if(mysql_num_rows($res)==0)//如果原本不存在这个router
	{
		$res = mysql_query("INSERT INTO node
		VALUES (0,'".$_POST['location']."','".$_POST['longitude']."','".$_POST['latitude']."','".$_POST['ip']."','".$_POST['remark']."', '".$_POST['password']."')");
		if (!$res)
		{
			$output['Fail']=1;
			$output['Type']=2;
			echo json_encode($output);
			mysql_close($con);
			die();
		}
		$res = mysql_query("SELECT * FROM node WHERE ip = '".$_POST['ip']."'");
		$row = mysql_fetch_array($res);
		if (!$res)
		{
			$output['Fail']=1;
			$output['Type']=1;
			echo json_encode($output);
			mysql_close($con);
			die();
		}
		//顺序是：cpu、memory、hd、network、uptime
		//delete file
		if (file_exists("SystemInfo/Info".$row['node_id'].".txt"))
			if(!unlink("SystemInfo/Info".$row['node_id'].".txt"))
			{
				$output['Fail']=1;
				$output['Type']=3;
				echo json_encode($output);
				mysql_close($con);
				die();
			}
		if(!file_put_contents("SystemInfo/Info".$row['node_id'].".txt",$_POST['system']))
		{
			$output['Fail']=1;
			$output['Type']=4;
			echo json_encode($output);
			mysql_close($con);
			die();
		}
	}
	else
	{
		$row = mysql_fetch_array($res);
		if($row['node_name']==$_POST['location'])//如果ip和名字都相同，认为是同一个节点，更新信息
		{
			$res = mysql_query("UPDATE node SET node_name = '".$_POST['location']."', longitude='".$_POST['longitude']."', latitude='".$_POST['latitude']."', remark= '".$_POST['remark']."', password = '".$_POST['password']."' WHERE ip = '".$_POST['ip']."'");
			if (!$res)
			{
				$output['Fail']=1;
				$output['Type']=2;
				echo json_encode($output);
				mysql_close($con);
				die();
			}
			//顺序是：cpu、memory、hd、network、uptime
			//delete file
			if (file_exists("SystemInfo/Info".$row['node_id'].".txt"))
				if(!unlink("SystemInfo/Info".$row['node_id'].".txt"))
				{
					$output['Fail']=1;
					$output['Type']=3;
					echo json_encode($output);
					mysql_close($con);
					die();
				}
			if(!file_put_contents("SystemInfo/Info".$row['node_id'].".txt",$_POST['system']))
			{
				$output['Fail']=1;
				$output['Type']=4;
				echo json_encode($output);
				mysql_close($con);
				die();
			}
		}
		else//如果不同，说明ip冲突，返回错误信息
		{
			$output['Fail']=1;
			$output['Type']=5;
			echo json_encode($output);
			mysql_close($con);
			die();
		}
	}
	mysql_close($con);
}
echo json_encode($output);
?>
