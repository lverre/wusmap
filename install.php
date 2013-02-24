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
 * @description Creates the database.
 */

$lang = "en";
if (isset($_REQUEST['lang'])) {
	$lang = $_REQUEST['lang'];
} else if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
	$lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
}
if (!file_exists("./i18n/$lang.php")) $lang = "en";
putenv("LANG=$lang");
setlocale(LC_ALL, "$lang");
include_once "./i18n/$lang.php";

function createConfig() {
	global $MESSAGES;
	$handle = fopen("config.php", 'w') or die("can't open file");
	fwrite($handle, "<?php
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
 * @description Configuration file.
 */

\$CONFIG = array();
\$CONFIG['db_name'] = '" . $_POST["db_name"] . "';
\$CONFIG['db_host'] = '" . $_POST["db_host"] . "';
\$CONFIG['db_user'] = '" . $_POST["db_user"] . "';
\$CONFIG['db_pwd'] = '" . $_POST["db_pwd"] . "';
\$CONFIG['db_prefix'] = '" . $_POST["db_prefix"] . "';
\$CONFIG['email_name'] = '" . $_POST["email_name"] . "';
\$CONFIG['email_host'] = '" . $_POST["email_host"] . "';
\$CONFIG['email_port'] = '" . $_POST["email_port"] . "';
\$CONFIG['email_pwd'] = '" . $_POST["email_pwd"] . "';
?>");
	fclose($handle);
	
	return $MESSAGES["INSTALL_CONFIG_FILE_SUCCESS"];
}

function executeSQL($sql) {
	global $CONFIG;
	global $MESSAGES;
	$mysqli = new mysqli($_POST["db_host"], $_POST["db_user"], $_POST["db_pwd"], $_POST["db_name"]);
	if ($mysqli->connect_errno) return sprintf($MESSAGES["SH_SQL_CONNECTION_FAILED"], $mysqli->connect_error);
	$result = $mysqli->query($sql);
	if (!$mysqli->errno) return sprintf($MESSAGES["SH_SQL_QUERY_FAILED"], $sql, $mysqli->errno, $mysqli->error);
	return $result;
}

function executeSQLOne($sql) {
	$result = executeSQL($sql);
	return count($result) > 0 ? $result[0] : null;
}

function createTables() {
	global $MESSAGES;
	$tname_assets = $_POST["db_prefix"] . "assets";
	$tname_points = $_POST["db_prefix"] . "points";
	$tassets_exists = false;
	$tpoints_exists = false;
	
	if (executeSQLOne("show tables like '$tname_assets'") != $tname_assets) {
		executeSQL("create table $tname_assets 
(
id int not null, 
name tinytext not null, 
unique key id (id)
);");
	} else {
		$tassets_exists = true;
	}
	
	if (executeSQLOne("show tables like '$tname_points'") != $tname_points) {
		executeSQL("create table $tname_points 
(
asset_id int not null, 
latitude decimal(8,5) not null,
longitude decimal(8,5) not null,
time datetime not null,
heading decimal(8,5),
speed decimal(8,5)
);");
	} else {
		$tpoints_exists = true;
	}
	
	if ($tassets_exists != $tpoints_exists) {
		return $tassets_exists 
			? $MESSAGES["INSTALL_TABLE_ASSET_POINTS"]
			: $MESSAGES["INSTALL_TABLE_POINTS_ASSET"];
	} else {
		return $tassets_exists 
			? $MESSAGES["INSTALL_TABLE_EXISTED"]
			: $MESSAGES["INSTALL_TABLE_SUCCESS"];
	}
}

?>
<!DOCTYPE html>
<html>
<head>
	<meta charset=utf-8 />
	<title><?php echo $MESSAGES["INSTALL"]; ?></title>
	<link rel="stylesheet" href="wusmap.css" />
</head>
<body>
<h1><?php echo $MESSAGES["INSTALL"]; ?></h1>
<?php
if (isset($_POST["db_name"])) {
	echo "<div>\n" . $MESSAGES["INSTALL_RESULT_TITLE"] . "\n<ul>\n";
	echo "<li>" . createConfig() . "</li>\n";
	echo "<li>" . createTables() . "</li>\n";
	echo "<li>" . $MESSAGES["INSTALL_FILE_REMOVAL"] . (unlink("install.php") ? $MESSAGES["SH_SUCCESS"] : $MESSAGES["SH_FAILURE"]) . "</li>\n";
	echo "</ul>\n";
	echo "<a href='manageassets.php'>" . $MESSAGES["INSTALL_ADD_BOAT"] . "</a>\n</div>";
} else {
?>
<form method='post'>
<table>
<tr>
	<th colspan="2"><?php echo $MESSAGES["INSTALL_DB"]; ?></th>
</tr>
<tr>
	<td><label for="name"><?php echo $MESSAGES["SH_NAME_TITLE"]; ?></label></td>
	<td><input type="text" name="db_name" id="name" title="<?php echo $MESSAGES["INSTALL_DB_NAME_TOOLTIP"]; ?>"></td>
</tr>
<tr>
	<td><label for="host"><?php echo $MESSAGES["INSTALL_DB_HOST_TITLE"]; ?></label></td>
	<td><input type="text" name="db_host" id="host" value="localhost" title="<?php echo $MESSAGES["INSTALL_DB_HOST_TOOLTIP"]; ?>"></td>
</tr>
<tr>
	<td><label for="user"><?php echo $MESSAGES["INSTALL_DB_USERNAME_TITLE"]; ?></label></td>
	<td><input type="text" name="db_user" id="user" title="<?php echo $MESSAGES["INSTALL_DB_USERNAME_TOOLTIP"]; ?>"></td>
</tr>
<tr>
	<td><label for="pwd"><?php echo $MESSAGES["INSTALL_PWD_TITLE"]; ?></label></td>
	<td><input type="password" name="db_pwd" id="pwd" title="<?php echo $MESSAGES["INSTALL_PWD_TOOLTIP"]; ?>"></td>
</tr>
<tr>
	<td><label for="prefix"><?php echo $MESSAGES["INSTALL_DB_PREFIX_TITLE"]; ?></label></td>
	<td><input type="text" name="db_prefix" id="prefix" value="wusmap_" title="<?php echo $MESSAGES["INSTALL_DB_PREFIX_TOOLTIP"]; ?>"></td>
</tr>
<tr>
	<th colspan="2"><?php echo $MESSAGES["INSTALL_EMAIL"]; ?></th>
</tr>
<tr>
	<td><label for="email_host"><?php echo $MESSAGES["INSTALL_EMAIL_SERVER_TITLE"]; ?></label></td>
	<td><input type="text" name="email_host" id="email_host" title="<?php echo $MESSAGES["INSTALL_EMAIL_SERVER_TOOLTIP"]; ?>"></td>
</tr>
<tr>
	<td><label for="email_port"><?php echo $MESSAGES["INSTALL_EMAIL_PORT_TITLE"]; ?></label></td>
	<td><input type="number" min="0" value="143" name="email_port" id="email_port" title="<?php echo $MESSAGES["INSTALL_EMAIL_PORT_TOOLTIP"]; ?>"></td>
</tr>
<tr>
	<td><label for="name"><?php echo $MESSAGES["INSTALL_EMAIL_ADDRESS_TITLE"]; ?></label></td>
	<td><input type="email" name="email_name" id="email_name" title="<?php echo $MESSAGES["INSTALL_EMAIL_ADDRESS_TOOLTIP"]; ?>"></td>
</tr>
<tr>
	<td><label for="email_pwd"><?php echo $MESSAGES["INSTALL_PWD_TITLE"]; ?></label></td>
	<td><input type="password" name="email_pwd" id="email_pwd" title="<?php echo $MESSAGES["INSTALL_PWD_TOOLTIP"]; ?>"></td>
</tr>
</table>
<input type='submit' value='<?php echo $MESSAGES["SH_SUBMIT"]; ?>' />
</form>
<?php	
}
?>
</body>
</html>