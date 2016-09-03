//一个能画出箭头来的函数
function addArrowLine(map, from_x, from_y, to_x, to_y, color, weight, opacity, isdashed)  
{  
    var line_style = {strokeColor:color, strokeWeight:weight, strokeOpacity:opacity};  
      
    //line  
    var polyline = new BMap.Polyline([new BMap.Point(from_x, from_y), new BMap.Point(to_x, to_y)], line_style);  
      
    //if(onclick_function != null)  
    //    polyline.addEventListener("click", onclick_function);  
      
    if(isdashed)   
        polyline.setStrokeStyle("dashed");  
  
    map.addOverlay(polyline);
	lines.push(polyline);
      
    //arrow  
    var length = 10;  
    var angleValue = Math.PI/7;  
    var linePoint = polyline.getPath();  
    var arrowCount = linePoint.length;  
    for(var i = 1; i < arrowCount; i++)  
    {  
        var pixelStart = map.pointToPixel(linePoint[i - 1]);  
        var pixelEnd = map.pointToPixel(linePoint[i]);  
        var angle = angleValue;  
        var r = length;  
        var delta = 0;  
        var param = 0;  
        var pixelTemX, pixelTemY;  
        var pixelX, pixelY, pixelX1, pixelY1;  
        if(pixelEnd.x - pixelStart.x == 0)  
        {  
            pixelTemX = pixelEnd.x;  
            if(pixelEnd.y > pixelStart.y)  
            {  
                pixelTemY = pixelEnd.y - r;  
            }  
            else  
            {  
                pixelTemY = pixelEnd.y + r;  
            }             
            pixelX = pixelTemX - r * Math.tan(angle);  
            pixelX1 = pixelTemX + r * Math.tan(angle);  
            pixelY = pixelY1 = pixelTemY;  
        }  
        else  
        {  
            delta = (pixelEnd.y - pixelStart.y) / (pixelEnd.x - pixelStart.x);  
            param = Math.sqrt(delta * delta + 1);  
            if((pixelEnd.x - pixelStart.x) < 0)  
            {  
                pixelTemX = pixelEnd.x + r / param;  
                pixelTemY = pixelEnd.y + delta * r / param;  
            }  
            else  
            {  
                pixelTemX = pixelEnd.x - r / param;  
                pixelTemY = pixelEnd.y - delta * r / param;  
            }  
            pixelX = pixelTemX + Math.tan(angle) * r * delta / param;  
            pixelY = pixelTemY - Math.tan(angle) * r / param;  
            pixelX1 = pixelTemX - Math.tan(angle) * r * delta / param;  
            pixelY1 = pixelTemY + Math.tan(angle) * r / param;  
        }  
        var pointArrow = map.pixelToPoint(new BMap.Pixel(pixelX, pixelY));  
        var pointArrow1 = map.pixelToPoint(new BMap.Pixel(pixelX1, pixelY1));  
        var Arrow = new BMap.Polyline([pointArrow, linePoint[i], pointArrow1], line_style);  
        map.addOverlay(Arrow); 
		arrows.push(Arrow);
    }  
}

function clear()
{
	for(line in lines)
	{
		map.removeOverlay(lines[line]);
	}
	for(arrow in arrows)
	{
		map.removeOverlay(arrows[arrow]);
	}
	displaypoints.splice(0,displaypoints.length);
}

function checkrouter()
{
	for(id in nodes)
	{
		if(warning[id]['err']==1)
		{
			$('#'+id+' .badge').html("Error");
			$('#'+id+' .badge').addClass("badge-danger");
		}
		else if(warning[id]['err']==2)
		{
			$('#'+id+' .badge').html("Warning");
			$('#'+id+' .badge').addClass("badge-warning");
		}
		else 
		{
			$('#'+id+' .badge').html("");
			$('#'+id+' .badge').addClass("");
		}		
	}
}

