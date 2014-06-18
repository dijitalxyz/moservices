<?php

/*
	iptvlist created by Roman Lut aka hax.
*/

//==================================================================
//==================================================================
function gtcallback($buffer)
{
  return "";
}

//==================================================================
//==================================================================

header("Content-Type:text/plain");
mb_internal_encoding("UTF-8"); 

ob_start("gtcallback");
require_once 'iptvlist_timezone.php';
ob_end_flush();

$ctime_arr = getdate( time() );

echo $ctime_arr[ "hours" ]."\n".$ctime_arr[ "minutes" ]."\n".$ctime_arr[ "seconds" ];

?>
