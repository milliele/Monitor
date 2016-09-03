<?php
$output['Fail']=0;
if(isset($_POST['event']) && $_POST['event']=='sendfibup')
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
	$row = mysql_fetch_array($res);
	if (file_exists("FIBup/FIB".$row['node_id'].".txt") && !unlink("FIBup/FIB".$row['node_id'].".txt"))
	{
		$output['Fail']=1;
		$output['Type']=3;
		echo json_encode($output);
		mysql_close($con);
		die();
	}
	if(!file_put_contents("FIBup/FIB".$row['node_id'].".txt",$_POST['file']))
	{
		$output['Fail']=1;
		$output['Type']=4;
		echo json_encode($output);
		mysql_close($con);
		die();
	}
	mysql_close($con);
}
echo json_encode($output);
?>