function displaylist()
{
	if(!(nodeactive in nodes))//如果是第一次显示
	{
		if(pkuid in nodes)
		{
			nodeactive=pkuid;
		}
		else if (thuid in nodes) nodeactive=thuid;
		else for(id in nodes)
		{
			nodeactive=id;
			break;
		}
	}
	var text='';
	if(pkuid in nodes)//如果有北大节点
	{
		text+= '<a  title="'+nodes[pkuid]['name']+'" data-container="body" data-toggle="popover" data-placement="right" data-content="'+nodes[pkuid]['remark']+'" href="javascript:displayNode('+pkuid+');" id="'+pkuid+'" class="list-group-item router';
		if(nodeactive==pkuid) text+=' active';
		text+='"><span>'+nodes[pkuid]['name']+'</span><span class="badge"></span></a>';
	}
	if(thuid in nodes)//如果有thu节点
	{
		text+= '<a title="'+nodes[thuid]['name']+'" data-container="body" data-toggle="popover" data-placement="right" data-content="'+nodes[thuid]['remark']+'" href="javascript:displayNode('+thuid+');" id="'+thuid+'" class="list-group-item router';
		if(nodeactive==thuid) text+=' active';
		text+='"><span>'+nodes[thuid]['name']+'</span><span class="badge"></span></a>';
	}
	for(id in nodes)
	{
		if((pkuid == -1 || id!=pkuid)&&( thuid==-1 || id!=thuid))
		{
			text+= '<a  title="'+nodes[id]['name']+'" data-container="body" data-toggle="popover" data-placement="right" data-content="'+nodes[id]['remark']+'" href="javascript:displayNode('+id+');" id="'+id+'" class="list-group-item router';
			if(nodeactive==id) text+=' active';
			text+='"><span>'+nodes[id]['name']+'</span><span class="badge"></span></a>';
		}
	}
	$('.nodelist').html(text);
	checkrouter();
}

function displayContent(content)
{
	//把原来的active清除，再把新点的active加上
	$('.prefixlist .active').removeClass("active");
	contentactive = content;
	$('#'+content).addClass("active");
	clear();//清除
	for(id in points) //对每个节点，查找其fib表里有没有该内容，如果有那么显示线
	{
		if(content in fibs[id] && fibs[id][content]['next'] in pointsip)
		{		
			addArrowLine(map, points[id]['lng'], points[id]['lat'], pointsip[fibs[id][content]['next']]['lng'], pointsip[fibs[id][content]['next']]['lat'], "blue", 5, 0.8, false);
			displaypoints.push(points[id]);
			displaypoints.push(pointsip[fibs[id][content]['next']]);
		}
	}
}

function loadJs(file) {
            var head = $("head").remove("script[role='reload']");
            $("<scri" + "pt>" + "</scr" + "ipt>").attr({ role: 'reload', src: file, type: 'text/javascript' }).appendTo(head);
}

function displayinfo(id)
{
	loadJs("js/my.js");
	//显示相关信息
	//IP
	$('#ip').html(infos[id]['ip']);
	//Memory
	if(infos[id]['mem']!=null)
	{
		$('#mem').html((infos[id]['mem']['MemTotal']/1073741824).toFixed(2)+'GB');
		$('#memory tbody').html('<tr><td>'+(infos[id]['mem']['MemTotal']/1073741824).toFixed(2)+'GB</td><td>'+(infos[id]['mem']['MemFree']/1073741824).toFixed(2)+'GB</td><td>'+(infos[id]['mem']['VmallocTotal']/1073741824).toFixed(2)+'GB</td><td>'+(infos[id]['mem']['VmallocUsed']/1073741824).toFixed(2)+'GB</td></tr>');
		$('#mem').siblings(".badge").attr("data-content",function() {  
			      return $("#memory").html();  
			    });
	}
	else $('#mem').html("");
	//CPU
	if(infos[id]['cpu']!=null)
	{
		if(0 in infos[id]['cpu']) $('#c').html(infos[id]['cpu'][0]['model name']+' / '+infos[id]['cpu'][0]['cpu cores']+'cores');	
		var con="";
		for(i in infos[id]['cpu'])
		{
			con+='<tr><td>'+infos[id]['cpu'][i]['processor']+'</td><td>'+infos[id]['cpu'][i]['core id']+'</td><td>'+infos[id]['cpu'][i]['model name']+'</td><td>'+infos[id]['cpu'][i]['cpu cores']+'</td><td>'+infos[id]['cpu'][i]['cpu MHz']+'</td><td>'+'</td><td>'+infos[id]['cpu'][i]['cache size']+'</td><td>'+'</td></tr>';
		}
		$('#cpu tbody').html(con);
		$('#c').siblings(".badge").attr("data-content",function() {  
			      return $("#cpu").html();  
			    });
	}
	else $('#c').html("");
	//Harddisk
	if(infos[id]['hd']!=null)
	{
		$('#hd').html((infos[id]['hd']['capacity']/1073741824).toFixed(2)+'GB');
		$('#harddisk tbody').html('<tr><td>'+(infos[id]['hd']['capacity']/1073741824).toFixed(2)+'GB</td><td>'+(infos[id]['hd']['used']/1073741824).toFixed(2)+'GB</td></tr>');
		$('#hd').siblings(".badge").attr("data-content",function() {  
			      return $("#harddisk").html();  
			    });
	}
	else $('#hd').html("");
	//Network
	if(infos[id]['net']!=null)
	{
		var totalr=0,totalt=0,con="";
		for(i in infos[id]['net'])
		{
			totalr+=infos[id]['net'][i]['ReceiveBytes'];
			totalt+=infos[id]['net'][i]['TransmitBytes'];
			con+='<tr><td>'+infos[id]['net'][i]['interface']+'</td><td>'+infos[id]['net'][i]['ReceiveBytes']+' B</td><td>'+infos[id]['net'][i]['ReceivePackets']+' packets</td><td>'+infos[id]['net'][i]['ReceiveErrs']+' packets</td><td>'+infos[id]['net'][i]['ReceiveDrop']+' packets</td><td>'+infos[id]['net'][i]['TransmitBytes']+' B</td><td>'+infos[id]['net'][i]['TransmitPackets']+' packets</td><td>'+infos[id]['net'][i]['TransmitErrs']+' packets</td><td>'+infos[id]['net'][i]['TransmitDrop']+' packets</td><td></tr>';
		}
		$('#net').html('Receive: '+totalr+' Bytes<br>&nbsp;&nbsp;&nbsp;&nbsp;Transmit: '+totalt+' Bytes');	
		$('#network tbody').html(con);
		$('#net').siblings(".badge").attr("data-content",function() {  
			      return $("#network").html();  
			    });
	}
	else $('#net').html("");
	//Uptime
	if(infos[id]['uptime']!=null)
	{
		$('#upt').html(infos[id]['uptime']['day']+' days '+infos[id]['uptime']['hour']+':'+infos[id]['uptime']['minute']+':'+infos[id]['uptime']['second']);
		$('#uptime tbody').html('<tr><td>'+infos[id]['uptime']['day']+' days '+infos[id]['uptime']['hour']+':'+infos[id]['uptime']['minute']+':'+infos[id]['uptime']['second']+'</td><td>'+(infos[id]['uptime']['Free rate']*100).toFixed(2)+'%</td></tr>');
		$('#upt').siblings(".badge").attr("data-content",function() {  
		              return $("#uptime").html();  
		            });
	}
	else $('#upt').html("");
}

