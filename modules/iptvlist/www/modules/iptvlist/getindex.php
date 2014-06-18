<?php
function iptvlist_body()
{
	echo file_get_contents( "/usr/local/etc/mos/www/modules/iptvlist/index.html");
}
?>
