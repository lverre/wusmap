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
 * @description Manages the boats.
 */

include_once "general.php";

if (isset($_REQUEST['input'])) {
	global $CONFIG;
	
	$boat_id = null;
	$datetime = null;
	$latitude = null;
	$longitude = null;
	$heading = null;
	$speed = null;
	if ($_REQUEST['input'] == "xml") {
		$result = parseEmail(stripslashes($_REQUEST['xml']));
		$boat_id = $result['boat_id'];
		$datetime = $result['datetime'];
		$latitude = $result['latitude'];
		$longitude = $result['longitude'];
		$heading = $result['heading'];
		$speed = $result['speed'];
	} else {
		$boat_id = $_REQUEST['boat_id'];
		$datetime = $_REQUEST['datetime'];
		$latitude = $_REQUEST['latitude'];
		$longitude = $_REQUEST['longitude'];
		$heading = $_REQUEST['heading'];
		$speed = $_REQUEST['speed'];
	}
	
	$table_name = $CONFIG['db_prefix'] . "points";
	executeSQL("insert into " . $CONFIG['db_prefix'] . "points 
( boat_id, latitude, longitude, time, heading, speed ) values 
( $boat_id, $latitude, $longitude, '$datetime', $heading, $speed )");
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset=utf-8 />
	<title><?php _e("ADD_POINT"); ?></title>
	<link rel="stylesheet" href="wusmap.css" />
	<script src="wusmap.js"></script>
</head>
<body>
<h1><?php _e("ADD_POINT"); ?></h1>
<form method="post">
<div>
	<input type="radio" name="input" id="input_values" value="values" onchange="if (this.checked) { setDisplay('xml_div', 'none'); setDisplay('values_table', 'block'); }" checked="checked" /><label for="input_values"><?php _e("ADD_POINT_VALUES"); ?></label>
	<input type="radio" name="input" id="input_xml" value="xml" onchange="if (this.checked) { setDisplay('xml_div', 'block'); setDisplay('values_table', 'none'); }" /><label for="input_xml"><?php _e("ADD_POINT_XML"); ?></label>
</div>
<div id="xml_div" style="display: none">
	<label for="xml"><?php _e("ADD_POINT_XML_TITLE"); ?></label><br />
	<textarea rows="30" cols="80" name="xml" id="xml" title="<?php _e("ADD_POINT_XML_TOOLTIP"); ?>"></textarea>
</div>
<table id="values_table">
<tr>
	<td><label for="boat_id"><?php _e("SH_BOAT_TITLE"); ?></label></td>
	<td>
		<select name="boat_id" id="boat_id">
<?php
$boats = getAllBoats();
while ($boat = $boats->fetch_assoc()) {
	echo "<option value=\"" . $boat['id'] . "\">" . $boat['name'] . "</option>\n";
}
?>
		</select>
	</td>
</tr>
<tr>
	<td><label for="latitude"><?php _e("SH_LAT_TITLE"); ?></label></td>
	<td><input type="number" step="0.000001" min="-90" max="90" name="latitude" id="latitude" title="<?php _e("SH_LAT_TOOLTIP"); ?>" style="width: 100px" /></td>
</tr>
<tr>
	<td><label for="longitude"><?php _e("SH_LON_TITLE"); ?></label></td>
	<td><input type="number" step="0.000001" min="-180" max="180" name="longitude" id="longitude" title="<?php _e("SH_LON_TOOLTIP"); ?>" style="width: 100px" /></td>
</tr>
<tr>
	<td><label for="datetime"><?php _e("SH_DATETIME_TITLE"); ?></label></td>
	<td><input type="datetime" name="datetime" id="datetime" title="<?php _e("SH_DATETIME_TOOLTIP"); ?>" /></td>
</tr>
<tr>
	<td><label for="heading"><?php _e("SH_HEADING_TITLE"); ?></label></td>
	<td><input type="number" step="0.01" min="0" max="360" name="heading" id="heading" title="<?php _e("ADD_POINT_HEADING_TOOLTIP"); ?>" style="width: 50px" /></td>
</tr>
<tr>
	<td><label for="speed"><?php _e("SH_SPEED_TITLE"); ?></label></td>
	<td><input type="number" step="0.01" min="0" name="speed" id="speed" title="<?php _e("ADD_POINT_SPEED_TOOLTIP"); ?>" style="width: 50px" /></td>
</tr>
</table>
<input type="submit" value="<?php _e("SH_SUBMIT"); ?>" />
</form>
</body>
</html>