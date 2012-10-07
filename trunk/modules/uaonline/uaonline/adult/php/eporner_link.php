#!/tmp/www/cgi-bin/php
﻿<?php echo "<?xml version='1.0' ?>"; ?>
<rss version="2.0" xmlns:dc="http://purl.org/dc/elements/1.1/">

<channel>
	<title>eporner</title>
	<menu>main menu</menu>

<?php
function str_between($string, $start, $end){ 
	$string = " ".$string; $ini = strpos($string,$start); 
	if ($ini == 0) return ""; $ini += strlen($start); $len = strpos($string,$end,$ini) - $ini; 
	return substr($string,$ini,$len); 
}
$link = $_GET["file"];

    $html = file_get_contents($link);
    $id = str_between($html,"eporner.com/player/","'");
    $link = "http://www.eporner.com/config/".$id;
    $html = file_get_contents($link);
    $link = str_between($html,"<file>","</file>");
    echo '<item>';
    echo '<title>Link</title>';
    echo '<link>'.$link.'</link>';
    echo '<enclosure type="video/mp4" url="'.$link.'"/>';	
    echo '</item>';

?>
</channel>
</rss>