<?php
/*
    Copyright 2013 Laurian Verre
	
    This file is part of Wusmap.

    Wusmap is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    Wusmap is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Wusmap.  If not, see <http://www.gnu.org/licenses/>.
*/

/**
 * @package Wusmap
 * @version 0.1
 * @author Laurian Verre
 * @description Gets the map.
 */

include_once "general.php";

function getMap($map_div_id, $width, $height, $zoom, $center_lat, $center_lon, $navigation_control, $mapType_control, $scale_control, $map_type, $route_color, $route_opacity, $route_weight, $asset_id, $min_date, $max_date) {
	global $CONFIG;
	$map_type = "google.maps.MapTypeId." . $map_type;
	
	$asset = executeSQLOne("select * from " . $CONFIG['db_prefix'] . "assets where id=" . $asset_id);
	if ($asset == null) {
		die("There is no asset corresponding to that id!");
	}
	
	$sql = "select * from " . $CONFIG['db_prefix'] . "points where asset_id=" . $asset_id;
	if ($min_date != null) {
		$sql .= " and time >= '$min_date'";
	}
	if ($max_date != null) {
		$sql .= " and time <= '$max_date'";
	}
	$sql .= " order by time asc";
	$points = executeSQL($sql);
	if ($points->num_rows == 0) {
		die("There is no point for that asset!");
	}
	
	$add_points = "";
	$min_lat = 90;
	$max_lat = -90;
	$min_lon = 180;
	$max_lon = -180;
	$last_point = null;
	while ($point = $points->fetch_assoc()) {
		$last_point = $point;
		$lat = $point['latitude'];
		$lon = $point['longitude'];
		$add_points .= "
	addPoint(points, " . $lat . ", " . $lon . ");";
		if ($lat < $min_lat) $min_lat = $lat;
		if ($lat > $max_lat) $max_lat = $lat;
		if ($lon < $min_lon) $min_lon = $lon;
		if ($lon > $max_lon) $max_lon = $lon;
	}
	if ($zoom != null) {
		if ($center_lat == null) {
			$center_lat = $last_point['latitude'];
			$center_lon = $last_point['longitude'];
		}
	} else {
		if ($center_lat == null) {
			$center_lat = ($max_lat + $min_lat) / 2;
			$center_lon = ($max_lon + $min_lon) / 2;
		}
		
		$dist = 
			(6371 * acos(sin($min_lat / 57.2958) * sin($max_lat / 57.2958) + 
			(cos($min_lat / 57.2958) * cos($max_lat / 57.2958) * cos(($max_lon / 57.2958) - ($min_lon / 57.2958)))));
		$zoom = floor(8 - log(1.6446 * $dist / sqrt(2 * ($width * $height))) / log(2));
	}
	
	return "function wusmapInit() {
	var mapDiv = document.getElementById('$map_div_id');
	mapDiv.style.width = '$width';
	mapDiv.style.height = '$height';

	var points = new google.maps.MVCArray();$add_points

	var map = createMap(
		'$map_div_id', 
		$zoom, $center_lat, $center_lon, 
		$navigation_control, $mapType_control, $scale_control, 
		$map_type, 
		'$route_color', $route_opacity, $route_weight, points);

	getMarker(map, " . $last_point['latitude'] . ", " . $last_point['longitude'] . ", '" . $asset['name'] . "', '" . $last_point['time'] . "', " . $last_point['heading'] . ", " . $last_point['speed'] . ");
}
addEvent(window, 'load', wusmapInit);
";
}

$map_div_id = getOrDefault("map_div_id", "wusmap");
$width = getOrDefault("width", 500);
$height = getOrDefault("height", 500);
$zoom = getOrDefault("zoom", null);
$center_lat = getOrDefault("center_lat", null);
$center_lon = getOrDefault("center_lon", null);
$navigation_control = getOrDefault("navigation_control", false) == 'on' ? 'true' : 'false';
$map_type_control = getOrDefault("map_type_control", 1) == 'on' ? 'true' : 'false';
$scale_control = getOrDefault("scale_control", 1) == 'on' ? 'true' : 'false';
$map_type = getOrDefault("map_type", "HYBRID");
$route_color = getOrDefault("route_color", "green");
$route_opacity = getOrDefault("route_opacity", 1);
$route_weight = getOrDefault("route_weight", 2);
$asset_id = getOrDefault("asset_id", null);
$min_date = getOrDefault("min_date", null);
$max_date = getOrDefault("max_date", null);

$js = getMap($map_div_id, $width, $height, $zoom, $center_lat, $center_lon, $navigation_control, $map_type_control, $scale_control, $map_type, $route_color, $route_opacity, $route_weight, $asset_id, $min_date, $max_date);

if (getOrDefault("output", null) != "iframe") {
	echo $js;
} else {
?>
<html>
<head>
	<meta charset=utf-8 />
	<title>Route</title>
	<link rel="stylesheet" href="wusmap.css" />
	<script src="http://maps.google.com/maps/api/js?sensor=false"></script>
	<script src="infobox_packed.js"></script>
	<script src="wusmap.js"></script>
	<script>
<?php echo $js; ?>
	</script>
</head>
<body>
	<div id="wusmap"></div>
</body>
</html>
<?php
}
?>