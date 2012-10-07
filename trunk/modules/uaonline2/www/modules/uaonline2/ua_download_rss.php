<?php
/*	------------------------------
	Ukraine online services 	
	Download manager RSS part module v1.0
	------------------------------
	Created by Sashunya 2011	
	wall9e@gmail.com			
	Some code was used from 
	Farvoice & others 
	------------------------------ */

include( 'ua_rss_view.php' );
class ua_rss_download extends ua_rss_download_const
{
	
	public function mediaDisplay_content()
	{
	global $ua_images_path;
	?>
	<image offsetXPC="<?= static::item_offsetXPC ?>" offsetYPC="<?= static::item_offsetYPC ?>" widthPC="<?= static::item_widthPC ?>" heightPC="<?= static::item_heightPC ?>" >
      <?= $ua_images_path . static::item_image ?>
    </image>
	
	<image offsetXPC="<?= static::menu_offsetXPC ?>" offsetYPC="<?= static::menu_offsetYPC ?>" widthPC="<?= static::menu_widthPC ?>" heightPC="<?= static::menu_heightPC ?>" >
		<?= $ua_images_path . static::menu_image ?>
    </image>
				
	<text  align="<?= static::text_header_align ?>" redraw="<?= static::text_header_redraw ?>" lines="<?= static::text_header_lines ?>" offsetXPC="<?= static::text_header_offsetXPC ?>" offsetYPC="<?= static::text_header_offsetYPC ?>" widthPC="<?= static::text_header_widthPC ?>" heightPC="<?= static::text_header_heightPC ?>" fontSize="<?= static::text_header_fontSize ?>" backgroundColor="<?= static::text_header_backgroundColor ?>" foregroundColor="<?= static::text_header_foregroundColor ?>">
		Менеджер загрузок
	</text>
	
	<text  align="<?= static::text_footer_align ?>" redraw="<?= static::text_footer_redraw ?>" lines="<?= static::text_footer_lines ?>" offsetXPC="<?= static::text_footer_offsetXPC ?>" offsetYPC="<?= static::text_footer_offsetYPC ?>" widthPC="<?= static::text_footer_widthPC ?>" heightPC="<?= static::text_footer_heightPC ?>" fontSize="<?= static::text_footer_fontSize ?>" backgroundColor="<?= static::text_footer_backgroundColor ?>" foregroundColor="<?= static::text_footer_foregroundColor ?>">
		 	 
	</text>
	
		<text redraw="no" offsetXPC="<?= static::status_header_offsetXPC ?>"  offsetYPC="<?= static::status_header_offsetYPC ?>" widthPC="<?= static::status_header_widthPC ?>" heightPC="<?= static::status_header_heightPC ?>" fontSize="<?= static::status_header_fontSize ?>" backgroundColor="<?= static::status_header_backgroundColor ?>" foregroundColor="<?= static::status_header_foregroundColor ?>">СТАТУС</text>
		<text redraw="no" offsetXPC="<?= static::name_header_offsetXPC ?>"  offsetYPC="<?= static::status_header_offsetYPC ?>" widthPC="<?= static::name_header_widthPC ?>" heightPC="<?= static::status_header_heightPC ?>" fontSize="<?= static::status_header_fontSize ?>" backgroundColor="<?= static::status_header_backgroundColor ?>" foregroundColor="<?= static::status_header_foregroundColor ?>">ИМЯ ФАЙЛА</text>
		<text redraw="no" offsetXPC="<?= static::done_header_offsetXPC ?>"  offsetYPC="<?= static::status_header_offsetYPC ?>" widthPC="<?= static::done_header_widthPC ?>" heightPC="<?= static::status_header_heightPC ?>" fontSize="<?= static::status_header_fontSize ?>" backgroundColor="<?= static::status_header_backgroundColor ?>" foregroundColor="<?= static::status_header_foregroundColor ?>">ВЫПОЛНЕНО</text>
	
	<?php	
		
	}
	
