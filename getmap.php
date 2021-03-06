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

include_once "general.php";

/* Get GET parameters */

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
$boat_id = getOrDefault("boat_id", null);
$min_date = getOrDefault("min_date", null);
$max_date = getOrDefault("max_date", null);
$first_marker = getOrDefault("first_marker", null) == 'on';
$last_marker = getOrDefault("last_marker", null) == 'on';
$marker_every = getOrDefault("marker_every", 0);
$dest_name = getOrDefault("destination_name", "Destination");
$dest_lat = getOrDefault("destination_lat", null);
$dest_lon = getOrDefault("destination_lon", null);
$min_date_trip = getOrDefault("min_date_trip", $min_date);
$eta_multiplier = getOrDefault("eta_multiplier", 1);
$show_powered = getOrDefault("show_powered", null) == 'on';
$show_weather = getOrDefault("show_weather", null) == 'on';
$show_big_map = getOrDefault("show_big_map", null) == 'on';

/* Tools */

function getRow($name, $format, $value1, $value2 = null, $value3 = null) {
	if ($name == null || $value1 == null) {
		return "";
	}
	
	$value = $value1;
	if ($format != null) {
		if ($value1 != null) {
			if ($value2 != null) {
				if ($value3 != null) {
					$value = sprintf($format, $value1, $value2, $value3);
				} else {
					$value = sprintf($format, $value1, $value2);
				}
			} else {
				$value = sprintf($format, $value1);
			}
		}
	}
	return "<tr><td class='wusmap-infobox-description-key'>$name</td><td class='wusmap-infobox-description-value'>$value</td>";
}

function getMarker($name, $lat, $lon, $time, $heading, $speed, $vmg, $dist_to_prev, $avg_speed_since_prev, $avg_vmg, $prev_heading, $dist_to_dest, $bearing, $eta, $remaining) {
	global $MESSAGES;
	global $DATE_FORMAT;
	$time = parseDate($time);
	
	$content = "<table>";
	$content .= getRow(__("MAP_DATETIME_TITLE"), null, $time);
	$content .= getRow(__("SH_LAT_TITLE"), null, coordToString($lat, true));
	$content .= getRow(__("SH_LON_TITLE"), null, coordToString($lon, false));
	$content .= getRow(__("SH_HEADING_TITLE"), __("MAP_HEADING_FORMAT"), $heading, headingToHRString($heading));
	$content .= $vmg != null ? getRow(__("SH_SPEED_TITLE"), __("MAP_SPEED_VMG_FORMAT"), $speed, $vmg) : getRow(__("SH_SPEED_TITLE"), __("SH_SPEED_FORMAT"), $speed);
	$content .= $prev_heading != null
		? getRow(__("MAP_TO_PREV_TITLE"), __("MAP_DIST_HEADING_FORMAT"), $dist_to_prev, $prev_heading, headingToHRString($prev_heading))
		: getRow(__("MAP_TO_PREV_TITLE"), __("SH_DIST_FORMAT"), $dist_to_prev);
	$content .= $avg_vmg != null ? getRow(__("MAP_SPEED_AVG_TITLE"), __("MAP_SPEED_VMG_FORMAT"), $avg_speed_since_prev, $avg_vmg) : getRow(__("MAP_SPEED_AVG_TITLE"), __("SH_SPEED_FORMAT"), $avg_speed_since_prev);
	$content .= $bearing != null
		? getRow(__("MAP_TO_DEST_TITLE"), __("MAP_DIST_HEADING_FORMAT"), $dist_to_dest, $bearing, headingToHRString($bearing))
		: getRow(__("MAP_TO_DEST_TITLE"), __("SH_DIST_FORMAT"), $dist_to_dest);
	$content .= getRow(__("MAP_REMAINING_TITLE"), null, $remaining);
	$content .= getRow("<span title='" . __("MAP_ETA_TOOLTIP") . "'>" . __("MAP_ETA_TITLE") . "</span>", null, parseDate($eta));
	$content .= "</table>";
	
	$title = $time != null ? sprintf(__("MAP_TITLE_FORMAT"), $name, $time) : $name;
	
	return "getMarker(map, $lat, $lon, \"$name\", \"$title\", \"$content\");";
}

