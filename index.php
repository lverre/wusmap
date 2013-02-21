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
 * @description Gets the URL.
 */

include_once "general.php";
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset=utf-8 />
	<title>Get Wusmap Map</title>
	<link rel="stylesheet" href="wusmap.css" />
	<script src="wusmap.js"></script>
</head>
<body>
<div style="margin-left:auto; margin-right:auto; width: 648px">
<h1>Get Wusmap Map</h1>
<form method="get" action="getmap.php">
<div style="clear: both; margin: 16px 8px 8px 8px;">
<table>
<tr>
	<th colspan="2">General</th>
</tr>
<tr>
	<td><label>Output:</label></td>
	<td>
		<input type="radio" name="output" id="output_iframe" value="iframe" onchange="if (this.checked) { setDisabled('map_div_id', true); setDisplay('map_div_id_tr', 'none'); }" checked="checked" /><label for="output_iframe">IFrame</label>
		<input type="radio" name="output" id="output_script" value="script" onchange="if (this.checked) { setDisabled('map_div_id', false); setDisplay('map_div_id_tr', 'table-row'); }" /><label for="output_script">Script</label>
	</td>
</tr>
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
		(<a href="manageassets.php">manage assets</a>)
	</td>
</tr>
<tr id="map_div_id_tr" style="display:none;">
	<td><label for="map_div_id">Map Div ID:</label></td>
	<td><input type="text" name="map_div_id" id="map_div_id" value="wusmap" title="The id of the div where to put the map" disabled="disabled" /></td>
</tr>
</table>
</div>
<div style="float:left; margin: 8px 16px 8px 8px; width: 300px;">
<table>
<tr>
	<th colspan="2">Map</th>
</tr>
<tr>
	<td><label for="width">Width:</label></td>
	<td><input type="number" step="1" name="width" id="width" value="500" title="Width of the map (eg. 100%, 600px, ...)" style="width: 50px" /></td>
</tr>
<tr>
	<td><label for="height">Height:</label></td>
	<td><input type="number" step="1" name="height" id="height" value="500" title="Height of the map (eg. 100%, 600px, ...)" style="width: 50px" /></td>
</tr>
<tr>
	<td>
		<input type="checkbox" id="manual_zoom" title="Whether to use auto-zoom or not" onchange="setDisabled('zoom', !this.checked); setDisplay('zoom', this.checked ? 'inline-block' : 'none');" checked="checked" />
		<label for="manual_zoom">Manual Zoom</label>
	</td>
	<td><input type="number" step="1" min="0" max="20" name="zoom" id="zoom" value="12" title="Zoom of the map when loading (1 to 20)" style="width: 50px" /></td>
</tr>
<tr>
	<td colspan="2">
		<input type="checkbox" id="manual_center" title="Whether to manually set the center of the map or not" onchange="setDisabled('center_lat', !this.checked); setDisplay('center_lat_tr', this.checked ? 'table-row' : 'none'); setDisabled('center_lon', !this.checked); setDisplay('center_lon_tr', this.checked ? 'table-row' : 'none');" />
		<label for="manual_center">Manual Center</label>
	</td>
</tr>
<tr id="center_lat_tr" style="display: none;">
	<td><label for="center_lat">Center Latitude:</label></td>
	<td><input type="number" step="0.00001" min="-90" max="90" name="center_lat" id="center_lat" title="In decimal: 42.5 for 45&deg;30'N" style="width: 100px" disabled="disabled" /></td>
</tr>
<tr id="center_lon_tr" style="display: none;">
	<td><label for="center_lon">Center Longitude:</label></td>
	<td><input type="number" step="0.00001" min="-180" max="180" name="center_lon" id="center_lon" title="In decimal: 42.5 for 45&deg;30'E" style="width: 100px" disabled="disabled" /></td>
</tr>
<tr>
	<td><label for="map_type">Map Type:</label></td>
	<td>
		<select name="map_type" id="map_type">
			<option value="ROADMAP">Roads</option>
			<option value="SATELLITE">Satellite</option>
			<option value="TERRAIN">Terrain</option>
			<option value="HYBRID" selected="selected">Hybrid (Roads + Sat)</option>
		</select>
	</td>
</tr>
<tr>
	<td colspan="2">
		<input type="checkbox" name="navigation_control" id="navigation_control" checked="checked" title="Whether to show the navigation control in the map or not" />
		<label for="navigation_control">Navigation Control</label>
	</td>
</tr>
<tr>
	<td colspan="2">
		<input type="checkbox" name="map_type_control" id="map_type_control" checked="checked" title="Whether to show the map type control in the map or not" />
		<label for="map_type_control">Map Type Control</label>
	</td>
</tr>
<tr>
	<td colspan="2">
		<input type="checkbox" name="scale_control" id="scale_control" checked="checked" title="Whether to show the scale control in the map or not" />
		<label for="scale_control">Scale Control</label>
	</td>
