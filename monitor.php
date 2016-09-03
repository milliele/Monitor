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
    <script type="text/javascript" src="js/post.js"></script>
    <script type="text/javascript" src="js/jquery.form.js"></script>
    <script type="text/javascript" src="js/md5.js"></script>
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
                        <li><a class="nav-link" href="documentation.php">Documentation</a></li>
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
                        <h3 class="panel-title">ICN Router List<button type="button" class="btn btn-primary btn-block " style="display:inline-block;padding:3px 7px;font-size:12px;font-weight:700;line-height:1;color:#fff;text-align:center;width:auto;min-width:10px;white-space:nowrap;vertical-align:baseline;border-radius:10px;float:right" onclick="loadJs('js/my.js');displaylist();displayNode($('.nodelist .active').attr('id'));activate();">Refresh</button></h3>
                    </div>
                    <div class="panel-body">
                        <div class="list-group nodelist">
			</div>
                    </div>
                </div>
                <!-- 节点信息 -->
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h3 class="panel-title">Router Infomation</h3>
                    </div>
		    <div class="panel-body">
			Mouse hovering on <b>&ltdetailed&gt</b> tag to get more infos.
		    </div>
                    <ul class="list-group">
			<li class="list-group-item"><b>IP: </b><span id="ip"></span></li>
			<li class="list-group-item"><b>Memory: </b><span id="mem"></span><span class="badge" title="" data-html=true data-container="body" data-toggle="popover" data-placement="right" data-content="">Detailed</span></li>
			<li class="list-group-item"><b>CPU: </b><span class="badge" data-html=true title="" data-container="body" data-toggle="popover" data-placement="right" data-content="">Detailed</span><br><span id="c"></span></li>
			<li class="list-group-item"><b>Harddisk: </b><span id="hd"></span><span class="badge" title="" data-html=true data-container="body" data-toggle="popover" data-placement="right" data-content="">Detailed</span></li>
			<li class="list-group-item"><b>Network: </b><span class="badge" title="" data-container="body" data-toggle="popover" data-html=true data-placement="right" data-content="">Detailed</span><br>&nbsp;&nbsp;&nbsp;&nbsp;<span id="net"></span></li>
			<li class="list-group-item"><b>Uptime: </b><span class="badge" title="" data-container="body" data-toggle="popover" data-placement="right" data-html=true htmldata-content="">Detailed</span><br>&nbsp;&nbsp;&nbsp;&nbsp;<span id="upt"></span></li>
                    </ul>
                </div>
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h3 class="panel-title">Prefix List<button type="button" class="btn btn-primary btn-block " style="display:inline-block;padding:3px 7px;font-size:12px;font-weight:700;line-height:1;color:#fff;text-align:center;width:auto;min-width:10px;white-space:nowrap;vertical-align:baseline;border-radius:10px;float:right" onclick="loadJs('js/my.js');displayfib($('.nodelist .active').attr('id'));activate();return true;">Refresh</button></h3>		
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
          <p><b>ICN Monitor</b>&nbsp;&nbsp;&nbsp;&nbsp;<a href="monitor.php">Monitor</a>&nbsp;&bull;&nbsp;<a href="documentation.php">Documentation</a><!--&nbsp;&bull;&nbsp;<a href="free-psd.html">Free PSD</a>&nbsp;&bull;&nbsp;<a href="color-picker.html">Color Picker</a>--></p>
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
<button class="hide" id="Alert" data-toggle="modal" data-target="#myModal"></button>
<button class="hide" id="Confirm" data-toggle="modal" data-target="#Conf"></button>
<button class="hide" id="Ensure" data-toggle="modal" data-target="#ensu"></button>
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" 
   aria-labelledby="myModalLabel" aria-hidden="true">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" 
               data-dismiss="modal" aria-hidden="true">
                  &times;
            </button>
            <h4 class="modal-title" id="myModalLabel">
               Sorry :(
            </h4>
         </div>
         <div class="modal-body">
            There is something <span style="color:#C93756">WRONG</span> with this router.<br><br>
	    <p id="reason"></p>
	    Do you want to delete it ?
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-default" 
               data-dismiss="modal">Cancel
            </button>
            <button type="button" class="btn btn-primary" onClick="$('#Confirm').click()">
               Yes
            </button>
         </div>
      </div><!-- /.modal-content -->
</div><!-- /.modal -->
<div class="modal fade" id="Conf" tabindex="-1" role="dialog" 
   aria-labelledby="myModalLabel" aria-hidden="true">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" 
               data-dismiss="modal" aria-hidden="true">
                  &times;
            </button>
            <h4 class="modal-title" id="ConfLabel">
               Enter your password
            </h4>
         </div>
         <div class="modal-body">
	    Please enter your password to delete the router:<br>
	    <input type="password" id='pass' class="form-control" placeholder="Please enter your password">
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-default" 
               data-dismiss="modal">Cancel
            </button>
            <button type="button" class="btn btn-primary" onClick="Checkpassword();">
               Yes
            </button>
         </div>
      </div><!-- /.modal-content -->
</div><!-- /.modal -->
<div class="modal fade" id="ensu" tabindex="-1" role="dialog" 
   aria-labelledby="myModalLabel" aria-hidden="true">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" 
               data-dismiss="modal" aria-hidden="true">
                  &times;
            </button>
            <h4 class="modal-title" id="ensuLabel">
               Ensure
            </h4>
         </div>
         <div class="modal-body">
	    Are you sure to delete this router ?
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel
            </button>
            <button type="button" class="btn btn-primary" data-dismiss="modal" onClick="ErrorAlert();">
               Yes
            </button>
         </div>
      </div><!-- /.modal-content -->
</div><!-- /.modal -->


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

function ErrorAlert()
{
	execute('clean.php', {'event':'clean','id':$('.nodelist .active').attr('id')});
}

function addMarker(point,num){
	var marker = new BMap.Marker(point);
	map.addOverlay(marker);
	marker.addEventListener("click", function(){
		$('#'+num+' span').click();
	});
}

map.addEventListener("zoomend", function () {
	clear();//清除
	content = $('.prefixlist .active').attr('id');
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

var nodeactive=-1; var contentactive='';

displaylist();
displayNode($('.nodelist .active').attr('id'));
activate();
var timeId = setInterval("loadJs('js/my.js');displayinfo($('.nodelist .active').attr('id'));checkrouter();",2000);
var timeId2 = setInterval("loadJs('js/my.js');displaylist();displayNode($('.nodelist .active').attr('id'));activate();",86400000);
</script>

<?php
	mysql_close($con);
?>
