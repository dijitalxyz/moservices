#!/tmp/www/cgi-bin/php
﻿<?php echo "<?xml version='1.0' encoding='UTF8' ?>";
$host = 'http://127.0.0.1';
?>
<rss version="2.0" xmlns:dc="http://purl.org/dc/elements/1.1/">

<channel>
	<title>pornvisit</title>
	<menu>main menu</menu>


<?php
function str_between($string, $start, $end){ 
	$string = " ".$string; $ini = strpos($string,$start); 
	if ($ini == 0) return ""; $ini += strlen($start); $len = strpos($string,$end,$ini) - $ini; 
	return substr($string,$ini,$len); 
}
$link = $_GET["file"];
$link = str_replace(' ','%20',$link);
$link = str_replace('[','%5B',$link);
$link = str_replace(']','%5D',$link);
$image = "/tmp/www/cgi-bin/scripts/adult/image/pornvisit.gif";
$title = "Link";
$html = file_get_contents($link);
$link = urldecode(str_between($html, "s1.addParam('flashvars','file=", "&"));
$link = $link."?start=0&amp;id=player&amp;client=FLASH%20WIN%2010,1,53,64&amp;version=4.2.95&amp;width=640";
    echo '<item>';
    echo '<title>'.$title.'</title>';
    echo '<link>'.$link.'</link>';
    echo '<media:thumbnail url="'.$image.'" />';
    echo '<enclosure type="video/flv" url="'.$link.'"/>';	
    echo '</item>';

  
?>


</channel>
</rss>
