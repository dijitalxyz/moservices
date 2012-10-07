<?php
/*  xIpla - VodsTemplate.php
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

class VodsTemplate
{
	
	var $items;
	var $items_count;
	var $page_title;
	var $current_id;
	var $current_sort;
	
	
	function VodsTemplate()
	{
		
		$this->items = "";
		$this->items_count = 0;
		$this->current_id = 0;	
		$this->current_sort = 4;
		$this->page_title = "";
		$this->sort_enable = 1;
		
	}
	
	
	function CreateTemplate($data, $pt, $id, $sort, $sort_enable)
	{
	
		$this->items = "<channel>\n";
		$this->page_title = mb_convert_case($pt, MB_CASE_UPPER, "UTF-8");
		$this->current_id = $id;
		$this->current_sort = $sort;
		$this->sort_enable = $sort_enable;
		
		$count = 0;

		if ($this->sort_enable > 1)
		{
			
			$this->items .= "<item>\n";
			$this->items .= "<title>Szukaj w tym dziale</title>\n";
			$this->items .= "<description></description>\n";
			$this->items .= "<link>rss_command://search</link>\n";
			$this->items .= '<search url="' . SERVER_HOST_AND_PATH . 'php/index2.php?action=searchsql&amp;id=' . $id . '&amp;sort=' . $sort . '&amp;pt=' .  base64_encode($this->page_title) . '&amp;keywords=%s" />'."\n";
			$this->items .= "<itemid>" . $count . "</itemid>\n";
			$this->items .= "</item>\n";
			$count++;
			
		}
		
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
	
			if ($d['adult'] > 0) $img_adult = "adulty.png";
			else $img_adult = "adultn.png";
	
			$this->items .= "<item>\n";
			$this->items .= "<title><![CDATA[" . $d['title'] . "]]></title>\n";
			$this->items .= "<description><![CDATA[" . $d['descr'] . "]]></description>\n";
			if (PROXY_ENABLE == 0) $this->items .= '<streamurl>' . $d['url'] . '</streamurl>'."\n";
			else $this->items .= '<streamurl>http://127.0.0.1:8666/vods.flv?li=' . $d['url'] . '</streamurl>'."\n";
			$this->items .= "<itemid>" . $count . "</itemid>\n";
			$this->items .= '<media:thumbnail url="' . $d['img'] . '"/>'."\n";
			$this->items .= "<plevel>" . $img_plevel . "</plevel>\n";
			$this->items .= "<vote>" . $img_vote . "</vote>\n";
			$this->items .= "<adult>" . $img_adult . "</adult>\n";
			
			if ($d['dur'] != "") $this->items .= "<date>" . "Data dodania: " . gmdate("d-m-Y", $d['timestamp'] + 3600 ) . ", Czas trwania: " . gmdate("H:i:s", $d['dur']) . "</date>\n";
			else $this->items .= "<date>" . "Data dodania: " . gmdate("d-m-Y", $d['timestamp'] + 3600 ) . "</date>\n";

			$this->items .= "</item>\n";
			
			$count++;
			
		}
		
		if ($count == 0)
		{
		
			$this->items .= "<item>\n";
			$this->items .= "<title><![CDATA[Brak filmów w tej kategorii]]></title>\n";
			$this->items .= "<itemid>" . $count . "</itemid>\n";
			$this->items .= "</item>\n";
			$count++;
			
		}
		
		
		$this->items .= "</channel>\n";
		$this->items_count = $count - 1;
		
	}
	
	
	function ShowTemplate()
	{
    
		header("Content-type: text/xml");
        echo '<?xml version="1.0" encoding="UTF-8" ?>'."\n";
        echo '<rss version="2.0" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:media="http://purl.org/dc/elements/1.1/">'."\n";
		
		echo '<mediaDisplay name="photoView"'."\n";
        echo 'showHeader="no" rowCount="3" columnCount="5" drawItemText="no"'."\n";
        echo 'sideColorBottom="-1:-1:-1" sideColorTop="-1:-1:-1"'."\n";
        echo 'itemOffsetXPC="4" itemOffsetYPC="72" itemWidthPC="17" itemHeightPC="5.5"'."\n";
        echo 'forceFocusOnItem="yes"'."\n";
        echo 'itemCornerRounding="yes"'."\n";
        echo 'backgroundColor="0:0:0" itemBorderColor="112:200:30" sliding="yes"'."\n";
        echo 'idleImageXPC="90" idleImageYPC="5" idleImageWidthPC="5" idleImageHeightPC="8"'."\n";
        echo 'bottomYPC="100" sideTopHeightPC="0">'."\n";

		echo '<idleImage>' . XTREAMER_PATH . 'img/hddg.png</idleImage>';
		echo '<idleImage>' . XTREAMER_PATH . 'img/hddr.png</idleImage>';
		
		echo '<image redraw="yes" offsetXPC="7" offsetYPC="10.4" widthPC="51.1" heightPC="51.2" >'."\n";
		echo XTREAMER_PATH . 'img/tve1.jpg'."\n";
		echo '</image>'."\n";
		
		echo '<image redraw="yes" offsetXPC="7" offsetYPC="10.4" widthPC="51.1" heightPC="51.2">'."\n";
        echo '<script>'."\n";
        echo 'getItemInfo(-1, "thumbnail");'."\n";
        echo '</script>'."\n";
		echo '</image>'."\n";
	
		echo '<image redraw="yes" offsetXPC="7" offsetYPC="10.4" widthPC="51.1" heightPC="51.2" >'."\n";
		echo XTREAMER_PATH . 'img/tve1t.png'."\n";
		echo '</image>'."\n";

		echo '<image redraw="yes" offsetXPC="47" offsetYPC="50" widthPC="10" heightPC="3.5" >'."\n";
		echo '<script>'."\n";
        echo '"' . XTREAMER_PATH . 'img/" + getItemInfo(-1, "vote");'."\n";
        echo '</script>'."\n";
		echo '</image>'."\n";

		echo '<image redraw="yes" offsetXPC="47" offsetYPC="54" widthPC="10" heightPC="3.2" >'."\n";
		echo '<script>'."\n";
        echo '"' . XTREAMER_PATH . 'img/" + getItemInfo(-1, "plevel");'."\n";
        echo '</script>'."\n";
		echo '</image>'."\n";
		
		echo '<text redraw="yes"'."\n";
        echo 'backgroundColor="-1:-1:-1" foregroundColor="245:254:1"'."\n";
        echo 'offsetXPC="7.2" offsetYPC="12.5" widthPC="44" heightPC="4.5" fontSize="16" lines="1">'."\n";
		echo '<script>'."\n";
        echo '"' . $this->page_title . '";'."\n";
        echo '</script>'."\n";
		echo '</text>'."\n";

        echo '<text redraw="yes"'."\n";
        echo 'backgroundColor="-1:-1:-1" foregroundColor="255:255:255"'."\n";
        echo 'offsetXPC="7.2" offsetYPC="18" widthPC="50.7" heightPC="5.5" fontSize="16" lines="1">'."\n";
        echo '<script>'."\n";
        echo 'getItemInfo("title");'."\n";
        echo '</script>'."\n";
		echo '</text>'."\n";

	    echo '<text redraw="yes"'."\n";
        echo 'backgroundColor="-1:-1:-1" foregroundColor="245:254:1"'."\n";
        echo 'offsetXPC="8" offsetYPC="51.5" widthPC="50.7" heightPC="5.5" fontSize="12" lines="1">'."\n";
        echo '<script>'."\n";
        echo 'getItemInfo("date");'."\n";
        echo '</script>'."\n";
		echo '</text>'."\n";	
		
        echo '<text redraw="yes"'."\n";
        echo 'backgroundColor="-1:-1:-1" foregroundColor="255:255:255"'."\n";
        echo 'offsetXPC="7.2" offsetYPC="24" widthPC="50.7" heightPC="26" fontSize="12" lines="9">'."\n";
        echo '<script>'."\n";
        echo 'getItemInfo("description");'."\n";
        echo '</script>'."\n";
		echo '</text>'."\n";

		echo '<image redraw="yes" offsetXPC="53.3" offsetYPC="17.5" widthPC="2.2" heightPC="3.5" >'."\n";
		echo '<script>'."\n";
        echo '"' . XTREAMER_PATH . 'img/" + getItemInfo(-1, "adult");'."\n";
        echo '</script>'."\n";
		echo '</image>'."\n";
	
		echo '<text redraw="yes"'."\n";
        echo 'backgroundColor="-1:-1:-1" foregroundColor="255:255:255"'."\n";
        echo 'offsetXPC="48.8" offsetYPC="56.2" widthPC="9" heightPC="5" fontSize="14" lines="1">'."\n";
        echo '<script>'."\n";
		echo 'getFocusItemIndex() + " / ' . $this->items_count . '";'."\n";
        echo '</script>'."\n";
		echo '</text>'."\n";

		echo '<text redraw="yes"'."\n";
        echo 'backgroundColor="-1:-1:-1" foregroundColor="255:255:255"'."\n";
        echo 'offsetXPC="25" offsetYPC="58.2" widthPC="19" heightPC="3" fontSize="10" lines="1">'."\n";
		echo '<script>'."\n";
		echo '"xIpla v' . VERSION . ' by ToM/UD";'."\n";
        echo '</script>'."\n";
		echo '</text>'."\n";
		
		if ($this->sort_enable > 0)
		{

			echo '<text redraw="no"'."\n";
			echo 'backgroundColor="-1:-1:-1"'."\n";
			
			if ($this->current_sort == 4) echo 'foregroundColor="172:218:66"'."\n";
			else echo 'foregroundColor="255:255:255"'."\n";

			echo 'offsetXPC="10" offsetYPC="61.5" widthPC="19" heightPC="3" fontSize="10" lines="1">'."\n";
			echo '<script>'."\n";
			echo '"A-Z";'."\n";
			echo '</script>'."\n";
			echo '</text>'."\n";

			echo '<text redraw="no"'."\n";
			
			if ($this->current_sort == 0) echo 'foregroundColor="172:218:66"'."\n";
			else echo 'foregroundColor="255:255:255"'."\n";

			echo 'offsetXPC="16.5" offsetYPC="61.5" widthPC="19" heightPC="3" fontSize="10" lines="1">'."\n";
			echo '<script>'."\n";
			echo '"Nowe";'."\n";
			echo '</script>'."\n";
			echo '</text>'."\n";

			echo '<text redraw="no"'."\n";

			if ($this->current_sort == 2) echo 'foregroundColor="172:218:66"'."\n";
			else echo 'foregroundColor="255:255:255"'."\n";

			echo 'offsetXPC="23.8" offsetYPC="61.5" widthPC="19" heightPC="3" fontSize="10" lines="1">'."\n";
			echo '<script>'."\n";
			echo '"Popularne";'."\n";
			echo '</script>'."\n";
			echo '</text>'."\n";

			echo '<text redraw="no"'."\n";

			if ($this->current_sort == 1) echo 'foregroundColor="172:218:66"'."\n";
			else echo 'foregroundColor="255:255:255"'."\n";

			echo 'offsetXPC="31.3" offsetYPC="61.5" widthPC="19" heightPC="3" fontSize="10" lines="1">'."\n";
			echo '<script>'."\n";
			echo '"Cenione";'."\n";
			echo '</script>'."\n";
			echo '</text>'."\n";
			
		}
		
		echo '<itemDisplay>'."\n";
        echo '<text redraw="yes" fontSize="11" lines="2"'."\n";
        echo 'offsetXPC="0" offsetYPC="0" widthPC="100" heightPC="100"'."\n";
        echo 'backgroundColor="0:0:0" foregroundColor="255:255:255">'."\n";
        echo '<script>'."\n";
        echo 'getItemInfo(-1, "title");'."\n";
        echo '</script>'."\n";
        echo '</text>'."\n";
		echo '</itemDisplay>'."\n";

		echo '<backgroundDisplay>'."\n";
		
		if ($this->sort_enable > 0) echo '<image  offsetXPC="0" offsetYPC="0" widthPC="100" heightPC="100">' . XTREAMER_PATH . 'img/back_cat.jpg</image>'."\n";
		else echo '<image  offsetXPC="0" offsetYPC="0" widthPC="100" heightPC="100">' . XTREAMER_PATH . 'img/back_cat_ns.jpg</image>'."\n";

		echo '</backgroundDisplay>'."\n";

		echo '<onUserInput>'."\n";
        echo '<script>'."\n";
		
		echo 'ret="false";'."\n";
		echo 'userInput = currentUserInput();'."\n";
		
		if ($this->sort_enable > 0)
		{

			echo 'if( userInput == "1" || userInput == "one" || userInput == "video_frwd" ){'."\n";
			echo 'showIdle();'."\n";
			echo 'jumpToLink("sNowe");'."\n";
			echo 'redrawDisplay();'."\n";
			echo 'ret="true";'."\n";
			echo '}'."\n";

			echo 'if( userInput == "2" || userInput == "two" || userInput == "video_play" ){'."\n";
			echo 'showIdle();'."\n";
			echo 'jumpToLink("sPopularne");'."\n";
			echo 'redrawDisplay();'."\n";
			echo 'ret="true";'."\n";
			echo '}'."\n";

			echo 'if( userInput == "3" || userInput == "three" || userInput == "video_ffwd" ){'."\n";
			echo 'showIdle();'."\n";
			echo 'jumpToLink("sCenione");'."\n";
			echo 'redrawDisplay();'."\n";
			echo 'ret="true";'."\n";
			echo '}'."\n";
		
			echo 'if( userInput == "video_search" || userInput == "five" || userInput == "video_repeat" ){'."\n";
			echo 'showIdle();'."\n";
			echo 'jumpToLink("sAZ");'."\n";
			echo 'redrawDisplay();'."\n";
			echo 'ret="true";'."\n";
			echo '}'."\n";
		
		}
	
		echo 'if( userInput == "ENTR" || userInput == "enter" ){'."\n";
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
		echo 'ret="true";'."\n";
		echo '}'."\n";
		
		echo 'if( userInput == "0" || userInput == "zero" || userInput == "video_stop" ){'."\n";
		echo 'showIdle();'."\n";
		echo 'jumpToLink("gotoHome");'."\n";
		echo 'redrawDisplay();'."\n";
		echo 'ret="true";'."\n";
		echo '}'."\n";
	
	    echo 'ret;'."\n";
	    echo '</script>'."\n";
		echo '</onUserInput>'."\n";		
		
		echo '</mediaDisplay>'."\n";
		
		if ($this->sort_enable > 0)
		{

			echo '<sNowe>'."\n";
			echo '<link>'."\n";
			echo SERVER_HOST_AND_PATH . 'php/index2.php?action=mov&amp;id=' . $this->current_id . '&amp;pt=' . base64_encode($this->page_title) . '&amp;sort=0'. "\n";
			echo '</link>'."\n";
			echo '</sNowe>'."\n";
		
			echo '<sPopularne>'."\n";
			echo '<link>'."\n";
			echo SERVER_HOST_AND_PATH . 'php/index2.php?action=mov&amp;id=' . $this->current_id . '&amp;pt=' . base64_encode($this->page_title) . '&amp;sort=2'. "\n";
			echo '</link>'."\n";
			echo '</sPopularne>'."\n";

			echo '<sCenione>'."\n";
			echo '<link>'."\n";
			echo SERVER_HOST_AND_PATH . 'php/index2.php?action=mov&amp;id=' . $this->current_id . '&amp;pt=' . base64_encode($this->page_title) . '&amp;sort=1'. "\n";
			echo '</link>'."\n";
			echo '</sCenione>'."\n";
		
			echo '<sAZ>'."\n";
			echo '<link>'."\n";
			echo SERVER_HOST_AND_PATH . 'php/index2.php?action=mov&amp;id=' . $this->current_id . '&amp;pt=' . base64_encode($this->page_title) . '&amp;sort=4'. "\n";
			echo '</link>'."\n";
			echo '</sAZ>'."\n";
			
		}
		
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