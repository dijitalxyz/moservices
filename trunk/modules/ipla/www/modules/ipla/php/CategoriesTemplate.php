<?php
/*  xIpla - CategoriesTemplate.php
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

class CategoriesTemplate
{
	
	var $items;
	var $items_count;
	var $page_title;
	var $current_id;
	

	function CategoriesTemplate()
	{
		
		$this->items = "";
		$this->items_count = 0;
		$this->current_id = 0;
		$this->page_title = "";
		
	}

	
	function CreateTemplate($data, $pt, $id)
	{
		$this->items = "<channel>\n";
		$this->page_title = mb_convert_case($pt, MB_CASE_UPPER, "UTF-8");
		$this->current_id = $id;
		
		$count = 0;
		
		
		foreach ($data as $d)
		{
			
			$this->items .= "<item>\n";
			$this->items .= "<title><![CDATA[" . $d['title'] . "]]></title>\n";
			$this->items .= "<description><![CDATA[" . $d['descr'] . "]]></description>\n";
			$this->items .= "<link>" . SERVER_HOST_AND_PATH . "php/index2.php?action=cat&amp;id=" . $d['id'] . "&amp;pt=" . base64_encode($d['title']) . "</link>\n";
			$this->items .= "<itemid>" . $count . "</itemid>\n";
			$this->items .= '<media:thumbnail url="' . $d['img'] . '"/>'."\n";
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
        echo 'showHeader="no" rowCount="1" columnCount="4" drawItemText="no"'."\n";
        echo 'sideColorBottom="-1:-1:-1" sideColorTop="-1:-1:-1"'."\n";
        echo 'itemOffsetXPC="4" itemOffsetYPC="72" itemWidthPC="22" itemHeightPC="22"'."\n";
        echo 'forceFocusOnItem="yes"'."\n";
        echo 'itemCornerRounding="yes"'."\n";
        echo 'itemImageWidthPC="22" itemImageHeightPC="22"'."\n";
        echo 'backgroundColor="0:0:0" itemBorderColor="112:200:30" sliding="yes"'."\n";
        echo 'idleImageXPC="90" idleImageYPC="5" idleImageWidthPC="5" idleImageHeightPC="8"'."\n";
        echo 'bottomYPC="100" sideTopHeightPC="0">'."\n";

		echo '<idleImage>' . XTREAMER_PATH . 'img/hddg.png</idleImage>';
		echo '<idleImage>' . XTREAMER_PATH . 'img/hddr.png</idleImage>';
		
		echo '<image redraw="yes" offsetXPC="7" offsetYPC="10.4" widthPC="51.1" heightPC="51.2" >'."\n";
		echo XTREAMER_PATH . 'img/tve1.jpg'."\n";
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
        echo 'backgroundColor="-1:-1:-1" foregroundColor="255:255:255"'."\n";
        echo 'offsetXPC="7.2" offsetYPC="24" widthPC="50.7" heightPC="30" fontSize="12" lines="11">'."\n";
        echo '<script>'."\n";
        echo 'getItemInfo("description");'."\n";
        echo '</script>'."\n";
		echo '</text>'."\n";
		
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

		echo '<text redraw="no"'."\n";
        echo 'backgroundColor="-1:-1:-1" foregroundColor="255:255:255"'."\n";
        echo 'offsetXPC="10" offsetYPC="61.5" widthPC="19" heightPC="3" fontSize="10" lines="1">'."\n";
		echo '<script>'."\n";
		echo '"A-Z";'."\n";
        echo '</script>'."\n";
		echo '</text>'."\n";

		echo '<text redraw="no"'."\n";
        echo 'backgroundColor="-1:-1:-1" foregroundColor="255:255:255"'."\n";
        echo 'offsetXPC="16.5" offsetYPC="61.5" widthPC="19" heightPC="3" fontSize="10" lines="1">'."\n";
		echo '<script>'."\n";
		echo '"Nowe";'."\n";
        echo '</script>'."\n";
		echo '</text>'."\n";
		
		echo '<text redraw="no"'."\n";
        echo 'backgroundColor="-1:-1:-1" foregroundColor="255:255:255"'."\n";
        echo 'offsetXPC="23.8" offsetYPC="61.5" widthPC="19" heightPC="3" fontSize="10" lines="1">'."\n";
		echo '<script>'."\n";
		echo '"Popularne";'."\n";
        echo '</script>'."\n";
		echo '</text>'."\n";

		echo '<text redraw="no"'."\n";
        echo 'backgroundColor="-1:-1:-1" foregroundColor="255:255:255"'."\n";
        echo 'offsetXPC="31.3" offsetYPC="61.5" widthPC="19" heightPC="3" fontSize="10" lines="1">'."\n";
		echo '<script>'."\n";
		echo '"Cenione";'."\n";
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
        echo '<image  offsetXPC="0" offsetYPC="0" widthPC="100" heightPC="100">' . XTREAMER_PATH . 'img/back_cat.jpg</image>'."\n";
		echo '</backgroundDisplay>'."\n";

		echo '<onUserInput>'."\n";
        echo '<script>'."\n";
		
		echo 'ret="false";'."\n";
		echo 'userInput = currentUserInput();'."\n";
		
		echo 'if( userInput == "1" || userInput == "one" || userInput == "video_frwd"){'."\n";
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
		
        echo 'if( userInput == "3" || userInput == "three" || userInput == "video_frwd" ){'."\n";
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

        echo 'if( userInput == "0" || userInput == "zero"  || userInput == "video_stop"){'."\n";
		echo 'showIdle();'."\n";
        echo 'jumpToLink("gotoHome");'."\n";
        echo 'redrawDisplay();'."\n";
	echo 'ret="true";'."\n";
        echo '}'."\n";
		
	    echo 'ret;'."\n";
	    echo '</script>'."\n";
		echo '</onUserInput>'."\n";
		
		echo '</mediaDisplay>'."\n";

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