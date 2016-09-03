<?php
	$output['Fail']=-1;
	if(isset($_POST['event']) && $_POST['event']=='clean')
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
		
		if($_POST['id']==-1)
		{
			$result = mysql_query("SELECT * FROM node");
			if (!$result)
			{
				$output['Fail']=1;
				$output['Type']=1;
				echo json_encode($output);
				mysql_close($con);
				die();
			}
			while($row = mysql_fetch_array($result))
			{
				$node = $row['node_name'];
				$node_num = $row['node_id'];
				$fib = "FIBup/FIB".$node_num.".txt";
				if(!file_exists($fib) || filemtime($fib)<(time()-24*60*60))//如果文件更新时间比当前时间晚24小时
				{
					if($output['Fail']==-1)
					{
						$output['Fail']=0;
						$output['content']='Successfully delete router ';
					}
					//delete file
					if (file_exists($fib) && !unlink($fib))
					{
						$output['Fail']=1;
						$output['Type']=3;
						echo json_encode($output);
						mysql_close($con);
						die();
					}
					$info = 'SystemInfo/Info'.$node_num.".txt";
					//delete file
					if (file_exists($info) && !unlink($info))
					{
						$output['Fail']=1;
						$output['Type']=3;
						echo json_encode($output);
						mysql_close($con);
						die();
					}
					//delete alive message
					if (!mysql_query("DELETE FROM node WHERE node_id = ".$node_num))
					{
						$output['Fail']=1;
						$output['Type']=2;
						echo json_encode($output);
						mysql_close($con);
						die();
					}
					$output['content']=$output['content'].$node."(".$node_num.") ";
					//如果没有结点了，把编号清空
					$res = mysql_query("SELECT * FROM node");
					if(mysql_num_rows($res)==0) mysql_query("TRUNCATE table node");
				}
			}
		}
		else
		{
			$result = mysql_query("SELECT * FROM node WHERE node_id = ".$_POST['id']);
			if (!$result)
			{
				$output['Fail']=1;
				$output['Type']=1;
				echo json_encode($output);
				mysql_close($con);
				die();
			}
			$row = mysql_fetch_array($result);
			$node = $row['node_name'];
			$node_num = $row['node_id'];
			$fib = "FIBup/FIB".$node_num.".txt";
			if($output['Fail']==-1)
			{
				$output['Fail']=0;
				$output['content']='Successfully delete router ';
			}
			//delete file
			if (file_exists($fib) && !unlink($fib))
			{
				$output['Fail']=1;
				$output['Type']=3;
				echo json_encode($output);
				mysql_close($con);
				die();
			}
			$info = 'SystemInfo/Info'.$node_num.".txt";
			//delete file
			if (file_exists($info) && !unlink($info))
			{
				$output['Fail']=1;
				$output['Type']=3;
				echo json_encode($output);
				mysql_close($con);
				die();
			}
			//delete alive message
			if (!mysql_query("DELETE FROM node WHERE node_id = ".$node_num))
			{
				$output['Fail']=1;
				$output['Type']=2;
				echo json_encode($output);
				mysql_close($con);
				die();
			}
			$output['content']=$output['content'].$node."(".$node_num.") ";
			//如果没有结点了，把编号清空
			$res = mysql_query("SELECT * FROM node");
			if(mysql_num_rows($res)==0) mysql_query("TRUNCATE table node");
		}	
		mysql_close($con);
	}
	echo json_encode($output);
?>