function getPointMarker($boat, $point, $prev_point, $first_point, $dest_lat, $dest_lon, $eta_multiplier) {
	global $MESSAGES;
	$lat = $point['latitude'];
	$lon = $point['longitude'];
	$now = strtotime($point['time']);
	$heading = $point['heading'];
	$speed = $point['speed'];
	
	// Get the average speed since last point
	$dist_to_prev = null;
	$avg_speed = null;
	$avg_vmg = null;
	$prev_heading = null;
	if ($prev_point != null) {
		$prev_lat = $prev_point['latitude'];
		$prev_lon = $prev_point['longitude'];
		$dist_to_prev = getDist($prev_lat, $prev_lon, $lat, $lon);
		$prev_heading = getHeading($prev_lat, $prev_lon, $lat, $lon);
		$interval = $now - strtotime($prev_point['time']);
		if ($interval > 0) {
			$avg_speed = round($dist_to_prev / ($interval / 3600), 1);
			if ($dest_lat != null && $dest_lon != null) {
				$avg_vmg = getVMG($avg_speed, $prev_heading, getHeading($prev_lat, $prev_lon, $dest_lat, $dest_lon));
			}
		}
	}
	
	// Get the current parameters to the destination
	$bearing = null;
	$dist_to_dest = null;
	$vmg = null;
	if ($dest_lat != null && $dest_lon != null) {
		$bearing = getHeading($lat, $lon, $dest_lat, $dest_lon);
		$dist_to_dest = getDist($lat, $lon, $dest_lat, $dest_lon);
		if ($dist_to_dest >= 5) $vmg = getVMG($speed, $heading, $bearing);
	}
	
	// Get the ETA
	$eta_speed = null;
	$eta = null;
	$remaining = null;
	if ($dist_to_dest >= 5) {
		if ($first_point != null && $first_point != $point) {
			$flat = $first_point['latitude'];
			$flon = $first_point['longitude'];
			$dist_done = getDist($flat, $flon, $lat, $lon);
			$interval = $now - strtotime($first_point['time']);
			if ($interval > 0) {
				$fvmg = getVMG(
					$dist_done / ($interval / 3600),
					getHeading($flat, $flon, $lat, $lon),
					getHeading($flat, $flon, $dest_lat, $dest_lon));
				if ($fvmg >= 1) $eta_speed = $fvmg;
			}
		}
		if ($eta_speed == null) {
			$eta_speed = $vmg;
		}
		if ($eta_speed > 0) {
			$remaining_seconds = round($eta_multiplier * ($dist_to_dest / $eta_speed) * 3600);
			$eta = $now + $remaining_seconds;
			$hours = round($remaining_seconds / 3600);
			$days = floor($hours / 24);
			$hours -= $days * 24;
			$remaining = sprintf(__("MAP_REMAINING_FORMAT"), $days, $hours);
		}
	}
	
	return getMarker($boat['name'], $lat, $lon, $now, $heading, $speed, $vmg, $dist_to_prev, $avg_speed, $avg_vmg, $prev_heading, $dist_to_dest, $bearing, $eta, $remaining);
}

function getBiggerMapUrl() {
	$url = "http://$_SERVER[HTTP_HOST]$_SERVER[PHP_SELF]?";
	foreach ($_REQUEST as $key => $value) {
		if ($key == 'height' || $key == 'width') {
			$value = '100%';
		}
		if ($key != 'show_powered' && $key != 'show_big_map' && $key != 'show_weather') {
			$url .= $key . "=" . urlencode($value) . "&";
		}
	}
	return $url;
}

/* Get Script */

$map_type = "google.maps.MapTypeId." . $map_type;

$boat = executeSQLOne("select * from " . $CONFIG['db_prefix'] . "boats where id=" . $boat_id);
if ($boat == null) {
	die(__("MAP_SQL_BOAT_INVALID"));
}

