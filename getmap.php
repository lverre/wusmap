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
 * @version 1.1
 * @author Laurian Verre
 * @description Gets the map.
 */

if (isset($_GET['lang'])) $lang = $_GET['lang'];

include_once "general.php";

$DATE_FORMAT = "Y-m-d H:i:s";

function getDist($lat1, $lon1, $lat2, $lon2) {
	$theta = $lon1 - $lon2;
	$arc = acos(sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta)));
	return $arc * 3440.0696544276457883369330453564;// Earth radius in nautical mile
}

function getRow($name, $value, $format=null) {
	if ($value != null) {
		if ($format != null) $value = sprintf($format, $value);
		return "<tr><td class='wusmap-infobox-description-key'>$name</td><td class='wusmap-infobox-description-value'>$value</td>";
	} else {
		return "";
	}
}

function getCoord($coord, $is_lat) {
	global $MESSAGES;
	$is_neg = $coord < 0;
	if ($is_neg) {
		$coord = 0 - $coord;
	}
	$deg = floor($coord);
	$coord = ($coord - $deg) * 60;
	$min = floor($coord);
	$coord = ($coord - $min) * 60;
	$sec = floor($coord);
	$card = $is_lat ? ($is_neg ? __("SH_CARD_S") : __("SH_CARD_N")) : ($is_neg ? __("SH_CARD_W") : __("SH_CARD_E"));
	return $deg . "&deg;" . $min . "'" . $sec . "'' " . $card;
}

function getMarker($name, $lat, $lon, $time, $heading, $speed, $dist_to_prev, $avg_speed_since_prev, $dist_to_dest, $eta, $remaining) {
	global $MESSAGES;
	global $DATE_FORMAT;
	$time = $time != null ? date($DATE_FORMAT, $time) : null;
	
	$content = "<table>";
	$content .= getRow(__("MAP_DATETIME_TITLE"), $time);
	$content .= getRow(__("SH_LAT_TITLE"), getCoord($lat, true));
	$content .= getRow(__("SH_LON_TITLE"), getCoord($lon, false));
	$content .= getRow(__("SH_HEADING_TITLE"), $heading, __("MAP_HEADING_FORMAT"));
	$content .= getRow(__("SH_SPEED_TITLE"), $speed, __("MAP_SPEED_FORMAT"));
	$content .= getRow(__("MAP_DIST_TO_PREV_TITLE"), $dist_to_prev, __("MAP_DIST_FORMAT"));
	$content .= getRow(__("MAP_SPEED_AVG_TITLE"), $avg_speed_since_prev, __("MAP_SPEED_FORMAT"));
	$content .= getRow(__("MAP_DIST_TO_DEST_TITLE"), $dist_to_dest, __("MAP_DIST_FORMAT"));
	$content .= getRow(__("MAP_REMAINING_TITLE"), $remaining);
	$content .= getRow("<span title='" . __("MAP_ETA_TOOLTIP") . "'>" . __("MAP_ETA_TITLE") . "</span>:", $eta != null ? date($DATE_FORMAT, $eta) : null);
	$content .= "</table>";
	
	$title = sprintf(__("MAP_TITLE_FORMAT"), $name, $time);
	
	return "getMarker(map, $lat, $lon, \"$name\", \"$title\", \"$content\");";
}

function getPointMarker($asset, $point, $prev_point, $dest_lat, $dest_lon) {
	global $MESSAGES;
	$now = strtotime($point['time']);
	$speed = $point['speed'];
	
	$dist_to_prev = null;
	$avg_speed_since_prev = null;
	if ($prev_point != null) {
		$dist_to_prev = getDist($point['latitude'], $point['longitude'], $prev_point['latitude'], $prev_point['longitude']);
		$interval = $now - strtotime($prev_point['time']);
		$avg_speed_since_prev = round($dist_to_prev / ($interval / 3600), 1);
		$speed = $avg_speed_since_prev;
		$dist_to_prev = round($dist_to_prev, 1);
	}
	
	$dist_to_dest = null;
	$eta = null;
	$remaining = null;
	if ($dest_lat != null && $dest_lon != null) {
		$dist_to_dest = getDist($point['latitude'], $point['longitude'], $dest_lat, $dest_lon);
		if ($dist_to_dest >= 5 && $point['speed'] > 0) {
			$remaining_seconds = round(($dist_to_dest / $speed) * 3600);
			$eta = $now + $remaining_seconds;
			$days = floor($remaining_seconds / 86400);
			$remaining_seconds -= $days * 86400;
			$hours = round($remaining_seconds / 3600);
			$remaining = sprintf(__("MAP_REMAINING_FORMAT"), $days, $hours);
		}
		$dist_to_dest = round($dist_to_dest, 1);
	}
	
	return getMarker($asset['name'], $point['latitude'], $point['longitude'], $now, $point['heading'], $point['speed'], $dist_to_prev, $avg_speed_since_prev, $dist_to_dest, $eta, $remaining);
}

