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
 * @description Gets the map.
 */

include_once "general.php";

$map_div_id = getOrDefault("map_div_id", "wusmap");
$width = getOrDefault("width", 500);
$height = getOrDefault("height", 500);
$zoom = getOrDefault("zoom", null);
$navigation_control = getOrDefault("navigation_control", false) == 'on' ? 'true' : 'false';
$map_type_control = getOrDefault("map_type_control", 1) == 'on' ? 'true' : 'false';
$scale_control = getOrDefault("scale_control", 1) == 'on' ? 'true' : 'false';
$map_type = getOrDefault("map_type", "HYBRID");
$route_color = getOrDefault("route_color", "green");
$route_opacity = getOrDefault("route_opacity", 1);
$route_weight = getOrDefault("route_weight", 2);
$asset_id = getOrDefault("asset_id", null);
$min_date = getOrDefault("min_date", null);
$max_date = getOrDefault("max_date", null);

$js = getMap($map_div_id, $width, $height, $zoom, $navigation_control, $map_type_control, $scale_control, $map_type, $route_color, $route_opacity, $route_weight, $asset_id, $min_date, $max_date);

if (getOrDefault("output", null) != "iframe") {
	echo $js;
} else {
?>
<html>
<head>
	<meta charset=utf-8 />
	<title>Route</title>
	<link rel="stylesheet" href="wusmap.css" />
	<script src="http://maps.google.com/maps/api/js?sensor=false"></script>
	<script src="infobox_packed.js"></script>
	<script src="wusmap.js"></script>
	<script>
<?php echo $js; ?>
	</script>
</head>
<body>
	<div id="wusmap"></div>
</body>
</html>
<?php
}
?>