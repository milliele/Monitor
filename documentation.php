<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <!-- Sets initial viewport load and disables zooming  -->
    <meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>Documentation - HTTP-ICN Monitor</title>
    <link rel="shortcut icon" href="small.ico"/>
    <link rel="bookmark" href="small.ico"/>
    <!-- site css -->
    <style type="text/css">
		#allmap{width:100%;height:600px;}
	</style>
    <link rel="stylesheet" href="css/1.css">
    <link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,800,700,400italic,600italic,700italic,800italic,300italic" rel="stylesheet" type="text/css">
    
    <!-- javascript .js-->
    <script type="text/javascript" src="js/1.js"></script>
    <script type="text/javascript" src="js/post.js"></script>
    </script>
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
                        <li><a class="nav-link" href="monitor.php">Monitor</a></li>
                        <li><a class="nav-link current" href="documentation.php">Documentation</a></li>
                    </ul>
                </div>
            </div>
        </nav>
	<!--header-->
	<div class="topic">
		<div class="container">
			<div class="col-md-8">
			  <h3>HTTP-ICN Monitor使用说明</h3>
			  <h4>How to make use of ICN Monitor</h4>
			</div>
		</div>
		<div class="topic__infos">
			<div class="container">
		    		HTTP-ICN Monitor可以跟踪HTTP-ICN节点FIB表的变化，并显示某特定prefix在每个HTTP-ICN节点中的下一跳（如果有的话）。
		  	</div>
		</div>
	</div>
    </div>
    <!--Document-->
	<div class="container documents">
    	<!--About Router -->
        <div class="example">
        	<h2 class="example-title">About Router</h2>
            <div class="row">
            	<div class="col-md-12 col-xs-12 col-sm-12 col-lg-12">
                    <p>每个Router在<a href="conf_example.xml" target="_blank"><b>&ltconf.xml&gt</b></a>里配置，具体的配置说明可以点击超链接查看。&nbsp;&nbsp;<span style="color:#C93756">(<a href="javascript:postwith('download.php',{'target':'conf_example.xml','name':'conf.xml'});">Click here to Download</a>&nbsp;&nbsp;&nbsp;&nbsp;Monitor的地址为：<b>202.112.237.226</b>.)</span></p>
                    <p>要向HTTP-ICN Monitor注册一个新的Router，您需要：<br>
                    <div class="alert alert-success alert-dismissable">
                    	<strong>注意！</strong>HTTP-ICN Monitor可监测的Router必须在Linux环境下配置，否则可能无法读取Router信息。
                    </div>
			<ol>
				<li>确保您的机器可以连接到网络，<b>下载</b> <a href="javascript:postwith('download.php',{'target':'router.tar.gz'});">router.tar.gz</a>. 该压缩包里的send.py程序会将Router的FIB表和Router的机器信息发送到HTTP-ICN Monitor</li>
				<li>打开终端，进入<b>router.tar.gz</b>所在的文件夹，输入如下命令<b>解压</b>:
					<pre>
    $ tar xvfvz router.tar.gz
                    </pre>
				</li>
				<li>在&ltconf.xml&gt里<b>配置</b>好Router，具体细节请查看上述链接。
                <div class="alert alert-info alert-dismissable">
                    	<strong>注意！</strong><b>&ltlogging.conf&gt</b>, <b>&ltconf.xml&gt</b> and <b>&ltsend.py&gt</b>必须在<b>同一</b>目录下
                    </div></li>
				<li>要使&ltsend.py&gt能在<b>后台</b>运行, 在终端输入:
					<pre>
    $ python &ltsend.py's path>/send.py
                    </pre>
                     <div class="alert alert-info alert-dismissable">
                    	<strong>注意！</strong>要运行<b>&ltsend.py&gt</b>，您需要安装Python2.x，Python3可能不兼容。
                    </div>
				<li>要使&ltsend.py&gt<b>开机自启动</b>，您需要修改&lt/etc/rc.local&gt:
					<pre>
    $ sudo gedit /etc/rc.local
                    </pre>在rc.local的<b>'exit 0'</b>前加入:
					<pre>
    nohup python &ltsend.py's path>/send.py >/dev/null 2>&1 &
                    </pre>或者