</tr>
<tr>
	<th colspan="2">Route</th>
</tr>
<tr>
	<td><label for="route_color">Route Color:</label></td>
	<td><input type="color" name="route_color" id="route_color" value="#008000" title="The color of the route's line (in HTML hex style ie. '#FFAA00', or default value ie. 'blue')" /></td>
</tr>
<tr>
	<td><label for="route_opacity">Route Opacity:</label></td>
	<td><input type="number step="0.001"" min="0" max="1" name="route_opacity" id="route_opacity" value="1" title="The opacity of the route's line (0.0 is transparent, 1.0 is opaque)" style="width: 50px" /></td>
</tr>
<tr>
	<td><label for="route_weight">Route Weight:</label></td>
	<td><input type="number" step="1" min="0" max="100" name="route_weight" id="route_weight" value="2" title="The width of the route's line (in pixels)" style="width: 50px" /></td>
</tr>
</table>
</div>
<div style="float:left; margin: 8px 8px 8px 16px; width: 300px;">
<table>
<tr>
	<th colspan="2">Dates</th>
</tr>
<tr>
	<td>
		<input type="checkbox" id="use_min_date" onchange="setDisabled('min_date', !this.checked); setDisplay('min_date', this.checked ? 'inline-block' : 'none');" />
		<label for="use_min_date">Min Date:</label>
	</td>
	<td><input type="datetime" name="min_date" id="min_date" disabled="disabled" style="display:none;" title="The start date of the route" /></td>
</tr>
<tr>
	<td>
		<input type="checkbox" id="use_max_date" onchange="setDisabled('max_date', !this.checked); setDisplay('max_date', this.checked ? 'inline-block' : 'none');" />
		<label for="use_max_date">Max Date:</label>
	</td>
	<td><input type="datetime" name="max_date" id="max_date" disabled="disabled" style="display:none;" title="The end date of the route" /></td>
</tr>
<tr>
	<th colspan="2">Markers</th>
</tr>
<tr>
	<td colspan="2">
		<input type="checkbox" name="first_marker" id="first_marker" checked="checked" title="Whether to show a marker for the first point or not" />
		<label for="first_marker">Show First Marker</label>
	</td>
</tr>
<tr>
	<td colspan="2">
		<input type="checkbox" name="last_marker" id="last_marker" checked="checked" title="Whether to show a marker for the last point or not" />
		<label for="last_marker">Show Last Marker</label>
	</td>
</tr>
<tr>
	<td>
		<input type="checkbox" id="use_marker_every" onchange="setDisabled('marker_every', !this.checked); setDisplay('marker_every', this.checked ? 'inline-block' : 'none');" />
		<label for="use_marker_every">Marker Every:</label>
	</td>
	<td>
		<input type="number" step="1" min="1" name="marker_every" id="marker_every" disabled="disabled" value="4" style="display:none;" title="The frequency of markers" />
		point(s)
	</td>
</tr>
<tr>
	<th colspan="2">Destination</th>
</tr>
<tr>
	<td colspan="2">
		<input type="checkbox" name="destination_show" id="destination_show" title="Whether to show a marker for the destination or not" onchange="setDisabled('destination_name', !this.checked); setDisabled('destination_lon', !this.checked); setDisabled('destination_lat', !this.checked); setDisplay('destination_name_tr', this.checked ? 'table-row' : 'none'); setDisplay('destination_lat_tr', this.checked ? 'table-row' : 'none'); setDisplay('destination_lon_tr', this.checked ? 'table-row' : 'none');" />
		<label for="destination_show">Show Destination</label>
	</td>
</tr>
<tr id="destination_name_tr" style="display: none;">
	<td><label for="destination_name">Name:</label></td>
	<td><input type="text" name="destination_name" id="destination_name" title="The name of the destination" disabled="disabled" /></td>
</tr>
<tr id="destination_lat_tr" style="display: none;">
	<td><label for="destination_lat">Latitude:</label></td>
	<td><input type="number" step="0.00001" min="-90" max="90" name="destination_lat" id="destination_lat" title="In decimal: 42.5 for 45&deg;30'N" style="width: 100px" disabled="disabled" /></td>
</tr>
<tr id="destination_lon_tr" style="display: none;">
	<td><label for="destination_lon">Longitude:</label></td>
	<td><input type="number" step="0.00001" min="-180" max="180" name="destination_lon" id="destination_lon" title="In decimal: 42.5 for 45&deg;30'E" style="width: 100px" disabled="disabled" /></td>
</tr>
</table>
</div>
<div style="clear: both; margin: 8px auto 8px auto; width: 120px;">
	<input type="submit" value="Get Map" style="width: 120px;" />
</div>
</form>
</div>
</body>
</html>