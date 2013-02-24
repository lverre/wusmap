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
 * @description Gets the URL.
 */

include_once "general.php";
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset=utf-8 />
	<title><?php _e("GET_MAP"); ?></title>
	<link rel="stylesheet" href="wusmap.css" />
	<script src="wusmap.js"></script>
</head>
<body>
<div style="margin-left:auto; margin-right:auto; width: 648px">
<h1><?php _e("GET_MAP"); ?></h1>
<form method="get" action="getmap.php">
<div style="clear: both; margin: 16px 8px 8px 8px;">
<table>
<tr>
	<th colspan="2"><?php _e("GET_MAP_GENERAL"); ?></th>
</tr>
<tr>
	<td><label><?php _e("GET_MAP_OUTPUT_TITLE"); ?></label></td>
	<td>
		<input type="radio" name="output" id="output_iframe" value="iframe" onchange="if (this.checked) { setDisabled('map_div_id', true); setDisplay('map_div_id_tr', 'none'); }" checked="checked" /><label for="output_iframe"><?php _e("GET_MAP_OUTPUT_IFRAME"); ?></label>
		<input type="radio" name="output" id="output_script" value="script" onchange="if (this.checked) { setDisabled('map_div_id', false); setDisplay('map_div_id_tr', 'table-row'); }" /><label for="output_script"><?php _e("GET_MAP_OUTPUT_SCRIPT"); ?></label>
	</td>
</tr>
<tr>
	<td><label for="asset_id"><?php _e("SH_BOAT_TITLE"); ?></label></td>
	<td>
		<select name="asset_id" id="asset_id">
<?php
$assets = getAllAssets();
while ($asset = $assets->fetch_assoc()) {
	echo "<option value=\"" . $asset['id'] . "\">" . $asset['name'] . "</option>\n";
}
?>
		</select>
		(<a href="manageassets.php"><?php _e("GET_MAP_MANAGE_BOATS"); ?></a>)
	</td>
</tr>
<tr id="map_div_id_tr" style="display:none;">
	<td><label for="map_div_id"><?php _e("GET_MAP_DIV_ID_TITLE"); ?></label></td>
	<td><input type="text" name="map_div_id" id="map_div_id" value="wusmap" title="<?php _e("GET_MAP_DIV_ID_TOOLTIP"); ?>" disabled="disabled" /></td>
</tr>
</table>
</div>
<div style="float:left; margin: 8px 16px 8px 8px; width: 300px;">
<table>
<tr>
	<th colspan="2"><?php _e("GET_MAP_MAP"); ?></th>
</tr>
<tr>
	<td><label for="width"><?php _e("GET_MAP_WIDTH_TITLE"); ?></label></td>
	<td><input type="number" step="1" name="width" id="width" value="500" title="<?php _e("GET_MAP_WIDTH_TOOLTIP"); ?>" style="width: 50px" /></td>
</tr>
<tr>
	<td><label for="height"><?php _e("GET_MAP_HEIGHT_TITLE"); ?></label></td>
	<td><input type="number" step="1" name="height" id="height" value="500" title="<?php _e("GET_MAP_HEIGHT_TOOLTIP"); ?>" style="width: 50px" /></td>
</tr>
<tr>
	<td>
		<input type="checkbox" id="manual_zoom" title="<?php _e("GET_MAP_ZOOM_TOOLTIP"); ?>" onchange="setDisabled('zoom', !this.checked); setDisplay('zoom', this.checked ? 'inline-block' : 'none');" checked="checked" />
		<label for="manual_zoom"><?php _e("GET_MAP_ZOOM_TITLE"); ?></label>
	</td>
	<td><input type="number" step="1" min="0" max="20" name="zoom" id="zoom" value="12" title="<?php _e("GET_MAP_ZOOM_VALUE_TOOLTIP"); ?>" style="width: 50px" /></td>
</tr>
<tr>
	<td colspan="2">
		<input type="checkbox" id="manual_center" title="<?php _e("GET_MAP_CENTER_TOOLTIP"); ?>" onchange="setDisabled('center_lat', !this.checked); setDisplay('center_lat_tr', this.checked ? 'table-row' : 'none'); setDisabled('center_lon', !this.checked); setDisplay('center_lon_tr', this.checked ? 'table-row' : 'none');" />
		<label for="manual_center"><?php _e("GET_MAP_CENTER_TITLE"); ?></label>
	</td>
