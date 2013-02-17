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
 * @description Check the emails.
 */

include_once "general.php";

function checkEmails() {
	global $CONFIG;
	$table_name = $CONFIG['db_prefix'] . "points";
	$connection = imap_open('{' . $CONFIG['email_host'] . '/notls}', $CONFIG['email_name'], $CONFIG['email_pwd']);
	$count = imap_num_msg($connection);
	for($i = 1; $i <= $count; $i++) {
		$header = imap_headerinfo($connection, $i);
		if ($header->Unseen == 'U' && $header->from[0]->host == 'advanced-tracking.com' && !strncmp($header->subject, 'XML Position', 12)) {
			echo "One new email at " . $header->date . "<br />";
			$result = parseEmail(stripslashes(imap_body($connection, $i)));
			executeSQL("insert into " . $CONFIG['db_prefix'] . "points ( asset_id, latitude, longitude, time, heading, speed ) values ( " . $result['asset_id'] . ", " . $result['latitude'] . ", " . $result['longitude'] . ", '" . $result['datetime'] . "', " . $result['heading'] . ", " . $result['speed'] . " )");
		}
	}
}

?>
<!DOCTYPE html>
<html>
<head>
	<meta charset=utf-8 />
	<title>Wusmap Check Emails</title>
	<link rel="stylesheet" href="wusmap.css" />
</head>
<body>
<h1>Wusmap Check Emails</h1>
<?php checkEmails(); ?>
</body>
</html>