<?php
/*  xIpla - config.php
 *  Copyright (C) 2010 ToM/UD
 *
 *  This Program is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2, or (at your option)
 *  any later version.
 *
 *  This Program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with flvstreamer; see the file COPYING.  If not, write to
 *  the Free Software Foundation, 675 Mass Ave, Cambridge, MA 02139, USA.
 *  http://www.gnu.org/copyleft/gpl.html
 *
 */

define("CACHE_TIME_CATEGORIES", "48");
define("CACHE_TIME_VODS", "12");
define("CACHE_TIME_VODS_RECOMMENDATIONS", "24");

# 1 - STD, 2 - MAX, 3 - HD
define("MAX_VIDEO_QUALITY", "3");

define("VODS_SEARCH_ENABLE","21");

define("PROXY_ENABLE", "1");

##########################################################################
## Please do not change !!!                                             ##
##########################################################################
define("TVPROXY_NAME", "xiplatvproxy");
define("TVPROXY_ARG", "-b 127.0.0.1 -q -d");

define("MAX_VIDEO_FORMAT", "3");

define("PLAY_ITEM_URL_BUFFER_SIZE", ",524288");
#define("PLAY_ITEM_URL_BUFFER_SIZE", "");

define("VERSION", "1.2.1");
	
define("HOST", "http://" . $_SERVER['HTTP_HOST']);
$webpath = dirname( dirname( $_SERVER['SCRIPT_NAME'] ) ) .'/';
define("SERVER_HOST_AND_PATH", HOST . $webpath);
define("XTREAMER_PATH", dirname( dirname(__FILE__) ) .'/');
/*
header( "Content-type: text/plain" );

echo "\nSERVER: ";print_r($_SERVER);

echo HOST.PHP_EOL;
echo $webpath.PHP_EOL;
echo SERVER_HOST_AND_PATH.PHP_EOL;
echo XTREAMER_PATH.PHP_EOL;
*/
?>