    // функция выводит список загрузок
	//-------------------------------
	public function itemDisplay_content()
	{
	global $ua_images_path;
	?>
	<image  offsetXPC="<?= static::down_item_image_display_offsetXPC ?>" offsetYPC="<?= static::down_item_image_display_offsetYPC ?>" widthPC="<?= static::down_item_image_display_widthPC ?>" heightPC="<?= static::down_item_image_display_heightPC ?>">
			<script>
			state = getStringArrayAt(pidstateArray,idx);
			if (state == "loading") 
				{
				state = "<?=$ua_images_path?>ua_download_loading.png";
				}
			else if	(state == "done") 
				{
				state = "<?=$ua_images_path?>ua_download_done.png";
				}
			else if	(state == "stopped") 	
				{
				state = "<?=$ua_images_path?>ua_download_stop.png";
				}
			else if	(state == "none") 	
				{
				state = "<?=$ua_images_path?>ua_download_none.png";
				}	
			state;
			</script>
	</image>
	
	<text offsetXPC="<?= static::down_itemdisplay_offsetXPC ?>" offsetYPC="<?= static::down_itemdisplay_offsetYPC ?>" widthPC="<?= static::down_itemdisplay_widthPC ?>" heightPC="<?= static::down_itemdisplay_heightPC ?>" fontSize="<?= static::down_itemdisplay_fontSize ?>" lines="<?= static::down_itemdisplay_lines ?>">
		<foregroundColor><script>color;</script></foregroundColor>
	    <script>getStringArrayAt(pidfileArray,idx);</script>
	</text>	
	
	<text offsetXPC="<?= static::down_item_percent_display_offsetXPC ?>" offsetYPC="<?= static::down_item_percent_display_offsetYPC ?>" widthPC="<?= static::down_item_percent_display_widthPC ?>" heightPC="<?= static::down_item_percent_display_heightPC ?>" fontSize="<?= static::down_item_percent_display_fontSize ?>" lines="<?= static::down_item_percent_display_lines ?>">
		<foregroundColor><script>color;</script></foregroundColor>
		<script>getStringArrayAt(pidpercentArray,idx)+"%";</script>
	</text>	
	
	<?php
	}
	
