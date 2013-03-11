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

if (isset($_POST['operation'])) {
	global $CONFIG;
	$table_name = $CONFIG['db_prefix'] . "boats";
	switch ($_POST['operation']) {
	case "add":
		executeSQL("insert into $table_name ( id, name ) values ( " . $_POST['id'] . ", '" . $_POST['name'] . "' )");
		break;
	case "edit":
		executeSQL("update $table_name set name = '" . $_POST['name'] . "' where id = " . $_POST['boats']);
		break;
	case "delete":
		executeSQL("delete from $table_name where id = " . $_POST['boats']);
		break;
	}
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset=utf-8 />
	<title><?php _e("MANAGE"); ?></title>
	<link rel="stylesheet" href="wusmap.css" />
	<script src="wusmap.js"></script>
</head>
<body>
<h1><?php _e("MANAGE"); ?></h1>
<form method="post">
<table>
<tr>
	<td><label><?php _e("MANAGE_OPERATION_TITLE"); ?></label></td>
	<td>
		<input type="radio" name="operation" id="operation_add" value="add" onchange="if (this.checked) { setDisplay('boats_tr', 'none'); setDisplay('name_tr', 'table-row'); setDisplay('id_tr', 'table-row'); }" checked="checked" /><label for="operation_add"><?php _e("MANAGE_OPERATION_ADD"); ?></label>
		<input type="radio" name="operation" id="operation_edit" value="edit" onchange="if (this.checked) { setDisplay('boats_tr', 'table-row'); setDisplay('name_tr', 'table-row'); setDisplay('id_tr', 'none'); }" /><label for="operation_edit"><?php _e("MANAGE_OPERATION_EDIT"); ?></label>
		<input type="radio" name="operation" id="operation_delete" value="delete" onchange="if (this.checked) { setDisplay('boats_tr', 'table-row'); setDisplay('name_tr', 'none'); setDisplay('id_tr', 'none'); }" /><label for="operation_delete"><?php _e("MANAGE_OPERATION_DEL"); ?></label>
	</td>
</tr>
<tr id="boats_tr" style="display: none;">
	<td><label for="boats"><?php _e("SH_BOAT_TITLE"); ?></label></td>
	<td>
		<select name="boats" id="boats">
<?php
$boats = getAllBoats();
while ($boat = $boats->fetch_assoc()) {
	echo "<option value=\"" . $boat['id'] . "\">" . $boat['name'] . "</option>\n";
}
?>
		</select>
	</td>
</tr>
<tr id="id_tr">
	<td><label for="name"><?php _e("MANAGE_ID_TITLE"); ?></label></td>
	<td><input type="number" name="id" id="id" title="<?php _e("MANAGE_ID_TOOLTIP"); ?>" /></td>
</tr>
<tr id="name_tr">
	<td><label for="name"><?php _e("SH_NAME_TITLE"); ?></label></td>
	<td><input type="text" name="name" id="name" title="<?php _e("MANAGE_NAME_TOOLTIP"); ?>" /></td>
</tr>
</table>
<input type="submit" value="<?php _e("SH_SUBMIT"); ?>" />
</form>
</body>
</html>