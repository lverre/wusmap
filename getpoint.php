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
 * @description Gets the information for a point.
 */

include_once "general.php";

$boat_id = getOrDefault("boat_id", null);
$date = getOrDefault("date", null);
$order = getOrDefault("order", null);
$output = getOrDefault("output", "\$L ; \$l");

if ($boat_id == null || !is_numeric($boat_id)) die("You must choose a valid boat");

$where = "boat_id=$boat_id";
$is_desc = true;
if ($date != null) {
	$comparator = null;
	switch ($order) {
		case "before":
		case "<":
			$is_desc = true;
			$comparator = "<";
			break;
		case "after":
		case ">":
			$is_desc = false;
			$comparator = ">";
			break;
		case "<=":
			$is_desc = true;
			$comparator = "<=";
			break;
		case ">=":
			$is_desc = false;
			$comparator = ">=";
			break;
		default:
			die("Invalid order");
			break;
	}
	$where .= " and time $comparator '$date'";
} else {
	if ($order == null) $order = "last";
	switch ($order) {
		case "last":
			$is_desc = true;
			break;
		case "first":
			$is_desc = false;
			break;
		default:
			die("Invalid order");
			break;
	}
}
$order_by = "time " . ($is_desc ? "desc" : "asc");

$sql = "select * from " . $CONFIG['db_prefix'] . "points where $where order by $order_by limit 1;";

$point = executeSQLOne($sql);

if ($point != null) {
	$chars = str_split($output);
	$special = false;
	foreach ($chars as $char) {
		if ($special && $char != "\$") {
			echo getValue($char, $point);
			$special = false;
		} else if (!$special && $char == "\$") {
			$special = true;
		} else {
			echo $char;
			$special = false;
		}
	}
}

function getValue($char, $point) {
	switch ($char) {
		case "L":
			return coordToString($point['latitude'], true);
		case "l":
			return coordToString($point['longitude'], false);
		case "s":
			return speedToString($point['speed']);
		case "h":
			return headingToString($point['heading']);
		case "H":
			return headingToHRString($point['heading']);
		case "t":
			return parseDate(strtotime($point['time']));
		default:
			die("Unkown param: $char");
	}
}

?>
