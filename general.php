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
 * @description General functions.
 */

include_once "config.php";

/* Emails */

function checkEmails() {
	global $CONFIG;
	$table_name = $CONFIG['db_prefix'] . "points";
	$connection = imap_open('{' . $CONFIG['email_host'] . '/notls}', $CONFIG['email_name'], $CONFIG['email_pwd']);
	$count = imap_num_msg($connection);
	for($i = 1; $i <= $count; $i++) {
		$header = imap_headerinfo($connection, $i);
		if ($header->Unseen == 'U' && $header->from[0]->host == 'advanced-tracking.com' && !strncmp($header->subject, 'XML Position', 12)) {
			echo "One new email at " . $header->date . "<br />";
			$result = parseEmail(stripslashes(imap_body($connection, $i)));
			executeSQL("insert into " . $CONFIG['db_prefix'] . "points ( asset_id, latitude, longitude, time, heading, speed ) values ( " . $result['asset_id'] . ", " . $result['latitude'] . ", " . $result['longitude'] . ", '" . $result['datetime'] . "', " . $result['heading'] . ", " . $result['speed'] . " )");
		}
	}
}

function parseEmail($content) {
	$xml = simplexml_load_string($content);
	
	$asset = $xml->xpath("/old-trail-mail/asset/asset-identifier/value");
	if (!$asset) die("missing asset");
	$asset_id = intval($asset[0]);
	
	$date = $xml->xpath("/old-trail-mail/trail/trail-date-time/date-of-value");
	$time = $xml->xpath("/old-trail-mail/trail/trail-date-time/time-of-value");
	$datetime = $date[0] . "T" . $time[0];
	
	$lat_deg = $xml->xpath("/old-trail-mail/trail/latitude/degrees-of-value");
	$lat_min = $xml->xpath("/old-trail-mail/trail/latitude/minutes-of-value");
	$lat_sec = $xml->xpath("/old-trail-mail/trail/latitude/seconds-of-value");
	$lat_hemisphere = $xml->xpath("/old-trail-mail/trail/latitude/hemisphere-of-value");
	$lat = $lat_deg[0] + $lat_min[0] / 60 + $lat_sec[0] / 3600;
	if ($lat_hemisphere[0] == "south") $lat = 0 - $lat;
	
	$lon_deg = $xml->xpath("/old-trail-mail/trail/longitude/degrees-of-value");
	$lon_min = $xml->xpath("/old-trail-mail/trail/longitude/minutes-of-value");
	$lon_sec = $xml->xpath("/old-trail-mail/trail/longitude/seconds-of-value");
	$lon_hemisphere = $xml->xpath("/old-trail-mail/trail/longitude/hemisphere-of-value");
	$lon = $lon_deg[0] + $lon_min[0] / 60 + $lon_sec[0] / 3600;
	if ($lon_hemisphere[0] == "west") $lon = 0 - $lon;
	
	$speed_res = $xml->xpath("/old-trail-mail/trail/speed/value-of-value");
	$speed = floatval($speed_res[0]);
	
	$heading_res = $xml->xpath("/old-trail-mail/trail/heading/value-of-value");
	$heading = floatval($heading_res[0]);
	
	return array('asset_id' => $asset_id, 'datetime' => $datetime, 'latitude' => $lat, 'longitude' => $lon, 'speed' => $speed, 'heading' => $heading);
}

/* SQL */

function executeSQL($sql) {
	global $CONFIG;
	$mysqli = new mysqli($CONFIG['db_host'], $CONFIG['db_user'], $CONFIG['db_pwd'], $CONFIG['db_name']);
	if ($mysqli->connect_errno) die("Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error);
	$result = $mysqli->query($sql);
	if (!$result) die("Query failed: '" . $sql . "' -> error " . $mysqli->errno . " " . $mysqli->error);
	return $result;
}

function executeSQLOne($sql) {
	$result = executeSQL($sql);
	return $result->num_rows > 0 ? $result->fetch_assoc() : null;
}

/* General */

function getOrDefault($key, $default) {
	return isset($_GET[$key]) ? $_GET[$key] : $default;
}

function getAllAssets() {
	global $CONFIG;
	return executeSQL("select * from " . $CONFIG['db_prefix'] . "assets");
}

/* Map */

function getMap($map_div_id, $width, $height, $zoom, $navigation_control, $mapType_control, $scale_control, $map_type, $route_color, $route_opacity, $route_weight, $asset_id, $min_date, $max_date) {
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
		$center_lat = $last_point['latitude'];
		$center_lon = $last_point['longitude'];
	} else {
		$center_lat = ($max_lat + $min_lat) / 2;
		$center_lon = ($max_lon + $min_lon) / 2;
		
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

 ?>