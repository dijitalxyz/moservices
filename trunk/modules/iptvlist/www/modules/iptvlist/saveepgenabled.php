<?php

define("epgenabledconf","/usr/local/etc/mos/www/modules/iptvlist/epg_enabled.conf");

file_put_contents( epgenabledconf, $_GET["val"] );
?>