$sql = "select * from " . $CONFIG['db_prefix'] . "points where boat_id=" . $boat_id;
if ($min_date != null) {
	$sql .= " and time >= '$min_date'";
}
if ($max_date != null) {
	$sql .= " and time <= '$max_date'";
}
$sql .= " order by time asc";
$points = executeSQL($sql);
if ($points->num_rows == 0) {
	die(__("MAP_SQL_NO_POINT"));
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
	$markers .= getMarker($dest_name, $dest_lat, $dest_lon, null, null, null, null, null, null, null, null, null, null, null, null) . "\n";
} else {
	// Just in case...
	$dest_lat = null;
	$dest_lon = null;
}
$min_date_trip = $min_date_trip != null ? strtotime($min_date_trip) : -1;
$first_point = null;
$last_point = null;
$prev_point = null;
$index = 0;
while ($point = $points->fetch_assoc()) {
	$prev_point = $last_point;
	$last_point = $point;
	if ($first_point == null) {
		if ($min_date_trip < 0 || strtotime($point['time']) >= $min_date_trip) $first_point = $point;
	}
	$lat = $point['latitude'];
	$lon = $point['longitude'];
	$add_points .= "
addPoint(points, " . $lat . ", " . $lon . ");";
	if (($first_marker && $index == 0) || ($marker_every > 0 && ($index % $marker_every) == 0)) {
		$markers .= "	" . getPointMarker($boat, $point, $prev_point, $first_point, $dest_lat, $dest_lon, $eta_multiplier) . "\n";
	}
	if ($lat < $min_lat) $min_lat = $lat;
	if ($lat > $max_lat) $max_lat = $lat;
	if ($lon < $min_lon) $min_lon = $lon;
	if ($lon > $max_lon) $max_lon = $lon;
	$index++;
}
if ($last_marker && !($first_marker && $index == 1) && !($marker_every > 0 && (($index - 1) % $marker_every) == 0)) {
	$markers .= "	" . getPointMarker($boat, $last_point, $prev_point, $first_point, $dest_lat, $dest_lon, $eta_multiplier) . "\n";
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
	
	// 256 is GLOBE_WIDTH, a constant in Google Map projection
	// 0.69... = ln2
	// *1.1 is to be sure we show everything
	$angle_lon = ($max_lon - $min_lon) * 1.1;
	$angle_lat = ($max_lat - $min_lat) * 1.1;
	$zoom = min(
		round(log($width * 360 / $angle_lon / 256) / 0.69314718055994530941723212145818),
		round(log($height * 360 / $angle_lat / 256) / 0.69314718055994530941723212145818));
}
	
$js = "function wusmapInit() {
	var mapDiv = document.getElementById('$map_div_id');
	mapDiv.style.width = '$width';
	mapDiv.style.height = '$height';

	var points = new google.maps.MVCArray();$add_points

	var map = createMap(
		'$map_div_id', 
		$zoom, $center_lat, $center_lon, 
		$navigation_control, $map_type_control, $scale_control, 
		$map_type, 
		'$route_color', $route_opacity, $route_weight, points);

	$markers
}
addEvent(window, 'load', wusmapInit);
";

/* Output */

if (getOrDefault("output", null) != "iframe") {
	echo $js;
} else {
?>
<!DOCTYPE html>
<html style="height: 100%;">
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
<body style="height: 100%; margin: 0px; padding: 0px;">
	<div id="wusmap"></div>
<?php if ($show_big_map) { ?>
	<div style="clear:both; font-size:0.8em; font-style:italic;"><?php echo sprintf(__("MAP_LINK_BIG_MAP"), "href='" . getBiggerMapUrl() . "' target='_parent'"); ?></div>
<?php } if ($show_weather) { ?>
	<div style="clear:both; font-size:0.8em; font-style:italic;"><?php echo sprintf(__("MAP_LINK_WEATHER"), "href='http://www.windfinder.com/weather-maps/forecast#$zoom/$center_lat/$center_lon' target='_parent'"); ?></div>
<?php } if ($show_powered) { ?>
	<div style="clear:both; font-size:0.8em; font-style:italic;"><?php echo sprintf(__("MAP_LINK_POWERED"), "href='http://lverre.github.com/wusmap' target='_parent'"); ?></div>
<?php } ?>
</body>
</html>
<?php
}
?>