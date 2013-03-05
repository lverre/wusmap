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
 * @description Check the emails.
 */

include_once "general.php";

function checkEmails() {
	global $CONFIG;
	global $MESSAGES;
	$table_name = $CONFIG['db_prefix'] . "points";
	$connection = imap_open('{' . $CONFIG['email_host'] . '/notls}', $CONFIG['email_name'], $CONFIG['email_pwd']);
	$unseen = imap_search($connection, 'UNSEEN');
	$output = "";
	if ($unseen && count($unseen) > 0) {
		foreach ($unseen as $index) {
			$header = imap_headerinfo($connection, $index);
			if ($header->from[0]->host == 'advanced-tracking.com' && !strncmp($header->subject, 'XML Position', 12)) {
				$output .= "<li>" . $header->date . "</li>\n";
				$result = parseEmail(trim(stripslashes(imap_body($connection, $index))));
				executeSQL("insert into " . $CONFIG['db_prefix'] . "points ( asset_id, latitude, longitude, time, heading, speed ) values ( " . $result['asset_id'] . ", " . $result['latitude'] . ", " . $result['longitude'] . ", '" . $result['datetime'] . "', " . $result['heading'] . ", " . $result['speed'] . " )");
			}
		}
	}
	if ($output == "") {
		echo __("CHECK_EMAILS_NONE");
	} else {
		echo __("CHECK_EMAILS_PARSED") . "\n<ul>\n" . $output . "</ul>";
	}
}

?>
<!DOCTYPE html>
<html>
<head>
	<meta charset=utf-8 />
	<title><?php _e("CHECK_EMAILS"); ?></title>
	<link rel="stylesheet" href="wusmap.css" />
</head>
<body>
<h1><?php _e("CHECK_EMAILS"); ?></h1>
<?php checkEmails(); ?>
</body>
</html>