</tr>
<tr id="center_lat_tr" style="display: none;">
	<td><label for="center_lat"><?php _e("SH_LAT_TITLE"); ?></label></td>
	<td><input type="number" step="0.00001" min="-90" max="90" name="center_lat" id="center_lat" title="<?php _e("SH_LAT_TOOLTIP"); ?>" style="width: 100px" disabled="disabled" /></td>
</tr>
<tr id="center_lon_tr" style="display: none;">
	<td><label for="center_lon"><?php _e("SH_LON_TITLE"); ?></label></td>
	<td><input type="number" step="0.00001" min="-180" max="180" name="center_lon" id="center_lon" title="<?php _e("SH_LON_TOOLTIP"); ?>" style="width: 100px" disabled="disabled" /></td>
</tr>
<tr>
	<td><label for="map_type"><?php _e("GET_MAP_TYPE_TITLE"); ?></label></td>
	<td>
		<select name="map_type" id="map_type">
			<option value="ROADMAP"><?php _e("GET_MAP_TYPE_ROADS"); ?></option>
			<option value="SATELLITE"><?php _e("GET_MAP_TYPE_SATELLITE"); ?></option>
			<option value="TERRAIN"><?php _e("GET_MAP_TYPE_TERRAIN"); ?></option>
			<option value="HYBRID" selected="selected"><?php _e("GET_MAP_TYPE_HYBRID"); ?></option>
		</select>
	</td>
</tr>
<tr>
	<td colspan="2">
		<input type="checkbox" name="navigation_control" id="navigation_control" checked="checked" title="<?php _e("GET_MAP_CTRL_NAVIGATION_TOOLTIP"); ?>" />
		<label for="navigation_control"><?php _e("GET_MAP_CTRL_NAVIGATION_TITLE"); ?></label>
	</td>
</tr>
<tr>
	<td colspan="2">
		<input type="checkbox" name="map_type_control" id="map_type_control" checked="checked" title="<?php _e("GET_MAP_CTRL_TYPE_TOOLTIP"); ?>" />
		<label for="map_type_control"><?php _e("GET_MAP_CTRL_TYPE_TITLE"); ?></label>
	</td>
</tr>
<tr>
	<td colspan="2">
		<input type="checkbox" name="scale_control" id="scale_control" checked="checked" title="<?php _e("GET_MAP_CTRL_SCALE_TOOLTIP"); ?>" />
		<label for="scale_control"><?php _e("GET_MAP_CTRL_SCALE_TITLE"); ?></label>
	</td>
</tr>
<tr>
	<th colspan="2"><?php _e("GET_MAP_ROUTE"); ?></th>
</tr>
<tr>
	<td><label for="route_color"><?php _e("GET_MAP_ROUTE_COLOR_TITLE"); ?></label></td>
	<td><input type="color" name="route_color" id="route_color" value="#008000" title="<?php _e("GET_MAP_ROUTE_COLOR_TOOLTIP"); ?>" /></td>
</tr>
<tr>
	<td><label for="route_opacity"><?php _e("GET_MAP_ROUTE_OPACITY_TITLE"); ?></label></td>
	<td><input type="number step="0.001"" min="0" max="1" name="route_opacity" id="route_opacity" value="1" title="<?php _e("GET_MAP_ROUTE_OPACITY_TOOLTIP"); ?>" style="width: 50px" /></td>
</tr>
<tr>
	<td><label for="route_weight"><?php _e("GET_MAP_ROUTE_WEIGHT_TITLE"); ?></label></td>
	<td><input type="number" step="1" min="0" max="100" name="route_weight" id="route_weight" value="2" title="<?php _e("GET_MAP_ROUTE_WEIGHT_TOOLTIP"); ?>" style="width: 50px" /></td>
</tr>
</table>
</div>
<div style="float:left; margin: 8px 8px 8px 16px; width: 300px;">
<table>
<tr>
	<th colspan="2"><?php _e("GET_MAP_DATES"); ?></th>
</tr>
<tr>
	<td>
		<input type="checkbox" id="use_min_date" onchange="setDisabled('min_date', !this.checked); setDisplay('min_date', this.checked ? 'inline-block' : 'none');" />
		<label for="use_min_date"><?php _e("GET_MAP_DATES_MIN_TITLE"); ?></label>
	</td>
	<td><input type="datetime" name="min_date" id="min_date" disabled="disabled" style="display:none;" title="<?php _e("GET_MAP_DATES_MIN_TOOLTIP"); ?>" /></td>