function getMap($map_div_id, $width, $height, $zoom, $center_lat, $center_lon, $navigation_control, $mapType_control, $scale_control, $map_type, $route_color, $route_opacity, $route_weight, $asset_id, $min_date, $max_date, $first_marker, $last_marker, $marker_every, $dest_name, $dest_lat, $dest_lon) {
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
	$markers = "";
	$min_lat = 90;
	$max_lat = -90;
	$min_lon = 180;
	$max_lon = -180;
	if ($dest_lat != null && $dest_lon != null) {
		if ($dest_lat < $min_lat) $min_lat = $dest_lat;
		if ($dest_lat > $max_lat) $max_lat = $dest_lat;
		if ($dest_lon < $min_lon) $min_lon = $dest_lon;
		if ($dest_lon > $max_lon) $max_lon = $dest_lon;
		$markers .= getMarker("'" . $dest_name . "'", $dest_lat, $dest_lon, null, null, null, null, null, null, null, null) . "\n";
	} else {
		// Just in case...
		$dest_lat = null;
		$dest_lon = null;
	}
	$last_point = null;
	$prev_point = null;
	$index = 0;
	while ($point = $points->fetch_assoc()) {
		$prev_point = $last_point;
		$last_point = $point;
		$lat = $point['latitude'];
		$lon = $point['longitude'];
		$add_points .= "
	addPoint(points, " . $lat . ", " . $lon . ");";
		if (($first_marker && $index == 0) || ($marker_every > 0 && ($index % $marker_every) == 0)) {
			$markers .= "	" . getPointMarker($asset, $point, $prev_point, $dest_lat, $dest_lon) . "\n";
		}
		if ($lat < $min_lat) $min_lat = $lat;
		if ($lat > $max_lat) $max_lat = $lat;
		if ($lon < $min_lon) $min_lon = $lon;
		if ($lon > $max_lon) $max_lon = $lon;
		$index++;
	}
	if ($last_marker && !($first_marker && $index == 1) && !($marker_every > 0 && (($index - 1) % $marker_every) == 0)) {
		$markers .= "	" . getPointMarker($asset, $last_point, $prev_point, $dest_lat, $dest_lon) . "\n";
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

	$markers
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
$navigation_control = getOrDefault("navigation_control", null) == 'on' ? 'true' : 'false';
$map_type_control = getOrDefault("map_type_control", null) == 'on' ? 'true' : 'false';
$scale_control = getOrDefault("scale_control", null) == 'on' ? 'true' : 'false';
$map_type = getOrDefault("map_type", "HYBRID");
$route_color = getOrDefault("route_color", "green");
$route_opacity = getOrDefault("route_opacity", 1);
$route_weight = getOrDefault("route_weight", 2);
$asset_id = getOrDefault("asset_id", null);
$min_date = getOrDefault("min_date", null);
$max_date = getOrDefault("max_date", null);
$first_marker = getOrDefault("first_marker", null) == 'on';
$last_marker = getOrDefault("last_marker", null) == 'on';
$marker_every = getOrDefault("marker_every", 0);
$dest_name = getOrDefault("destination_name", "Destination");
$dest_lat = getOrDefault("destination_lat", null);
$dest_lon = getOrDefault("destination_lon", null);

$js = getMap($map_div_id, $width, $height, $zoom, $center_lat, $center_lon, $navigation_control, $map_type_control, $scale_control, $map_type, $route_color, $route_opacity, $route_weight, $asset_id, $min_date, $max_date, $first_marker, $last_marker, $marker_every, $dest_name, $dest_lat, $dest_lon);

if (getOrDefault("output", null) != "iframe") {
	echo $js;
} else {
?>
<html>
<head>
	<meta charset=utf-8 />
	<title><?php _e("MAP"); ?></title>
	<link rel="stylesheet" href="wusmap.css" />
	<script src="http://maps.google.com/maps/api/js?sensor=false"></script>
	<script src="infobox_packed.js"></script>
	<script src="wusmap_map.js"></script>
	<script>
<?php echo $js; ?>
	</script>
</head>
<body>
	<div id="wusmap"></div>
	<div style="clear:both; font-size:0.8em; font-style:italic;"><?php echo sprintf(__("MAP_POWERED"), "<a href='http://lverre.github.com/wusmap'>wusmap</a>"); ?></div>
</body>
</html>
<?php
}
?>