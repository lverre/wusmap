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
</head>
<body>
<h1>Get Wusmap Map</h1>
<form method="get" action="getmap.php">
<table>
<tr>
	<th colspan="2">General</th>
</tr>
<tr>
	<td><label>Output:</label></td>
	<td>
		<input type="radio" name="output" id="output_iframe" value="iframe" onchange="if (this.checked) { document.getElementById('map_div_id').disabled = true; document.getElementById('map_div_id_tr').style.display = 'none'; }" checked="checked" /><label for="output_script">IFrame</label>
		<input type="radio" name="output" id="output_script" value="script" onchange="if (this.checked) { document.getElementById('map_div_id').disabled = false; document.getElementById('map_div_id_tr').style.display = 'table-row'; }" /><label for="output_script">Script</label>
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
<tr>
	<th colspan="2">Map</th>
</tr>
<tr>
	<td><label for="width">Width:</label></td>
	<td><input type="number" name="width" id="width" value="500" title="Width of the map (eg. 100%, 600px, ...)" style="width: 50px" /></td>
</tr>
<tr>
	<td><label for="height">Height:</label></td>
	<td><input type="number" name="height" id="height" value="500" title="Height of the map (eg. 100%, 600px, ...)" style="width: 50px" /></td>
</tr>
<tr>
	<td>
		<input type="checkbox" id="manual_zoom" title="Whether to use auto-zoom or not" onchange="document.getElementById('zoom').disabled = !this.checked; document.getElementById('zoom').style.display = this.checked ? 'table-row' : 'none';" checked="checked" />
		<label for="manual_zoom">Manual Zoom</label>
	</td>
	<td><input type="number" min="0" max="20" name="zoom" id="zoom" value="12" title="Zoom of the map when loading (1 to 20)" style="width: 50px" /></td>
</tr>
<tr>
	<td colspan="2">
		<input type="checkbox" name="navigation_control" id="navigation_control" checked="checked" title="Whether to show the navigation control in the map or not" />
		<label for="navigation_control">Navigation Control</label></td>
	</td>
</tr>
<tr>
	<td colspan="2">
		<input type="checkbox" name="map_type_control" id="map_type_control" checked="checked" title="Whether to show the map type control in the map or not" />
		<label for="map_type_control">Map Type Control</label></td>
	</td>
</tr>
<tr>
	<td colspan="2">
		<input type="checkbox" name="scale_control" id="scale_control" checked="checked" title="Whether to show the scale control in the map or not" />
		<label for="scale_control">Scale Control</label>
	</td>
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
	<th colspan="2">Route</th>
</tr>
<tr>
	<td><label for="route_color">Route Color:</label></td>
	<td><input type="color" name="route_color" id="route_color" value="#008000" title="The color of the route's line (in HTML hex style ie. '#FFAA00', or default value ie. 'blue')" /></td>
</tr>
<tr>
	<td><label for="route_opacity">Route Opacity:</label></td>
	<td><input type="number" min="0" max="1" name="route_opacity" id="route_opacity" value="1" title="The opacity of the route's line (0.0 is transparent, 1.0 is opaque)" style="width: 50px" /></td>
</tr>
<tr>
	<td><label for="route_weight">Route Weight:</label></td>
	<td><input type="number" min="0" max="100" name="route_weight" id="route_weight" value="2" title="The width of the route's line (in pixels)" style="width: 50px" /></td>
</tr>
<tr>
	<th colspan="2">Dates</th>
</tr>
<tr>
	<td>
		<input type="checkbox" id="use_min_date" onchange="var input = document.getElementById('min_date'); if (this.checked) { input.disabled = false; input.style.display='table-row'; } else { input.disabled = true; input.style.display='none'; }" />
		<label for="use_min_date">Min Date:</label>
	</td>
	<td><input type="datetime" name="min_date" id="min_date" disabled="disabled" style="display:none;" title="The start date of the route" /></td>
</tr>
<tr>
	<td>
		<input type="checkbox" id="use_max_date" onchange="var input = document.getElementById('max_date'); if (this.checked) { input.disabled = false; input.style.display='table-row'; } else { input.disabled = true; input.style.display='none'; }" />
		<label for="use_max_date">Max Date:</label>
	</td>
	<td><input type="datetime" name="max_date" id="max_date" disabled="disabled" style="display:none;" title="The end date of the route" /></td>
</tr>
</table>
<input type="submit" value="Get Map" />
</form>
</body>
</html>