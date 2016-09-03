
var map = new BMap.Map("allmap");
	map.centerAndZoom(new BMap.Point(116.404, 39.915), 13);
	map.enableScrollWheelZoom();
	var b = new BMap.Bounds(new BMap.Point(116.027143, 39.772348),new BMap.Point(116.832025, 40.126349));
	try {	
		BMapLib.AreaRestriction.setBounds(map, b);
	} catch (e) {
		alert(e);
	}