<?php
require 'rss_iptv.php';
require 'epglist.php';
require 'parse_epg.php';

//==================================================================
//==================================================================
function getMosUrl()
{
  return "";
}

//echo rss_iptv_content();

//list($h, $m) = createEPGList("Favorites.m3u");
//echo $h."\n".$m;
parseEPG();
?>