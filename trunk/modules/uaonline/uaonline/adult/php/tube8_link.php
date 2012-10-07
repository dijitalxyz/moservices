#!/tmp/www/cgi-bin/php
﻿<?php echo "<?xml version='1.0' ?>"; ?>
<rss version="2.0" xmlns:dc="http://purl.org/dc/elements/1.1/">

<channel>
	<title>tube8.com</title>
	<menu>main menu</menu>

<?php
$link = $_GET["file"];
    $html = file_get_contents($link);
    $t1 = explode(' videourl="', $html);
    $t2 = explode('"', $t1[1]);
    $link = $t2[0];
    echo '<item>';
    echo '<title>Link</title>';
    echo '<link>'.$link.'</link>';
    echo '<enclosure type="video/flv" url="'.$link.'"/>';	
    echo '</item>';

?>
</channel>
</rss>