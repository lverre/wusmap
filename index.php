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
 * @description Gets the Map URL.
 */

include_once "general.php";

$boats = getAllBoats();
$boats_select = "";
while ($boat = $boats->fetch_assoc()) {
	$boats_select .= "<option value=\"" . $boat['id'] . "\">" . $boat['name'] . "</option>\n";
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset=utf-8 />
	<title><?php _e("FORM"); ?></title>
	<link rel="stylesheet" href="wusmap.css" />
	<script src="wusmap.js"></script>
</head>
<body>
<div style="margin-left:auto; margin-right:auto; width: 712px">
<h1><?php _e("FORM"); ?></h1>
<div>
	<input type="radio" name="get_type" id="get_type_map" onchange="if (this.checked) { setDisplay('form_get_map', 'block'); setDisplay('form_export', 'none'); setDisplay('form_get_point', 'none'); }" checked="checked" /><label for="get_type_map"><?php _e("FORM_GET_MAP"); ?></label>
	<input type="radio" name="get_type" id="get_type_export" onchange="if (this.checked) { setDisplay('form_get_map', 'none'); setDisplay('form_export', 'block'); setDisplay('form_get_point', 'none'); }" /><label for="get_type_export"><?php _e("FORM_GET_EXPORT"); ?></label>
	<input type="radio" name="get_type" id="get_type_point" onchange="if (this.checked) { setDisplay('form_get_map', 'none'); setDisplay('form_export', 'none'); setDisplay('form_get_point', 'block'); }" /><label for="get_type_point"><?php _e("FORM_GET_POINT"); ?></label>
</div>

<form id="form_get_map" method="get" action="getmap.php">
<div style="float:left; margin: 8px 16px 8px 8px; width: 332px;">
<table>
<tr>
	<th colspan="2"><?php _e("FORM_GENERAL"); ?></th>
</tr>
<tr>
	<td><label><?php _e("FORM_OUTPUT_TITLE"); ?></label></td>
	<td>
		<input type="radio" name="output" id="output_iframe" value="iframe" onchange="if (this.checked) { setDisabled('map_div_id', true); setDisplay('map_div_id_tr', 'none'); setVisibility('misc_tr', true); setVisibility('misc_eta_multiplier_tr', true); setVisibility('misc_powered_tr', true); setVisibility('misc_big_map_tr', true); setVisibility('misc_weather_tr', true); }" checked="checked" /><label for="output_iframe"><?php _e("FORM_OUTPUT_IFRAME"); ?></label>
		<input type="radio" name="output" id="output_script" value="script" onchange="if (this.checked) { setDisabled('map_div_id', false); setDisplay('map_div_id_tr', 'table-row'); setVisibility('misc_tr', false); setVisibility('misc_eta_multiplier_tr', false); setVisibility('misc_powered_tr', false); setVisibility('misc_big_map_tr', false); setVisibility('misc_weather_tr', false); }" /><label for="output_script"><?php _e("FORM_OUTPUT_SCRIPT"); ?></label>
	</td>
</tr>
<tr>
	<td><label for="boat_id_get_map"><?php _e("SH_BOAT_TITLE"); ?></label></td>
	<td>
		<select name="boat_id" id="boat_id_get_map">
<?php echo $boats_select; ?>
		</select>
		(<a href="manageboats.php"><?php _e("FORM_MANAGE_BOATS"); ?></a>)
	</td>
</tr>
<tr id="map_div_id_tr" style="display:none;">
	<td><label for="map_div_id"><?php _e("FORM_DIV_ID_TITLE"); ?></label></td>
	<td><input type="text" name="map_div_id" id="map_div_id" value="wusmap" title="<?php _e("FORM_DIV_ID_TOOLTIP"); ?>" disabled="disabled" /></td>
</tr>
<tr>
	<th colspan="2"><?php _e("FORM_MAP"); ?></th>
</tr>
<tr>
	<td><label for="width"><?php _e("FORM_WIDTH_TITLE"); ?></label></td>
	<td><input type="text" name="width" id="width" value="500px" title="<?php _e("FORM_WIDTH_TOOLTIP"); ?>" style="width: 50px" /></td>
</tr>
<tr>
	<td><label for="height"><?php _e("FORM_HEIGHT_TITLE"); ?></label></td>
	<td><input type="text" name="height" id="height" value="500px" title="<?php _e("FORM_HEIGHT_TOOLTIP"); ?>" style="width: 50px" /></td>
</tr>
<tr>
	<td>
		<input type="checkbox" id="manual_zoom" title="<?php _e("FORM_ZOOM_TOOLTIP"); ?>" onchange="setDisabled('zoom', !this.checked);" checked="checked" />
		<label for="manual_zoom"><?php _e("FORM_ZOOM_TITLE"); ?></label>
	</td>
	<td><input type="number" step="1" min="0" max="20" name="zoom" id="zoom" value="12" title="<?php _e("FORM_ZOOM_VALUE_TOOLTIP"); ?>" style="width: 50px" /></td>
</tr>
<tr>
	<td colspan="2">
		<input type="checkbox" id="manual_center" title="<?php _e("FORM_CENTER_TOOLTIP"); ?>" onchange="setDisabled('center_lat', !this.checked); setDisabled('center_lon', !this.checked);" />
		<label for="manual_center"><?php _e("FORM_CENTER_TITLE"); ?></label>
	</td>
</tr>
<tr id="center_lat_tr">
	<td><label for="center_lat"><?php _e("SH_LAT_TITLE"); ?></label></td>
	<td><input type="number" step="0.00001" min="-90" max="90" name="center_lat" id="center_lat" title="<?php _e("SH_LAT_TOOLTIP"); ?>" style="width: 100px" disabled="disabled" /></td>
</tr>
<tr id="center_lon_tr">
	<td><label for="center_lon"><?php _e("SH_LON_TITLE"); ?></label></td>
	<td><input type="number" step="0.00001" min="-180" max="180" name="center_lon" id="center_lon" title="<?php _e("SH_LON_TOOLTIP"); ?>" style="width: 100px" disabled="disabled" /></td>
</tr>
<tr>
	<td><label for="map_type"><?php _e("FORM_TYPE_TITLE"); ?></label></td>
	<td>
		<select name="map_type" id="map_type">
			<option value="ROADMAP"><?php _e("FORM_TYPE_ROADS"); ?></option>
			<option value="SATELLITE"><?php _e("FORM_TYPE_SATELLITE"); ?></option>
			<option value="TERRAIN"><?php _e("FORM_TYPE_TERRAIN"); ?></option>
			<option value="HYBRID" selected="selected"><?php _e("FORM_TYPE_HYBRID"); ?></option>
		</select>
	</td>
</tr>
<tr>
	<td colspan="2">
		<input type="checkbox" name="navigation_control" id="navigation_control" checked="checked" title="<?php _e("FORM_CTRL_NAVIGATION_TOOLTIP"); ?>" />
		<label for="navigation_control"><?php _e("FORM_CTRL_NAVIGATION_TITLE"); ?></label>
	</td>
</tr>
<tr>
	<td colspan="2">
		<input type="checkbox" name="map_type_control" id="map_type_control" checked="checked" title="<?php _e("FORM_CTRL_TYPE_TOOLTIP"); ?>" />
		<label for="map_type_control"><?php _e("FORM_CTRL_TYPE_TITLE"); ?></label>
	</td>
</tr>
<tr>
	<td colspan="2">
		<input type="checkbox" name="scale_control" id="scale_control" checked="checked" title="<?php _e("FORM_CTRL_SCALE_TOOLTIP"); ?>" />
		<label for="scale_control"><?php _e("FORM_CTRL_SCALE_TITLE"); ?></label>
	</td>
</tr>
<tr>
	<th colspan="2"><?php _e("FORM_ROUTE"); ?></th>
</tr>
<tr>
	<td><label for="route_color"><?php _e("FORM_ROUTE_COLOR_TITLE"); ?></label></td>
	<td><input type="color" name="route_color" id="route_color" value="#008000" title="<?php _e("FORM_ROUTE_COLOR_TOOLTIP"); ?>" /></td>
</tr>
<tr>
	<td><label for="route_opacity"><?php _e("FORM_ROUTE_OPACITY_TITLE"); ?></label></td>
	<td><input type="number" step="0.001"" min="0" max="1" name="route_opacity" id="route_opacity" value="1" title="<?php _e("FORM_ROUTE_OPACITY_TOOLTIP"); ?>" style="width: 50px" /></td>
</tr>
<tr>
	<td><label for="route_weight"><?php _e("FORM_ROUTE_WEIGHT_TITLE"); ?></label></td>
	<td><input type="number" step="1" min="0" max="100" name="route_weight" id="route_weight" value="2" title="<?php _e("FORM_ROUTE_WEIGHT_TOOLTIP"); ?>" style="width: 50px" /></td>
</tr>
</table>
</div>
<div style="float:left; margin: 8px 8px 8px 16px; width: 332px;">
<table>
<tr>
	<th colspan="2"><?php _e("FORM_DATES"); ?></th>
</tr>
<tr>
	<td>
		<input type="checkbox" id="use_min_date" onchange="setDisabled('min_date', !this.checked);" />
		<label for="use_min_date"><?php _e("FORM_DATES_MIN_TITLE"); ?></label>
	</td>
	<td><input type="datetime" name="min_date" id="min_date" disabled="disabled" title="<?php _e("FORM_DATES_MIN_TOOLTIP"); ?>" /></td>
</tr>
<tr>
	<td>
		<input type="checkbox" id="use_max_date" onchange="setDisabled('max_date', !this.checked);" />
		<label for="use_max_date"><?php _e("FORM_DATES_MAX_TITLE"); ?></label>
	</td>
	<td><input type="datetime" name="max_date" id="max_date" disabled="disabled" title="<?php _e("FORM_DATES_MAX_TOOLTIP"); ?>" /></td>
</tr>
<tr>
	<th colspan="2"><?php _e("FORM_MARKERS"); ?></th>
</tr>
<tr>
	<td colspan="2">
		<input type="checkbox" name="first_marker" id="first_marker" checked="checked" title="<?php _e("FORM_MARKERS_FIRST_TOOLTIP"); ?>" />
		<label for="first_marker"><?php _e("FORM_MARKERS_FIRST_TITLE"); ?></label>
	</td>
</tr>
<tr>
	<td colspan="2">
		<input type="checkbox" name="last_marker" id="last_marker" checked="checked" title="<?php _e("FORM_MARKERS_LAST_TOOLTIP"); ?>" />
		<label for="last_marker"><?php _e("FORM_MARKERS_LAST_TITLE"); ?></label>
	</td>
</tr>
<tr>
	<td>
		<input type="checkbox" id="use_marker_every" onchange="setDisabled('marker_every', !this.checked);" />
		<label for="use_marker_every"><?php _e("FORM_MARKERS_EVERY_TITLE"); ?></label>
	</td>
	<td>
		<input type="number" step="1" min="1" name="marker_every" id="marker_every" disabled="disabled" value="4" title="<?php _e("FORM_MARKERS_EVERY_TOOLTIP"); ?>" />
		<?php _e("FORM_MARKERS_EVERY_POINTS"); ?>
	</td>
</tr>
<tr>
	<th colspan="2"><?php _e("FORM_DEST"); ?></th>
</tr>
<tr>
	<td colspan="2">
		<input type="checkbox" id="destination_show" title="<?php _e("FORM_DEST_SHOW_TOOLTIP"); ?>" onchange="setDisabled('destination_name', !this.checked); setDisabled('destination_lon', !this.checked); setDisabled('destination_lat', !this.checked);" />
		<label for="destination_show"><?php _e("FORM_DEST_SHOW_TITLE"); ?></label>
	</td>
</tr>
<tr id="destination_name_tr">
	<td><label for="destination_name"><?php _e("SH_NAME_TITLE"); ?></label></td>
	<td><input type="text" name="destination_name" id="destination_name" title="<?php _e("FORM_DEST_NAME_TOOLTIP"); ?>" disabled="disabled" /></td>
</tr>
<tr id="destination_lat_tr">
	<td><label for="destination_lat"><?php _e("SH_LAT_TITLE"); ?></label></td>
	<td><input type="number" step="0.00001" min="-90" max="90" name="destination_lat" id="destination_lat" title="<?php _e("SH_LAT_TOOLTIP"); ?>" style="width: 100px" disabled="disabled" /></td>
</tr>
<tr id="destination_lon_tr">
	<td><label for="destination_lon"><?php _e("SH_LON_TITLE"); ?></label></td>
	<td><input type="number" step="0.00001" min="-180" max="180" name="destination_lon" id="destination_lon" title="<?php _e("SH_LON_TOOLTIP"); ?>" style="width: 100px" disabled="disabled" /></td>
</tr>
<tr id="misc_tr">
	<th colspan="2"><?php _e("FORM_MISC"); ?></th>
</tr>
<tr id="misc_eta_multiplier_tr">
	<td><label for="eta_multiplier"><?php _e("FORM_MISC_ETA_MULTIPLIER_TITLE"); ?></label></td>
	<td><input type="number" step="0.01" min="0" value="1" name="eta_multiplier" id="eta_multiplier" title="<?php _e("FORM_MISC_ETA_MULTIPLIER_TOOLTIP"); ?>" style="width: 50px" /></td>
</tr>
<tr id="misc_powered_tr">
	<td colspan="2">
		<input type="checkbox" name="show_powered" id="show_powered" checked="checked" />
		<label for="show_powered"><?php _e("FORM_MISC_POWERED_TITLE"); ?></label>
	</td>
</tr>
<tr id="misc_big_map_tr">
	<td colspan="2">
		<input type="checkbox" name="show_big_map" id="show_big_map" checked="checked" />
		<label for="show_big_map"><?php _e("FORM_MISC_BIG_MAP_TITLE"); ?></label>
	</td>
</tr>
<tr id="misc_weather_tr">
	<td colspan="2">
		<input type="checkbox" name="show_weather" id="show_weather" checked="checked" />
		<label for="show_weather"><?php _e("FORM_MISC_WEATHER_TITLE"); ?></label>
	</td>
</tr>
</table>
</div>
<div style="clear: both; margin: 8px auto 8px auto; width: 120px;">
	<input type="submit" value="<?php _e("SH_SUBMIT"); ?>" style="width: 120px;" />
</div>
</form>

<form id="form_export" method="get" action="export.php" style="display: none;">
<div style="float:left; margin: 8px 16px 8px 8px; width: 696px;">
<table>
<tr>
	<th colspan="2"><?php _e("FORM_GENERAL"); ?></th>
</tr>
<tr>
	<td><label for="format"><?php _e("FORM_FORMAT_TITLE"); ?></label></td>
	<td>
		<select name="format" id="format">
<?php
if ($handle = opendir('export')) {
    while (false !== ($entry = readdir($handle))) {
		if (endsWith($entry, ".php")) {
			$value = substr($entry, 0, strlen($entry) - 4);
			echo "<option value=\"$value\">$value</option>\n";
		}
    }
}
?>
		</select>
	</td>
</tr>
<tr>
	<td><label for="boat_id_export"><?php _e("SH_BOAT_TITLE"); ?></label></td>
	<td>
		<select name="boat_id" id="boat_id_export">
<?php echo $boats_select; ?>
		</select>
		(<a href="manageboats.php"><?php _e("FORM_MANAGE_BOATS"); ?></a>)
	</td>
</tr>
<tr>
	<th colspan="2"><?php _e("FORM_DATES"); ?></th>
</tr>
<tr>
	<td>
		<input type="checkbox" id="use_min_date_point" onchange="setDisabled('min_date_point', !this.checked);" />
		<label for="use_min_date_point"><?php _e("FORM_DATES_MIN_TITLE"); ?></label>
	</td>
	<td><input type="datetime" name="min_date" id="min_date_point" disabled="disabled" title="<?php _e("FORM_DATES_MIN_TOOLTIP"); ?>" /></td>
</tr>
<tr>
	<td>
		<input type="checkbox" id="use_max_date_point" onchange="setDisabled('max_date_point', !this.checked);" />
		<label for="use_max_date_point"><?php _e("FORM_DATES_MAX_TITLE"); ?></label>
	</td>
	<td><input type="datetime" name="max_date" id="max_date_point" disabled="disabled" title="<?php _e("FORM_DATES_MAX_TOOLTIP"); ?>" /></td>
</tr>
</table>
</div>
<div style="clear: both; margin: 8px auto 8px auto; width: 120px;">
	<input type="submit" value="<?php _e("SH_SUBMIT"); ?>" style="width: 120px;" />
</div>
</form>

<form id="form_get_point" method="get" action="getpoint.php" style="display: none;">
<div style="float:left; margin: 8px 16px 8px 8px; width: 696px;">
<table>
<tr>
	<th colspan="2"><?php _e("FORM_GENERAL"); ?></th>
</tr>
<tr>
	<td><label for="boat_id_get_point"><?php _e("SH_BOAT_TITLE"); ?></label></td>
	<td>
		<select name="boat_id" id="boat_id_get_point">
<?php echo $boats_select; ?>
		</select>
		(<a href="manageboats.php"><?php _e("FORM_MANAGE_BOATS"); ?></a>)
	</td>
</tr>
<tr>
	<th colspan="2"><?php _e("FORM_POINT_POINT"); ?></th>
</tr>
<tr>
	<td colspan="2">
		<input type="checkbox" id="specific_date" onchange="setDisplay('order_not_specific', this.checked ? 'none' : 'inline'); setDisplay('order_specific', this.checked ? 'inline' : 'none'); setDisabled('point_date', !this.checked); " />
		<label for="specific_date"><?php _e("FORM_POINT_SPECIFIC_DATE_TITLE"); ?></label>
	</td>
</tr>
<tr>
	<td><label for="point_date"><?php _e("SH_DATETIME_TITLE"); ?></label></td>
	<td><input type="text" name="date" id="point_date" title="<?php _e("SH_DATETIME_TOOLTIP"); ?>" disabled="disabled" /></td>
</tr>
<tr>
	<td><?php _e("FORM_POINT_ORDER_TITLE"); ?></td>
	<td>
		<div id="order_not_specific">
			<input type="radio" name="order" id="order_last" value="last" checked="checked" /><label for="order_last"><?php _e("FORM_POINT_ORDER_LAST_TITLE"); ?></label>
			<input type="radio" name="order" id="order_first" value="first" /><label for="order_first"><?php _e("FORM_POINT_ORDER_FIRST_TITLE"); ?></label>
		</div>
		<div id="order_specific" style="display: none;">
			<input type="radio" name="order" id="order_lt" value="<" /><label for="order_lt"><?php _e("FORM_POINT_ORDER_LT_TITLE"); ?></label>
			<input type="radio" name="order" id="order_lte" value="<=" /><label for="order_lte"><?php _e("FORM_POINT_ORDER_LTE_TITLE"); ?></label>
			<input type="radio" name="order" id="order_gt" value=">" /><label for="order_gt"><?php _e("FORM_POINT_ORDER_GT_TITLE"); ?></label>
			<input type="radio" name="order" id="order_gte" value=">=" /><label for="order_gte"><?php _e("FORM_POINT_ORDER_GTE_TITLE"); ?></label>
		</div>
	</td>
</tr>
<tr>
	<th colspan="2"><?php _e("FORM_POINT_OUTPUT"); ?></th>
</tr>
<tr>
	<td colspan="2"><textarea name="output" id="point_output" title="<?php _e("FORM_POINT_OUTPUT_TOOLTIP"); ?>" style="width: 100%">$L ; $l</textarea></td>
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