function displayfib(id)
{
	text = '';
	if(!(contentactive in fibs[id]))
	{
		for( content in fibs[id] )
		{
			contentactive = content;
			break;
		}
	}
	for( content in fibs[id] )
	{
		text += '<a title="" data-container="body" data-toggle="popover" data-placement="right" data-content="'+fibs[id][content]['name']+'" id="'+content+'" href="javascript:displayContent(';
		text +=  "'"+content+"'";
		text +=  ');map.setViewport(displaypoints);" class="list-group-item';
		if(content == contentactive) text+=' active';
		text += '">'+fibs[id][content]['name'].substr(0,24) + '</a>';
	}
	$('.prefixlist').html(text);
	displayContent($('.prefixlist .active').attr('id'));
}

function Checkpassword()
{
	//alert(nodes[$('.nodelist .active').attr('id')]['password']);
	if(hex_md5($('#pass').val()) == nodes[$('.nodelist .active').attr('id')]['password']) $('#Ensure').click();
	else
	{
		alert("Password Wrong!");
		$('#pass').val("");
	}
}

function displayNode(id)
{
	//把原来的active清除，再把新点的active加上
	$('.nodelist .active').removeClass("active");
	nodeactive = id;
	$('#'+id).addClass("active");
	//显示相关信息
	displayinfo(id);
	//显示路由表
	displayfib(id);
	map.setViewport(displaypoints);//设置正好的视野
	if(warning[id]['err']!=0)
	{
		text = '';
		for(i in warning[id]['error'])
		{
			if(warning[id]['error'][i]['type']==1) text+='<span style="color:#C93756"><b>Error: </b>';
			else if(warning[id]['error'][i]['type']==2) text+='<span style="color:#f6bb42"><b>Warning: </b>';
			text+=warning[id]['error'][i]['remark']+'</span><br>';
		}
		$('#reason').html(text);
		$('#Alert').click();
	}
}

function activate()
{
	$(function(){
	$("[data-toggle='popover']").popover().on("mouseenter", function () {
		    var _this = this;
		    $(this).popover("show");
		    $(this).siblings(".popover").on("mouseleave", function () {
		        $(_this).popover('destroy');
		    });
		}).on("mouseleave", function () {
		    var _this = this;
		    setTimeout(function () {
		        if (!$(".popover:hover").length) {
		            $(_this).popover("destroy")
		        }
		    }, 100);
		});
    });
}
