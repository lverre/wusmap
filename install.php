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
 * @description Creates the database.
 */

function createConfig() {
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
 * @version 0.1
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
	
	return "Config file created successfully.";
}

function executeSQL($sql) {
	global $CONFIG;
	$mysqli = new mysqli($_POST["db_host"], $_POST["db_user"], $_POST["db_pwd"], $_POST["db_name"]);
	if ($mysqli->connect_errno) return "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
	$result = $mysqli->query($sql);
	if (!$mysqli->errno) return "Query failed: '" . $sql . "' -> error " . $mysqli->errno . " " . $mysqli->error;
	return $result;
}

function executeSQLOne($sql) {
	$result = executeSQL($sql);
	return count($result) > 0 ? $result[0] : null;
}

function createTables() {
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
			? "The assets table existed but the points one did not... weird!"
			: "The points table existed but the assets one did not... weird!";
	} else {
		return $tassets_exists 
			? "The tables already existed."
			: "The tables were successfully created.";
	}
}

?>
<!DOCTYPE html>
<html>
<head>
	<meta charset=utf-8 />
	<title>Wusmap Install</title>
	</head>
<body>
<h1>Wusmap Install</h1>
<?php
if (isset($_POST["db_name"])) {
	echo "<div>\nResult:\n<ul>\n";
	echo "<li>" . createConfig() . "</li>\n";
	echo "<li>" . createTables() . "</li>\n";
	echo "</ul>\n";
	echo "<a href='manageassets.php'>Add an asset (boat)</a>\n</div>";
} else {
	
}
?>
<form method='post'>
Please, enter the configuration for the database:
<table style="border:0;">
<tr>
	<td><label for="name">Database Name:</label></td>
	<td><input type="text" name="db_name" id="name" title="The name of the database."></td>
</tr>
<tr>
	<td><label for="host">Database Host:</label></td>
	<td><input type="text" name="db_host" id="host" value="localhost" title="The hostname to access the database."></td>
</tr>
<tr>
	<td><label for="user">User Name:</label></td>
	<td><input type="text" name="db_user" id="user" title="The username to access the database"></td>
</tr>
<tr>
	<td><label for="pwd">Password:</label></td>
	<td><input type="password" name="db_pwd" id="pwd" title="The user's password"></td>
</tr>
<tr>
	<td><label for="prefix">Tables Prefix:</label></td>
	<td><input type="text" name="db_prefix" id="prefix" value="wusmap_" title="A prefix for the wusmap tables"></td>
</tr>
</table>
<br />
Please, enter the configuration for the e-mail address:
<table style="border:0;">
<tr>
	<td><label for="email_host">Imap Server:</label></td>
	<td><input type="text" name="email_host" id="email_host" title="The server name to access the email address."></td>
</tr>
<tr>
	<td><label for="email_port">Imap Port:</label></td>
	<td><input type="number" min="0" value="143" name="email_port" id="email_port" title="The server port to access the email address."></td>
</tr>
<tr>
	<td><label for="name">Email Address:</label></td>
	<td><input type="email" name="email_name" id="email_name" title="The name of the email address."></td>
</tr>
<tr>
	<td><label for="email_pwd">Password:</label></td>
	<td><input type="password" name="email_pwd" id="email_pwd" title="The user's password"></td>
</tr>
</table>
<br />
<input type='submit' value='Submit' />
</form>
</body>
</html>