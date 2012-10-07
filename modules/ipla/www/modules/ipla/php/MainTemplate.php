<?php
/*  xIpla - MainTemplate.php
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

class MainTemplate
{
	
	var $items;

	
	function MainTemplate()
	{
		
		$this->items = "";
	
	}
	
	
	function CreateTemplate($data)
	{

		$this->items = "<channel>\n";
		
		$count = 0;
	
		$this->items .= "<item>\n";
		$this->items .= "<title>Polecane</title>\n";
		$this->items .= "<description></description>\n";
		$this->items .= "<link>" . SERVER_HOST_AND_PATH . "php/index2.php?action=rec</link>\n";
		$this->items .= "<itemid>" . $count . "</itemid>\n";
		$this->items .= "</item>\n";
		$count++;
		
		foreach ($data as $d)
		{
	
			$this->items .= "<item>\n";
			$this->items .= "<title>" . $d['title'] . "</title>\n";
			$this->items .= "<description>" . $d['descr'] . "</description>\n";
			$this->items .= "<link>" . SERVER_HOST_AND_PATH . "php/index2.php?action=cat&amp;id=" . $d['id'] . "&amp;pt=" . base64_encode($d['title']) . "</link>\n";
			$this->items .= "<itemid>" . $count . "</itemid>\n";
			$this->items .= "</item>\n";
			
			$count++;
			
		}

		$this->items .= "<item>\n";
		$this->items .= "<title>Kanały</title>\n";
		$this->items .= "<description></description>\n";
		$this->items .= "<link>" . SERVER_HOST_AND_PATH . "php/index2.php?action=cha</link>\n";
		$this->items .= "<itemid>" . $count . "</itemid>\n";
		$this->items .= "</item>\n";
		$count++;

		$this->items .= "<item>\n";
		$this->items .= "<title>Szukaj</title>\n";
		$this->items .= "<description></description>\n";
		$this->items .= "<link>rss_command://search</link>\n";
		$this->items .= '<search url="' . SERVER_HOST_AND_PATH . 'php/index2.php?action=search&amp;keywords=%s" />'."\n";
		$this->items .= "<itemid>" . $count . "</itemid>\n";
		$this->items .= "</item>\n";
		$count++;
		
		$this->items .= "</channel>\n";
		
	}
	
	
	function ShowTemplate()
	{
    
		header("Content-type: text/xml");
        echo '<?xml version="1.0" encoding="UTF-8" ?>'."\n";
        echo '<rss version="2.0" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:media="http://purl.org/dc/elements/1.1/">'."\n";
		
		echo '<mediaDisplay name="photoView"'."\n";
        echo 'showHeader="yes" rowCount="1" columnCount="5" drawItemText="no" showDefaultInfo="no"'."\n";
        echo 'itemImageXPC="100" itemImageYPC="100" itemOffsetXPC="4" itemOffsetYPC="85" sliding="yes"'."\n";
        echo 'itemWidthPC="17.4" itemHeightPC="9" itemBorderColor="-1:-1:-1"'."\n";
        echo 'idleImageXPC="90" idleImageYPC="5" idleImageWidthPC="5" idleImageHeightPC="8"'."\n";
        echo 'bottomYPC="88" sideTopHeightPC="20" itemBackgroundColor="0:0:0" itemGap="0"'."\n";
        echo 'backgroundColor="-1:-1:-1" sideColorBottom="-1:-1:-1" sideColorTop="-1:-1:-1"'."\n";
        echo 'fontSize="18">'."\n";

		echo '<idleImage>' . XTREAMER_PATH . 'img/hddg.png</idleImage>';
		echo '<idleImage>' . XTREAMER_PATH . 'img/hddr.png</idleImage>';

		echo '<text redraw="no"'."\n";
        echo 'backgroundColor="-1:-1:-1" foregroundColor="255:255:255"'."\n";
        echo 'offsetXPC="5" offsetYPC="81" widthPC="19" heightPC="3" fontSize="10" lines="1">'."\n";
		echo '<script>'."\n";
		echo '"xIpla v' . VERSION . ' by ToM/UD";'."\n";
        echo '</script>'."\n";
		echo '</text>'."\n";
		
		echo '<itemDisplay>'."\n";
        echo '<image redraw="yes" offsetXPC="0" offsetYPC="0" widthPC="100" heightPC="100" >'."\n";
		echo '<script>'."\n";
        echo 'if( getFocusItemIndex() == getItemInfo(-1,"itemid") )'."\n";
        echo '"' . XTREAMER_PATH . 'img/bar1f.png";'."\n";
		echo 'else'."\n";
        echo '"' . XTREAMER_PATH . 'img/bar1u.png";'."\n";
        echo '</script>'."\n";
        echo '</image>'."\n";
		echo '<text redraw="yes" backgroundColor="-1:-1:-1" offsetXPC="10" offsetYPC="10" widthPC="80" heightPC="80" fontSize="16" lines="1">'."\n";
        echo '<script>'."\n";
		echo 'getItemInfo(-1,"title");'."\n";
        echo '</script>'."\n";
        echo 'foregroundColor="255:255:255"'."\n";
		echo '</text></itemDisplay>'."\n";
		
		echo '<backgroundDisplay>'."\n";
        echo '<image  offsetXPC="0" offsetYPC="0" widthPC="100" heightPC="100">' . XTREAMER_PATH . 'img/back_main.jpg</image>'."\n";
		echo '</backgroundDisplay>'."\n";

		echo '</mediaDisplay>'."\n";
		
		echo $this->items;
		
		echo '</rss>'."\n";
		
	}
	
}

?>