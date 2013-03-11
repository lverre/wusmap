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
 * @description Exports the points to CSV.
 */

if (!isset($_REQUEST["newline"])) $nl = "\r\n";// Default newline is \r\n (RFC 4180)
$sep = getOrDefault("separator", ",");

header("Content-Type: text/csv");
header("Content-Disposition: inline; filename=\"$filename.csv\";");

$first_pass = true;
while ($point = $points->fetch_assoc()) {
	$title = null;
	$row = null;
	foreach ($point as $key => $value) {
		if ($key != 'boat_id') {
			if ($first_pass) {
				if ($title != null) $title .= $sep;
				$title .= $key;
			}
			if ($row != null) $row .= $sep;
			$row .= $value;
		}
	}
	if ($first_pass) {
		echo $title . "$nl";
		$first_pass = false;
	}
	echo $row . "$nl";
}

?>