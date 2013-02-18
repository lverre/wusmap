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

if (isset($_POST['operation'])) {
	global $CONFIG;
	$table_name = $CONFIG['db_prefix'] . "assets";
	switch ($_POST['operation']) {
	case "add":
		executeSQL("insert into $table_name ( id, name ) values ( " . $_POST['id'] . ", '" . $_POST['name'] . "' )");
		break;
	case "edit":
		executeSQL("update $table_name set name = '" . $_POST['name'] . "' where id = " . $_POST['assets']);
		break;
	case "delete":
		executeSQL("delete from $table_name where id = " . $_POST['assets']);
		break;
	}
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset=utf-8 />
	<title>Manage Wusmap Assets</title>
	<link rel="stylesheet" href="wusmap.css" />
	<script src="wusmap.js"></script>
</head>
<body>
<h1>Manage Wusmap Assets</h1>
<form method="post">
<table>
<tr>
	<td><label>Operation:</label></td>
	<td>
		<input type="radio" name="operation" id="operation_add" value="add" onchange="if (this.checked) { setDisplay('assets_tr', 'none'); setDisplay('name_tr', 'table-row'); setDisplay('id_tr', 'table-row'); }" checked="checked" /><label for="operation_add">Add</label>
		<input type="radio" name="operation" id="operation_edit" value="edit" onchange="if (this.checked) { setDisplay('assets_tr', 'table-row'); setDisplay('name_tr', 'table-row'); setDisplay('id_tr', 'none'); }" /><label for="operation_edit">Edit</label>
		<input type="radio" name="operation" id="operation_delete" value="delete" onchange="if (this.checked) { setDisplay('assets_tr', 'table-row'); setDisplay('name_tr', 'none'); setDisplay('id_tr', 'none'); }" /><label for="operation_delete">Delete</label>
	</td>
</tr>
<tr id="assets_tr" style="display: none;">
	<td><label for="assets">Asset:</label></td>
	<td>
		<select name="assets" id="assets">
<?php
$assets = getAllAssets();
while ($asset = $assets->fetch_assoc()) {
	echo "<option value=\"" . $asset['id'] . "\">" . $asset['name'] . "</option>\n";
}
?>
		</select>
	</td>
</tr>
<tr id="id_tr">
	<td><label for="name">Id:</label></td>
	<td><input type="number" name="id" id="id" title="The id of the asset" /></td>
</tr>
<tr id="name_tr">
	<td><label for="name">Name:</label></td>
	<td><input type="text" name="name" id="name" title="The name of the asset" /></td>
</tr>
</table>
<input type="submit" value="Submit" />
</form>
</body>
</html>