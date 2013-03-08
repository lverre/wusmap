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
 * @description Exports the points to the desired format.
 */

include_once "general.php";
$DATE_FORMAT = "Y-m-d H:i:s";

$asset_id = getOrDefault("asset_id", null);
$min_date = getOrDefault("min_date", null);
$max_date = getOrDefault("max_date", null);
$newline = getOrDefault("newline", "win");
$format = getOrDefault("format", "csv");

$exporter = "export/$format.php";
if (!file_exists($exporter)) die("Unknown formatter: $format");
// TODO: if newline is not provided, show a form to get the parameters

$nl = "\n";
switch (strtoupper($newline)) {
	case "WIN":
	case "WINDOWS":
	case "CRLF":
		$nl = "\r\n";
		break;
	case "LFCR":
		$nl = "\n\r";
		break;
	case "CR":
		$nl = "\r";
		break;
	case "UNIX":
	case "LF":
	default:
		$nl = "\n";
		break;
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
	die(__("MAP_SQL_NO_POINT"));
}

$filename = "wusmap_$asset_id";
if ($min_date != null) {
	$filename .= "_min$min_date";
}
if ($max_date != null) {
	$filename .= "_max$max_date";
}

include_once "export/$format.php";

?>