<pre>
    setsid python &ltsend.py's path>/send.py
                    </pre></li></ol>
		<span style="color:#C93756">如果您仍然无法在HTTP-ICN Monitor的<b>Router List</b>里看见您的Router, 请参考<a href="#1">FAQ</a></span></p>
                    <p>FIB表的格式如下: <a href="javascript:postwith('download.php',{'target':'FIB_example.txt'});">&ltFIB_example.txt&gt</a></p>
                    <pre>
    default
    &lt;content_name&gt; &lt;next_hop&gt;
    Baidu.com 172.31.252.33
                    </pre>
		</div>
            </div>
	</div>
        <div class="example">
        	<h2 class="example-title">About Monitor</h2>
            <div class="row">
            	<div class="col-md-12 col-xs-12 col-sm-12 col-lg-12">
                    <ul><li><b>侧边栏：</b><ul><li><b>Router List：</b>显示当前已经注册到HTTP-ICN Monitor的所有Router, <b>点击Router的名字</b>就可以显示该Router的相关信息。
                    <div class="alert alert-info alert-dismissable">
                    	<strong>注意！</strong>在Router向HTTP-ICN Monitor发送信息的过程中可能出错。 如果Router的显示出现了问题，在它名字的右侧将会提示，<b>点击Router的名字</b>就可以查看具体出错描述，描述的请参考<a href="#diserror">FAQ</a><br>可以选择在服务器上删除错误的Router，需要输入您配置Router时配置的密码，配置的密码请<b>不要使用</b>中文或者一些少见的、非法的字符，否则可能验证出错<br>服务器每<b>周一</b>的<b>23:59</b>会删除异常的节点，请<b>及时处理</b>
                    </div></li>
                    <li><b>Router Infomation：</b>显示该Router的机器的<strong>内存信息</strong>, <strong>CPU信息</strong>, <strong>硬盘信息</strong>, <strong>网络流量</strong>和<strong>Router的消逝时间</strong>。将鼠标悬停在<b>&ltdetailed&gt</b> tag上就可以查看更具体的信息。具体信息描述请参考：<a href="#info">FAQ</a></li>
                    <li><b>Prefix List：</b>显示当前选中Router的FIB表记录的除Default以外的entry中记录的prefix。<strong>选择一个entry</strong>您就可以在右边地图中看到该prefix的相关信息。</li></ul>
                    <li><b>Map: </b><ul>Router List里的每个Router在地图中都<strong>用点标注</strong>了出来，点击地图上的点和在侧边栏里点击相应Router的名字是<strong>同样的效果</strong><br>有Prefix被选中时，它的相关信息会在地图中显示，如果RouterA指向RouterB，说明<strong>在RouterA的FIB里，关于选定Prefix的下一跳是RouterB</strong>。</ul></li></ul>
		</div>
            </div>
	</div>
	<div id="1" class="example">
        	<h2 class="example-title">FAQ</h2>
            <div class="row">
            	<div class="col-md-12 col-xs-12 col-sm-12 col-lg-12">
                    <ol>
			<li><b>我已按照上述操作，但仍未能在Router List里看见我的Router。</b><br>
				查看<b>&ltlogging.conf&gt</b>, <b>&ltconf.xml&gt</b>以及您要发送的FIB文件的权限，它们需要至少是可读的。</li><br>
            <li id="diserror"><strong>Router错误描述</strong><table class="table">
                            <thead>
                                <tr>
                                    <th>Description</th><th>Type</th><th>Reason</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr><td>FIB not exist</td><td><span style="color:#C93756">Error</span></td><td>该Router的FIB文件不存在</td></tr>
                                <tr><td>Out-of-date FIB</td><td><span style="color:#f6bb42">Warning</span></td><td>该Router的FIB文件已经超过30min没有更新了，可能您机机器上的send.py程序运行出错，请查看<strong>log</strong></span>文件夹下的<strong>&ltsend.log&gt</strong>文件。</td></tr>
                                <tr><td>Can not read FIB</td><td><span style="color:#C93756">Error</span></td><td>无法读取该Router的FIB文件，文件为空时也会出错，而正确的FIB文件至少有一行Default。</td></tr>
                                <tr><td>Systeminfo not exist</td><td><span style="color:#C93756">Error</span></td><td>该Router的系统信息文件不存在</td></tr>
                                <tr><td>Out-of-date System Info</td><td><span style="color:#f6bb42">Warning</span></td><td>该Router的系统信息文件已经超过30min没有更新了，可能您机机器上的send.py程序运行出错，请查看<strong>log</strong></span>文件夹下的<strong>&ltsend.log&gt</strong>文件</td></tr>
                                <tr><td>Can not read System Info</td><td><span style="color:#C93756">Error</span></td><td>无法读取该Router的系统信息文件，文件为空时也会出错，请确保您是在Linux环境下运行，详情请查看<strong>log</strong></span>文件夹下的<strong>&ltsend.log&gt</strong>文件。</td></tr>
                            </tbody>              
                        </table></li>
			<li id="logerr"><b>日志错误描述</b><br>
				<table class="table">
                            <thead>
                                <tr>
                                    <th>Error Type</th><th>Description</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr><td>Configuration Error</td><td>配置文件错误，一般是缺少某项必须的参数</td></tr>
                                <tr><td>PHP Feedback Error</td><td>服务器反馈回来的错误，大部分情况下是服务器出问题了，请联系我们：<span class="connect">milliele@pku.edu.cn</span></td></tr>
                                <tr><td>HTTP Error</td><td>获得的服务器返回的HTTP Response里的错误码</td></tr>
                                <tr><td>URL Error</td><td>未成成功获得HTTP Response时发生的错误，可能原因有：<ul><li>网络无连接，即本机无法上网</li><li>连接不到特定的服务器，请确保配置文件里Monitor地址配置正确</li><li>服务器不存在</li></ul></td></tr>
                                <tr><td>XML Error</td><td>读取配置文件时出错，请确保xml文件格式正确且合法</td></tr>
                                <tr><td>File Error</td><td>读取文件时出错，请确保FIB表存放位置正确，且FIB文件的权限允许读</td></tr>
                                <tr><td>Other Error</td><td>其它错误，配置文件语法不正确也有可能显示为此项</td></tr>
                            </tbody>              
                        </table></li>
             
                <li id="info"><strong>系统信息描述</strong><table class="table">
                            <thead>
                                <tr>
                                    <th>Name</th><th>Description</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr><td>IP</td><td>Router的IP地址</td></tr>
                                <tr><td>Memory</td><td><ul><li><strong>略：</strong>内存大小</li><li><strong>详：</strong>内存大小，剩余内存大小，虚存大小，已使用虚存大小</li></ul></td></tr>
                                <tr><td>CPU</td><td><ul><li><strong>略：</strong>CPU名称，核心数</li><li><strong>详：</strong>【每个逻辑核】逻辑核编号，物理核编号，CPU名字，CPU核心数，实际主频，二级缓存大小</li></ul></td></tr>
                                <tr><td>Harddisk</td><td><ul><li><strong>略：</strong>硬盘大小</li><li><strong>详：</strong>硬盘大小，硬盘已使用大小</li></ul></td></tr>
                                <tr><td>Network</td><td><ul><li><strong>略：</strong>总接收字节数，总发送字节数</li><li><strong>详：</strong>【每个网卡】【接收/发送】字节数，正确接收包数，错误包数，丢弃包数</li></ul></td></tr>
                                <tr><td>Uptime</td><td><ul><li><strong>略：</strong>开机时间（消逝时间）</li><li><strong>详：</strong>开机时间（消逝时间），空闲率</li></ul></td></tr>
                            </tbody>              
                        </table></li>
			<li><b>修改了rc.local文件仍然无法使send.py开机自启动？</b><br>请输入send.py的路径时不要使用【~】，而使用绝对路径例如：【/home/xxx/send.py】</li><br>
		    </ol>
            <div class="alert alert-info alert-dismissable">
                如仍未解决您的问题，请联系我们：<span class="connect">milliele@pku.edu.cn</span>
            </div>
		</div>
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


