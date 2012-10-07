<?php
/*  xIpla - EpgTemplate.php
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
 
include_once "config.php";

class EpgTemplate
{
	
	var $items;
	var $items_count;
	var $page_title;
	var $current_id;
	var $current_sort;

	
	function EpgTemplate()
	{
		
		$this->items = "";
		$this->page_title = "";
		
	}
	
	function CreateTemplate($data, $pt)
	{
		
		$this->items = "<channel>\n";
		$this->page_title = $pt;
		
		$count = 0;
		
		foreach ($data as $d)
		{
			
			if ($d['plevel'] > 4.8) $img_plevel = "plevel5.png";
			elseif ($d['plevel'] > 4.3) $img_plevel = "plevel45.png";
			elseif ($d['plevel'] > 3.8) $img_plevel = "plevel4.png";
			elseif ($d['plevel'] > 3.3) $img_plevel = "plevel35.png";
			elseif ($d['plevel'] > 2.8) $img_plevel = "plevel3.png";
			elseif ($d['plevel'] > 2.3) $img_plevel = "plevel25.png";
			elseif ($d['plevel'] > 1.8) $img_plevel = "plevel2.png";
			elseif ($d['plevel'] > 1.3) $img_plevel = "plevel15.png";
			elseif ($d['plevel'] > 0.8) $img_plevel = "plevel1.png";
			elseif ($d['plevel'] > 0.3) $img_plevel = "plevel05.png";
			else  $img_plevel = "plevel0.png";
		
			if ($d['vote'] > 4.8) $img_vote = "vote5.png";
			elseif ($d['vote'] > 4.3) $img_vote = "vote45.png";
			elseif ($d['vote'] > 3.8) $img_vote = "vote4.png";
			elseif ($d['vote'] > 3.3) $img_vote = "vote35.png";
			elseif ($d['vote'] > 2.8) $img_vote = "vote3.png";
			elseif ($d['vote'] > 2.3) $img_vote = "vote25.png";
			elseif ($d['vote'] > 1.8) $img_vote = "vote2.png";
			elseif ($d['vote'] > 1.3) $img_vote = "vote15.png";
			elseif ($d['vote'] > 0.8) $img_vote = "vote1.png";
			elseif ($d['vote'] > 0.3) $img_vote = "vote05.png";
			else  $img_vote = "vote0.png";
			
			$this->items .= "<item>\n";
			$this->items .= "<title><![CDATA[" . $d['title'] . "]]></title>\n";
			$this->items .= "<description><![CDATA[" . $d['descr'] . "]]></description>\n";
			if (PROXY_ENABLE == 0) $this->items .= '<streamurl>' . $d['url'] . '</streamurl>'."\n";
			else $this->items .= '<streamurl>http://127.0.0.1:8666/vods.flv?li=' . $d['url'] . '</streamurl>'."\n";
			$this->items .= "<itemid>" . $count . "</itemid>\n";
			$this->items .= '<media:thumbnail url="' . $d['img'] . '"/>'."\n";
			$this->items .= "<vote>" . $img_vote . "</vote>\n";
			$this->items .= "<plevel>" . $img_plevel . "</plevel>\n";
			$this->items .= "<date>" . "Data dodania: " . gmdate("d-m-Y", $d['timestamp'] + 3600 ) . ", Czas trwania: " . gmdate("H:i:s", $d['dur']) . "</date>\n";
			$this->items .= "<epgtime>" . $d['epgtimestart'] . " - " . $d['epgtimeend'] . "</epgtime>\n";
			$this->items .= "</item>\n";
			
			$count++;
			
		}
		
		$this->items .= "</channel>\n";
		
	}

	
	function ShowTemplate()
	{

		header("Content-type: text/xml");
        echo '<?xml version="1.0" encoding="UTF-8" ?>'."\n";
        echo '<rss version="2.0" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:media="http://purl.org/dc/elements/1.1/">'."\n";

		echo '<mediaDisplay name="photoView"'."\n";
        echo 'showHeader="no" rowCount="1" columnCount="4" drawItemText="no"'."\n";
        echo 'sideColorBottom="-1:-1:-1" sideColorTop="-1:-1:-1"'."\n";
        echo 'itemOffsetXPC="4" itemOffsetYPC="72" itemWidthPC="22" itemHeightPC="22"'."\n";
        echo 'forceFocusOnItem="yes"'."\n";
        echo 'itemCornerRounding="yes"'."\n";
        echo 'itemImageWidthPC="22" itemImageHeightPC="22"'."\n";
        echo 'backgroundColor="0:0:0" itemBorderColor="112:200:30" sliding="yes"'."\n";
        echo 'idleImageXPC="90" idleImageYPC="5" idleImageWidthPC="5" idleImageHeightPC="8"'."\n";
        echo 'bottomYPC="100" sideTopHeightPC="0">'."\n";

		echo '<idleImage>' . XTREAMER_PATH . 'img/hddg.png</idleImage>'."\n";
		echo '<idleImage>' . XTREAMER_PATH . 'img/hddr.png</idleImage>'."\n";
	
		echo '<image redraw="yes" offsetXPC="0" offsetYPC="0" widthPC="70.4" heightPC="63.8" >'."\n";
		echo XTREAMER_PATH . 'img/cha1.jpg'."\n";
		echo '</image>'."\n";		
		
		echo '<image redraw="yes" offsetXPC="57" offsetYPC="51.5" widthPC="12" heightPC="4.5" >'."\n";
		echo '<script>'."\n";
        echo '"' . XTREAMER_PATH . 'img/" + getItemInfo(-1, "vote");'."\n";
        echo '</script>'."\n";
		echo '</image>'."\n";

		echo '<image redraw="yes" offsetXPC="57" offsetYPC="56.5" widthPC="12" heightPC="4.2" >'."\n";
		echo '<script>'."\n";
        echo '"' . XTREAMER_PATH . 'img/" + getItemInfo(-1, "plevel");'."\n";
        echo '</script>'."\n";
		echo '</image>'."\n";
	
        echo '<text redraw="yes"'."\n";
        echo 'backgroundColor="-1:-1:-1" foregroundColor="93:248:21"'."\n";
        echo 'offsetXPC="7" offsetYPC="12" widthPC="62" heightPC="8" fontSize="24" lines="1">'."\n";
        echo '<script>'."\n";
        echo '"' . $this->page_title . ' (" + getItemInfo("epgtime") + ")";'."\n";
        echo '</script>'."\n";
		echo '</text>'."\n";
		
		echo '<text redraw="yes"'."\n";
		echo 'backgroundColor="-1:-1:-1" foregroundColor="255:255:255"'."\n";
		echo 'offsetXPC="7" offsetYPC="20" widthPC="62" heightPC="5" fontSize="16" lines="1">'."\n";
		echo '<script>'."\n";
		echo 'getItemInfo("title");'."\n";
		echo '</script>'."\n";
		echo '</text>'."\n";

        echo '<text redraw="yes"'."\n";
        echo 'backgroundColor="-1:-1:-1" foregroundColor="255:255:255"'."\n";
        echo 'offsetXPC="7" offsetYPC="25" widthPC="62" heightPC="27" fontSize="12" lines="9">'."\n";
        echo '<script>'."\n";
        echo 'getItemInfo("description");'."\n";
        echo '</script>'."\n";
		echo '</text>'."\n";

	    echo '<text redraw="yes"'."\n";
        echo 'backgroundColor="-1:-1:-1" foregroundColor="245:254:1"'."\n";
        echo 'offsetXPC="17" offsetYPC="57" widthPC="36" heightPC="5.5" fontSize="12" lines="1">'."\n";
        echo '<script>'."\n";
        echo 'getItemInfo("date");'."\n";
        echo '</script>'."\n";
		echo '</text>'."\n";	

		echo '<text redraw="no"'."\n";
        echo 'backgroundColor="-1:-1:-1" foregroundColor="255:255:255"'."\n";
        echo 'offsetXPC="13" offsetYPC="68" widthPC="19" heightPC="3" fontSize="10" lines="1">'."\n";
		echo '<script>'."\n";
		echo '"xIpla v' . VERSION . ' by ToM/UD";'."\n";
        echo '</script>'."\n";
		echo '</text>'."\n";
	
		echo '<itemDisplay>'."\n";
		echo '<image redraw="no" offsetXPC="1" offsetYPC="1" widthPC="100" heightPC="80">'."\n";
        echo '<script>'."\n";
        echo 'getItemInfo(-1, "thumbnail");'."\n";
        echo '</script>'."\n";
		echo '</image>'."\n";
        echo '<text redraw="yes" fontSize="11" alignt="justify" lines="2"'."\n";
        echo 'offsetXPC="0" offsetYPC="80" widthPC="100" heightPC="20"'."\n";
        echo 'backgroundColor="-1:-1:-1" foregroundColor="255:255:255">'."\n";
        echo '<script>'."\n";
        echo 'getItemInfo(-1, "title");'."\n";
        echo '</script>'."\n";
        echo '</text>'."\n";
		echo '</itemDisplay>'."\n";

		echo '<backgroundDisplay>'."\n";
        echo '<image  offsetXPC="0" offsetYPC="0" widthPC="100" heightPC="100">' . XTREAMER_PATH . 'img/back_cha.jpg</image>'."\n";
		echo '</backgroundDisplay>'."\n";

		echo '<onUserInput>'."\n";
        echo '<script>'."\n";
		
		echo 'ret = "false";'."\n";
		echo 'userInput = currentUserInput();'."\n";

		echo 'if( userInput == "ENTR" || userInput == "enter" || userInput == "video_play" ){'."\n";
		echo 'ret = "true";'."\n";
		echo 'streamurl = getItemInfo(-1, "streamurl");'."\n";
		echo 'if (streamurl != null){'."\n";

	if( is_dir( '/sbin/www' ))
	{
		echo 'SwitchViewer(0);'."\n";
		echo 'SwitchViewer(1);'."\n";
		echo 'playItemURL(streamurl,10' . PLAY_ITEM_URL_BUFFER_SIZE . ');'."\n";
	}
	else
	{
		echo 'playItemURL(streamurl,0);'."\n";
	}
		echo '}else{'."\n";
		echo 'redrawDisplay();'."\n";
		echo '}'."\n";
		echo '}'."\n";		

		echo $this->epg_script;
		
        echo 'if( userInput == "0" || userInput == "zero" ||  userInput == "video_stop"){'."\n";
		echo 'showIdle();'."\n";
        echo 'jumpToLink("gotoHome");'."\n";
        echo 'redrawDisplay();'."\n";
	echo 'ret = "true";'."\n";
        echo '}'."\n";
	
	    echo 'ret;'."\n";
	    echo '</script>'."\n";

		echo '</onUserInput>'."\n";		
	
		echo '</mediaDisplay>'."\n";
		
		
		echo '<gotoHome>'."\n";
		echo '<link>'."\n";
		echo SERVER_HOST_AND_PATH . 'php/index2.php'. "\n";
		echo '</link>'."\n";
		echo '</gotoHome>'."\n";
		
		echo $this->items;
		
		echo '</rss>'."\n";
		
	}
	
}

?>