</tr>
<tr>
	<td>
		<input type="checkbox" id="use_max_date" onchange="setDisabled('max_date', !this.checked); setDisplay('max_date', this.checked ? 'inline-block' : 'none');" />
		<label for="use_max_date"><?php _e("GET_MAP_DATES_MAX_TITLE"); ?></label>
	</td>
	<td><input type="datetime" name="max_date" id="max_date" disabled="disabled" style="display:none;" title="<?php _e("GET_MAP_DATES_MAX_TOOLTIP"); ?>" /></td>
</tr>
<tr>
	<th colspan="2"><?php _e("GET_MAP_MARKERS"); ?></th>
</tr>
<tr>
	<td colspan="2">
		<input type="checkbox" name="first_marker" id="first_marker" checked="checked" title="<?php _e("GET_MAP_MARKERS_FIRST_TOOLTIP"); ?>" />
		<label for="first_marker"><?php _e("GET_MAP_MARKERS_FIRST_TITLE"); ?></label>
	</td>
</tr>
<tr>
	<td colspan="2">
		<input type="checkbox" name="last_marker" id="last_marker" checked="checked" title="<?php _e("GET_MAP_MARKERS_LAST_TOOLTIP"); ?>" />
		<label for="last_marker"><?php _e("GET_MAP_MARKERS_LAST_TITLE"); ?></label>
	</td>
</tr>
<tr>
	<td>
		<input type="checkbox" id="use_marker_every" onchange="setDisabled('marker_every', !this.checked);" />
		<label for="use_marker_every"><?php _e("GET_MAP_MARKERS_EVERY_TITLE"); ?></label>
	</td>
	<td>
		<input type="number" step="1" min="1" name="marker_every" id="marker_every" disabled="disabled" value="4" title="<?php _e("GET_MAP_MARKERS_EVERY_TOOLTIP"); ?>" />
		<?php _e("GET_MAP_MARKERS_EVERY_POINTS"); ?>
	</td>
</tr>
<tr>
	<th colspan="2"><?php _e("GET_MAP_DEST"); ?></th>
</tr>
<tr>
	<td colspan="2">
		<input type="checkbox" id="destination_show" title="<?php _e("GET_MAP_DEST_SHOW_TOOLTIP"); ?>" onchange="setDisabled('destination_name', !this.checked); setDisabled('destination_lon', !this.checked); setDisabled('destination_lat', !this.checked); setDisplay('destination_name_tr', this.checked ? 'table-row' : 'none'); setDisplay('destination_lat_tr', this.checked ? 'table-row' : 'none'); setDisplay('destination_lon_tr', this.checked ? 'table-row' : 'none');" />
		<label for="destination_show"><?php _e("GET_MAP_DEST_SHOW_TITLE"); ?></label>
	</td>
</tr>
<tr id="destination_name_tr" style="display: none;">
	<td><label for="destination_name"><?php _e("SH_NAME_TITLE"); ?></label></td>
	<td><input type="text" name="destination_name" id="destination_name" title="<?php _e("GET_MAP_DEST_NAME_TOOLTIP"); ?>" disabled="disabled" /></td>
</tr>
<tr id="destination_lat_tr" style="display: none;">
	<td><label for="destination_lat"><?php _e("SH_LAT_TITLE"); ?></label></td>
	<td><input type="number" step="0.00001" min="-90" max="90" name="destination_lat" id="destination_lat" title="<?php _e("SH_LAT_TOOLTIP"); ?>" style="width: 100px" disabled="disabled" /></td>
</tr>
<tr id="destination_lon_tr" style="display: none;">
	<td><label for="destination_lon"><?php _e("SH_LON_TITLE"); ?></label></td>
	<td><input type="number" step="0.00001" min="-180" max="180" name="destination_lon" id="destination_lon" title="<?php _e("SH_LON_TOOLTIP"); ?>" style="width: 100px" disabled="disabled" /></td>
</tr>
</table>
</div>
<div style="clear: both; margin: 8px auto 8px auto; width: 120px;">
	<input type="submit" value="<?php _e("SH_SUBMIT"); ?>" style="width: 120px;" />
</div>
</form>
</div>
</body>
</html>