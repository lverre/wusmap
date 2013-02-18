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
 * @version 1.0
 * @author Laurian Verre
 * @description Manages the assets.
 */

include_once "general.php";

if (isset($_REQUEST['input'])) {
	global $CONFIG;
	
	$asset_id = null;
	$datetime = null;
	$latitude = null;
	$longitude = null;
	$heading = null;
	$speed = null;
	if ($_REQUEST['input'] == "xml") {
		$result = parseEmail(stripslashes($_REQUEST['xml']));
		$asset_id = $result['asset_id'];
		$datetime = $result['datetime'];
		$latitude = $result['latitude'];
		$longitude = $result['longitude'];
		$heading = $result['heading'];
		$speed = $result['speed'];
	} else {
		$asset_id = $_REQUEST['asset_id'];
		$datetime = $_REQUEST['datetime'];
		$latitude = $_REQUEST['latitude'];
		$longitude = $_REQUEST['longitude'];
		$heading = $_REQUEST['heading'];
		$speed = $_REQUEST['speed'];
	}
	
	$table_name = $CONFIG['db_prefix'] . "points";
	executeSQL("insert into " . $CONFIG['db_prefix'] . "points 
( asset_id, latitude, longitude, time, heading, speed ) values 
( $asset_id, $latitude, $longitude, '$datetime', $heading, $speed )");
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset=utf-8 />
	<title>Manually Add A Point</title>
	<link rel="stylesheet" href="wusmap.css" />
	<script src="wusmap.js"></script>
</head>
<body>
<h1>Manually Add A Point</h1>
<form method="post">
<div>
	<input type="radio" name="input" id="input_values" value="values" onchange="if (this.checked) { setDisplay('xml_div', 'none'); setDisplay('values_table', 'block'); }" checked="checked" /><label for="input_values">Values</label>
	<input type="radio" name="input" id="input_xml" value="xml" onchange="if (this.checked) { setDisplay('xml_div', 'block'); setDisplay('values_table', 'none'); }" /><label for="input_xml">XML</label>
</div>
<div id="xml_div" style="display: none">
	<label for="xml">XML (from Email):</label><br />
	<textarea rows="30" cols="80" name="xml" id="xml" title="Paste the body of the email as XML"></textarea>
</div>
<table id="values_table">
<tr>
	<td><label for="asset_id">Asset:</label></td>
	<td>
		<select name="asset_id" id="asset_id">
<?php
$assets = getAllAssets();
while ($asset = $assets->fetch_assoc()) {
	echo "<option value=\"" . $asset['id'] . "\">" . $asset['name'] . "</option>\n";
}
?>
		</select>
	</td>
</tr>
<tr>
	<td><label for="latitude">Latitude:</label></td>
	<td><input type="number" min="-90" max="90" name="latitude" id="latitude" title="In decimal: 42.5 for 45&deg;30'N" style="width: 100px" /></td>
</tr>
<tr>
	<td><label for="longitude">Longitude:</label></td>
	<td><input type="number" min="-180" max="180" name="longitude" id="longitude" title="In decimal: 42.5 for 45&deg;30'E" style="width: 100px" /></td>
</tr>
<tr>
	<td><label for="datetime">Date &amp; Time:</label></td>
	<td><input type="datetime" name="datetime" id="datetime" title="In the format ISO 8601, e.g. 2004-05-23T14:25:10" /></td>
</tr>
<tr>
	<td><label for="heading">Heading:</label></td>
	<td><input type="number" min="0" max="360" name="heading" id="heading" title="In degree, in decimal, e.g. 265.7" style="width: 50px" /></td>
</tr>
<tr>
	<td><label for="speed">Speed:</label></td>
	<td><input type="number" min="0" name="speed" id="speed" title="In knots, in decimal, e.g. 5.2" style="width: 50px" /></td>
</tr>
</table>
<input type="submit" value="Submit" />
</form>
</body>
</html>