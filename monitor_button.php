<?php include 'readinfo.php';?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <!-- Sets initial viewport load and disables zooming  -->
    <meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>Monitor - HTTP-ICN Monitor</title>
    <link rel="shortcut icon" href="small.ico"/>
    <link rel="bookmark" href="small.ico"/>
    <!-- site css -->
    <style type="text/css">
		#allmap{width:100%;height:1600px;}
		.panel-primary>.panel-body
		{
			max-height:500px;
			overflow:auto;
		}
		.btn-refresh
		{
			
    </style>
    <link rel="stylesheet" href="css/1.css">
    <link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,800,700,400italic,600italic,700italic,800italic,300italic" rel="stylesheet" type="text/css">
    <!-- javascript .js-->
    <script type="text/javascript" src="js/1.js"></script>
    <script type="text/javascript" src="http://api.map.baidu.com/api?v=2.0&amp;ak=ATBGX8WfpHMD3C8EL8xTOfvR"></script>
    <script type="text/javascript" src="http://api.map.baidu.com/library/CurveLine/1.5/src/CurveLine.min.js"></script>
    <script type="text/javascript" src="js/my.js"></script>
    <script type="text/javascript" src="js/map.js"></script>
    <script type="text/javascript">
		//$(function () { $("[data-toggle='popover']").popover(); });
		$(function () { $("[data-toggle='tooltip']").tooltip(); });
		//displayNode($('.nodelist .active span').attr('id'));
		//displayContent($('.prefixlist .active span').attr('id'));
    </script>
	<?php
		//连接数据库并选中数据库
		$con = mysql_connect("localhost","root","11");
		if (!$con)
		{
			die('Could not connect: ' . mysql_error());
		}

		mysql_select_db("ICN_monitor", $con);

		$pku = -1;
		$thu = -1;
		//如果有这两个学校，记录并放在前面
		$res = mysql_query("SELECT * FROM node WHERE node_name = '北京大学'");
		if(mysql_num_rows($res)!=0) $pku=mysql_fetch_array($res);
		$res = mysql_query("SELECT * FROM node WHERE node_name = '清华大学'");
		if(mysql_num_rows($res)!=0) $thu=mysql_fetch_array($res); 
	?>
  </head>
  <body style="background-color: #f1f2f6;">
  	<!--Header-->
    <div class="docs-header">
    	<!--nav-->
        <nav class="navbar navbar-default navbar-custom" role="navigation">
            <div class="container">
                <div class="navbar-header">
                	<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                	<a class="navbar-brand" href="monitor.php"><img src="img/logo.png" height="40"></a>
                </div>
                <div class="collapse navbar-collapse">
                    <ul class="nav navbar-nav navbar-right">
                        <li><a class="nav-link current" href="monitor.php">Monitor</a></li>
                        <li><a class="nav-link" href="documentation.html">Documentation</a></li>
                    </ul>
                </div>
            </div>
        </nav>
    </div>
    <!--Document-->
    <div class="container documents">
    	<div class="row">
        	<!--侧边栏，用来选节点-->
        	<div class="col-md-3 col-lg-3">
            	<!-- 节点列表 -->
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h3 class="panel-title">ICN Router List</h3>
                    </div>
                    <div class="panel-body">
                        <div class="list-group nodelist">
                            <?php //显示collapse以及读取节点的相关信息
								if($pku != -1)//如果有北大节点
								{
									echo '<a  title="'.$pku['node_name'].'" data-container="body" data-toggle="popover" data-placement="right" data-content="'.$pku['remark'].'" href="javascript:displayNode(';
									echo $pku['node_id'];
									echo ');" id="'.$pku['node_id'].'" class="list-group-item active"><span>';
                                    					echo $pku['node_name'];
									echo '</span></a>';
								}
								if($thu != -1)//如果有清华节点
								{
									echo '<a  title="'.$thu['node_name'].'" data-container="body" data-toggle="popover" data-placement="right" data-content="'.$thu['remark'].'" href="javascript:displayNode(';
									echo $thu['node_id'];
									if($pku == -1) echo ');" id="'.$thu['node_id'].'" class="list-group-item active"><span>';
									else echo ')" id="'.$thu['node_id'].'" class="list-group-item"><span>';
                                    					echo $thu['node_name'];
									echo '</span></a>';
								}
								//其他节点
								$re233 = mysql_query("SELECT * FROM node");
								$i = 0;
								while($row = mysql_fetch_array($re233)){
									$node = $row['node_name'];
									$node_num = $row['node_id'];
									//显示表项，如果北大清华显示过了就不显示
									if(($pku == -1 || $node_num!=$pku['node_id'])&&( $thu==-1 || $node_num!=$thu['node_id']))
									{
										echo '<a  title="'.$row['node_name'].'" data-container="body" data-toggle="popover" data-placement="right" data-content="'.$row['remark'].'" href="javascript:displayNode(';
										echo $row['node_id'];
										if($pku == -1 && $thu == -1 && $i == 0) echo ');" id="'.$row['node_id'].'" class="list-group-item active"><span>';
									else echo ')" id="'.$row['node_id'].'" class="list-group-item"><span>';
										echo $row['node_name'];
										echo '</span></a>';
									}
									++$i;
								}
							?>
						</div>
                    </div>
                </div>
                <!-- 节点信息 -->
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h3 class="panel-title">Router Infomation<button type="button" class="btn btn-primary btn-block" style="display:inline-block;padding:3px 7px;font-size:12px;font-weight:700;line-height:1;color:#fff;text-align:center;width:auto;min-width:10px;white-space:nowrap;vertical-align:baseline;border-radius:10px;float:right" onclick="displayinfo($('.nodelist .active').attr('id'));return true;">Refresh</button></h3>
                    </div>
		    <div class="panel-body">
			Mouse hovering on <b>&ltdetailed&gt</b> tag to get more infos.
		    </div>
                    <ul class="list-group" id="infoyo">
                    </ul>
                </div>
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h3 class="panel-title">Prefix List<button type="button" class="btn btn-primary btn-block " style="display:inline-block;padding:3px 7px;font-size:12px;font-weight:700;line-height:1;color:#fff;text-align:center;width:auto;min-width:10px;white-space:nowrap;vertical-align:baseline;border-radius:10px;float:right" onclick="displayfib($('.nodelist .active').attr('id'));return true;">Refresh</button></h3>
					
                    </div>
                    <div class="panel-body">
			Displays only first 24 characters, mouse hovering on each item to get complete name.
			<br/><br/>
                        <div class="list-group prefixlist">
			</div>
                    </div>
                </div>
            </div>
            <!--主栏，调用百度地图API-->
            <div class="col-md-9 col-lg-9">
            	<div id="allmap"></div>
          </div>
        </div>
	</div>
    <!--Footer-->
    <div class="site-footer">
      <div class="container">
        <!--<hr class="dashed" />-->
        <div class="copyright clearfix">
          <p><b>ICN Monitor</b>&nbsp;&nbsp;&nbsp;&nbsp;<a href="monitor.php">Monitor</a>&nbsp;&bull;&nbsp;<a href="documentation.html">Documentation</a><!--&nbsp;&bull;&nbsp;<a href="free-psd.html">Free PSD</a>&nbsp;&bull;&nbsp;<a href="color-picker.html">Color Picker</a>--></p>
          <p>&copy; 2016-<?php echo date("Y")?> NetVIP Lab in Peking University. All rights reserved.</p>
          <p>Advise is welcome! Email us at <span class="connect">milliele@pku.edu.cn</span></p>
        </div>
      </div>
    </div>
  </body>
</html>

<div class="hide" id="memory">
<table class="table">
	<thead>
		<tr>
			<th>Memory_Total</th><th>Memory_Free</th><th>Visual Memory_Total</th><th>Visual Memory_Used</th>
		</tr>
	</thead>
	<tbody>
	</tbody>              
</table>
</div>
<div class="hide" id="cpu">
<table class="table">
	<thead>
		<tr>
			<th>Processor</th><th>Core ID</th><th>CPU name</th><th>CPU Cores</th><th>CPU MHz</th><th>L2 Cache</th>
		</tr>
	</thead>
	<tbody>
	</tbody>              
</table>
</div>
<div class="hide" id="harddisk">
<table class="table">
	<thead>
		<tr>
			<th>Total</th><th>Used</th>
		</tr>
	</thead>
	<tbody>
	</tbody>              
</table>
</div>
<div class="hide" id="network">
<table class="table">
	<thead>
		<tr>
			<th rowspan="2">Interface</th><th colspan="4">Receive</th><th colspan="4">Transmit</th>
		</tr>
		<tr>
			<th>Bytes</th><th>Packets</th><th>Errors</th><th>Drops</th><th>Bytes</th><th>Packets</th><th>Errors</th><th>Drops</th>
		</tr>
	</thead>
	<tbody>
	</tbody>              
</table>
</div>
<div class="hide" id="uptime">
<table class="table">
	<thead>
		<tr>
			<th>Time</th><th>Free Rate</th>
		</tr>
	</thead>
	<tbody>
	</tbody>              
</table>
</div>


<script type="text/javascript">
var map = new BMap.Map("allmap");
var PKU_point = new BMap.Point(116.319677,39.996656);//北京大学
map.centerAndZoom(PKU_point, 14);
//map.addControl(new BMap.NavigationControl());
map.enableScrollWheelZoom();

var mapType1 = new BMap.MapTypeControl({mapTypes: [BMAP_NORMAL_MAP,BMAP_HYBRID_MAP]});
var mapType2 = new BMap.MapTypeControl({anchor: BMAP_ANCHOR_TOP_LEFT});

var overView = new BMap.OverviewMapControl();

var pointsip = new Array(); // 以ip为索引的点集
var points = new Array(); // 以id为索引的点集
var lines = new Array(); //当前地图中线的集合
var arrows = new Array(); //当前地图中点的集合
var displaypoints = new Array();

function addMarker(point,num){
	var marker = new BMap.Marker(point);
	map.addOverlay(marker);
	marker.addEventListener("click", function(){
		$('#'+num+' span').click();
	});
}

map.addEventListener("zoomend", function () {
	clear();//清除
	id = $('.prefixlist .active').attr('id');
	for(id in points) //对每个节点，查找其fib表里有没有该内容，如果有那么显示线
	{
		if(content in fibs[id])
		{		
			addArrowLine(map, points[id]['lng'], points[id]['lat'], pointsip[fibs[id][content]['next']]['lng'], pointsip[fibs[id][content]['next']]['lat'], "blue", 5, 0.8, false);
			displaypoints.push(points[id]);
			displaypoints.push(pointsip[fibs[id][content]['next']]);
		}
	}
});

var tmp_point;
<?php //把所有结点标出来
	$re233 = mysql_query("SELECT * FROM node");
	while($row = mysql_fetch_array($re233))
	{
		echo 'tmp_point = new BMap.Point('.$row['longitude'].','.$row['latitude'].');';
		echo "pointsip['".$row['ip']."'] = tmp_point;";
		echo "points[".$row['node_id']."] = tmp_point;";
		echo "addMarker(tmp_point,".$row['node_id'].");";
	}
?>

displayNode($('.nodelist .active').attr('id'));
</script>

<?php
	mysql_close($con);
?>
