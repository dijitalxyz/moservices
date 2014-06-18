<?php

function test_actions()
{

}

function test_body()
{
global $nav_options;

	echo "<div id=\"container\">\n";
//	phpInfo();

echo "<pre>\n";

// print_r( $nav_options );

$db = new SQLite3('/usr/local/etc/dvdplayer/Setup');
$results = $db->query('SELECT key, value FROM SetupKeyValue where key="SETUP_TIME_ZONE"');


if ($row = $results->fetchArray())
{
	$numb = $row[1];

	echo "number=$numb\n";

	$offset = ( $numb - 25 ) * 1800;

	echo "offset=$offset\n";

	$tz = timezone_name_from_abbr( '', $offset, 1 );

	echo "timezone=$tz\n";
}
else echo "error query\n";


echo "</pre>\n";

	echo "</div>\n";

}
?>