	public function onRefresh()
	{
	global $ua_path;
	global $ua_path_link;
	global $exua_parser_filename;
	?>
	<onRefresh>
	setRefreshTime(-1);    
	showIdle();
    itemCount = 0;
	    pidfileArray = null;
		pidlinkArray = null;
		pidpercentArray = null;
		pidstateArray = null;       	
		pidnumArray = null;       	
	dlok = getURL(url);
	if (dlok != null) dlok = readStringFromFile( dlok );
	if (dlok != null)
		{
		c = 0;
		itemCount = getStringArrayAt(dlok, c); c += 1;
			
			count = 0;
			while( count != itemCount )
			{
			pidpercentArray = pushBackStringArray( pidpercentArray, getStringArrayAt(dlok, c));  print("PERCENT-",getStringArrayAt(dlok, c));c += 1;
			pidstateArray = pushBackStringArray( pidstateArray, getStringArrayAt(dlok, c)); 	print("STATE-",getStringArrayAt(dlok, c));	c += 1;	
			pidfileArray = pushBackStringArray( pidfileArray, getStringArrayAt(dlok, c)); print("file-",getStringArrayAt(dlok, c));c += 1;			
			pidlinkArray = pushBackStringArray( pidlinkArray, getStringArrayAt(dlok, c)); print("link-",getStringArrayAt(dlok, c));c += 1;
			pidnumArray = pushBackStringArray( pidnumArray, getStringArrayAt(dlok, c)); print("num-",getStringArrayAt(dlok, c));c += 1;
			count += 1;
			}
		}
		
		cancelIdle();
		setFocusItemIndex(itm_index);
		setFocusMenuIndex(menu);
		redrawDisplay();
	</onRefresh>
	<?php
	}
	
	
	public function menu()
	{
	
	global $ua_path_link;
	global $ua_download_parser_filename;
	
	?>
	<menu_template>
	<displayTitle>
		<script>
			getStringArrayAt( menuArray , getQueryMenuIndex() );
		</script> 
	</displayTitle>
	<onClick>
		showIdle();	
		idx = getQueryItemIndex();
		pidfile = getStringArrayAt(pidfileArray, idx);
		pidlink = getStringArrayAt(pidlinkArray, idx);
		pidnum = getStringArrayAt(pidnumArray, idx);
		act = getStringArrayAt( menucmdArray , menu );
		
		if (act == "pause" ){
		 action = "kill="+pidnum; 
		 }
 		else if (act == "continue" ){
		 action = "title=" + urlEncode(pidfile) + "&amp;downloadlink=" + urlEncode(pidlink);
		  }
        	else if (act == "delete" ){
		 action = "delete=" + pidlink; 
		 }
 		else if (act == "clear" ){
		 action = "clear=1"; 
		 }
      	 
		 dlok = getURL("<?=$ua_path_link?>ua_download_parser.php?"+action);
		 print("ACTION",action);
		 url = "<?=$ua_path_link.$ua_download_parser_filename."?display=1"?>";
		 setRefreshTime(1);
		
		null;
	</onClick>
	</menu_template>
	<?php
	}
	
	
//-------------------------------
	public function onEnters()
	{
	global $ua_path_link;
	global $ua_download_parser_filename;
	?>
	<onEnter>
	 returnFromLink=readStringFromFile("/tmp/env_returnFromLink_message");
	 returnFromList=readStringFromFile("/tmp/env_returnFromList_message");
		if (returnFromLink == "1" || returnFromList == "1")
			{
				writeStringToFile("/tmp/env_returnFromLink_message", "");
				writeStringToFile("/tmp/env_returnFromList_message", "");
			}
	else
	{ 	

	<?php
		if (isset($_GET['display'])){ 
			$url=$ua_path_link.$ua_download_parser_filename."?display=1";
			?>
			url = "<?= $url?>";
			<?php
			
			}
		
		if	(isset($_GET["downloadlink"]) && isset($_GET["title"])  ) {
			$title = $_GET["title"];
			$link  = $_GET["downloadlink"];
			$url=$ua_path_link.$ua_download_parser_filename."?title=";
			?>
				url = "<?= $url?>"+urlEncode("<?= $title?>")+"&amp;downloadlink="+urlEncode("<?= $link?>");
			<?php
		}
		?>
		print ("URL",url);	
		menuArray = null;
		menucmdArray = null;
		menuCount = 5;
		menuArray = pushBackStringArray( menuArray, "Обновить");
		menucmdArray = pushBackStringArray( menucmdArray, "refresh");
		menuArray = pushBackStringArray( menuArray, "Пауза");
		menucmdArray = pushBackStringArray( menucmdArray, "pause");
		menuArray = pushBackStringArray( menuArray, "Продол.загруз.");
		menucmdArray = pushBackStringArray( menucmdArray, "continue");
		menuArray = pushBackStringArray( menuArray, "Удалить");
		menucmdArray = pushBackStringArray( menucmdArray, "delete");
		menuArray = pushBackStringArray( menuArray, "Очистить спис.");
		menucmdArray = pushBackStringArray( menucmdArray, "clear"); 
		itm_index=0;
		menu=0;
		setRefreshTime(1);    
	}
	</onEnter>
	<onExit>
		writeStringToFile("/tmp/env_returnFromList_message", "1");
	</onExit>
	<?php
	}	
	
	//-------------------------------
	public function items()
	{
	?>
	<item_template>
	<onClick>
	 idx = getFocusItemIndex();
	 null;
	</onClick>
	</item_template>
	<?php
	}
	
	public function channel()
	{
	global $ua_path;
	global $exua_rss_list_filename;
	?>
	<channel>
	<title>download</title>
	<link><?=$ua_path."ua_download_rss.php"?></link>
	<itemSize>
		<script>
			itemCount;
		</script>
    </itemSize>
	<menuSize>
		<script>
			menuCount;
		</script>
    </menuSize>
		
	</channel>
		
	<?php
	}
}
//-------------------------------
$view = new ua_rss_download;
$view->showRss();
exit;