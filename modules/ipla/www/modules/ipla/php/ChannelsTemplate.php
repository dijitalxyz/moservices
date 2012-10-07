<?php
/*  xIpla - ChannelsTemplate.php
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

class ChannelsTemplate
{
	
	var $items;
	var $epg_links;
	var $epg_script;
	var $epg_array;
	var $maxposition;
	var $channels_count;
	
	
	function ChannelsTemplate()
	{
		
		$this->items = "";
		$this->epg_links = "";
		$this->epg_script = "";
		$this->epg_array = array ();
		$this->maxposition = 0;
		$this->channels_count = 0;

	}
	
	
	function CreateTemplate($data, $iplainterface)
	{
	
		$this->items = "<channel>\n";
		
		$this->epg_script  = 'if( userInput == "video_search" ){'."\n";
		$this->epg_script .= 'itemindex = getFocusItemIndex();'."\n";		
		$this->epg_script .= 'showIdle();'."\n";
		
		$count = 0;
		
		foreach ($data as $d)
		{
			
			
			$this->items .= "<item>\n";
			$this->items .= "<description><![CDATA[" . $d['descr'] . "]]></description>\n";
			$this->items .= '<streamurl>http://127.0.0.1:8666/channel.flv?id=' . $d['id'] . '</streamurl>'."\n";
			$this->items .= "<itemid>" . $count . "</itemid>\n";
			$this->items .= '<media:thumbnail url="' . $d['img'] . '"/>'."\n";
			$this->items .= "</item>\n";
			
			$this->epg_array[$count] = $iplainterface->GetEpgArray($d['id']);
			if (count($this->epg_array[$count]) > $this->maxposition  ) $this->maxposition = count($this->epg_array[$count]);

			
			$this->epg_script .= 'if (itemindex == ' . $count . ' ) jumpToLink("gotoEPG' . ($count+1) . '");'."\n";
			$this->epg_links  .= '<gotoEPG' . ($count+1) . '>'."\n";
			$this->epg_links  .= "<link>" . SERVER_HOST_AND_PATH . "php/index2.php?action=epg&amp;id=" . $d['id'] . "&amp;title=" . base64_encode($d['descr']) . "</link>\n";
			$this->epg_links  .= '</gotoEPG' . ($count+1) . '>'."\n";
			
			$count++;
			
		}
		
		$this->channels_count = $count;


        $this->epg_script .='redrawDisplay();'."\n";
        $this->epg_script .='}'."\n";

		
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
		
        echo '<text redraw="yes"'."\n";
        echo 'backgroundColor="-1:-1:-1" foregroundColor="93:248:21"'."\n";
        echo 'offsetXPC="7" offsetYPC="12" widthPC="62" heightPC="8" fontSize="24" lines="1">'."\n";
        echo '<script>'."\n";
        echo 'getItemInfo("description");'."\n";
        echo '</script>'."\n";
		echo '</text>'."\n";

		if ($this->maxposition > 0)
		{

			if ($this->maxposition > 8) $this->maxposition = 8;
			
			for ($i=0;$i < $this->maxposition;$i++)
			{
			 
				$dline  = '<text redraw="yes"'."\n";
				$dline .= 'backgroundColor="-1:-1:-1" foregroundColor="255:255:255"'."\n";
				$dline .= 'offsetXPC="7" offsetYPC="' . (20 + ( $i * 5.5))  . '" widthPC="62" heightPC="5" fontSize="16" lines="1">'."\n";
				$dline .= '<script>'."\n";
				$dline .= 'itemindex = getFocusItemIndex();'."\n";
			
				for ($j=0;$j < $this->channels_count;$j++)
				{	
			
					$len = strlen($this->epg_array[$j][$i]);
					if ($len > 0) $dline .= 'if (itemindex == ' . $j . ' ) "' . str_replace('"', '\"', $this->epg_array[$j][$i]) . '";'."\n";
					else $dline .= 'if (itemindex == ' . $j . ' ) " ";'."\n";
				
				}

				$dline .= '</script>'."\n";
				$dline .= '</text>'."\n";

				echo $dline;
			
			}
			
		}

		echo '<image redraw="yes" offsetXPC="62" offsetYPC="58.5" widthPC="6" heightPC="2.5" >'."\n";
        echo XTREAMER_PATH . 'img/button_b_epg.png'."\n";
		echo '</image>'."\n";
		
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
        echo 'getItemInfo(-1, "description");'."\n";
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
		echo 'ret = "true";'."\n";
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

		echo $this->epg_links;
		
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