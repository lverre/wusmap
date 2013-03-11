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
 * @description General functions.
 */

include_once "config.php";
$DATE_FORMAT = "Y-m-d H:i:s";

/* Translation */

$lang = "en";
if (isset($_REQUEST['lang'])) {
	$lang = $_REQUEST['lang'];
} else if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
	$lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
}
if (!file_exists("./i18n/$lang.php")) $lang = "en";
putenv("LANG=$lang");
setlocale(LC_ALL, "$lang");
include_once "./i18n/$lang.php";

function __($key) {
	global $MESSAGES;
	return $MESSAGES[$key];
}

function _e($key) {
	echo __($key);
}

/* Emails */

function parseEmail($content) {
	$xml = simplexml_load_string($content);
	
	$boat = $xml->xpath("/old-trail-mail/boat/boat-identifier/value");
	if (!$boat) die("missing boat");
	$boat_id = intval($boat[0]);
	
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
	
	return array('boat_id' => $boat_id, 'datetime' => $datetime, 'latitude' => $lat, 'longitude' => $lon, 'speed' => $speed, 'heading' => $heading);
}

/* SQL */

function executeSQL($sql) {
	global $CONFIG;
	$mysqli = new mysqli($CONFIG['db_host'], $CONFIG['db_user'], $CONFIG['db_pwd'], $CONFIG['db_name']);
	if ($mysqli->connect_errno) die(sprintf(__("SH_SQL_CONNECTION_FAILED"), $mysqli->connect_error));
	$result = $mysqli->query($sql);
	if (!$result) die(sprintf(__("SH_SQL_QUERY_FAILED"), $sql, $mysqli->errno, $mysqli->error));
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
	return executeSQL("select * from " . $CONFIG['db_prefix'] . "boats");
}

 ?>