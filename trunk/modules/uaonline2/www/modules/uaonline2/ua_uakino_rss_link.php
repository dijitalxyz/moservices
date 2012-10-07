<?php
/*	------------------------------
	Ukraine online services 	
	UAKINO.NET RSS link module v1.4
	------------------------------
	Created by Sashunya 2012	
	wall9e@gmail.com			
	Some code was used from 
	Farvoice & others 
	------------------------------ */


include( 'ua_rss_view.php' );

class ua_rss_link_const2 extends ua_rss_link_const
{
	const text_site_footer_offsetXPC	=	'75';
	const text_site_footer_widthPC		=	'20';
}

class ua_rss_link extends ua_rss_link_const2
{
	// функция вставляет бордюры все картинки и текст
	//-------------------------------
	public function mediaDisplay_content()
	{
	global $ua_images_path;
	include ("ua_rss_images_rss_link.inc.php");
	?>
		
		<image  offsetXPC="<?= static::image_site_footer_display_offsetXPC ?>" offsetYPC="<?= static::image_site_footer_display_offsetYPC ?>" widthPC="<?= static::image_site_footer_display_widthPC ?>" heightPC="<?= static::image_site_footer_display_heightPC ?>">
					<?= $ua_images_path . static::uakino_logo ?>
		</image>
	
		<?php
			include ("ua_rss_description.inc.php");
		?>
	
		<text  align="<?= static::text_footer_align ?>" redraw="<?= static::text_footer_redraw ?>" lines="<?= static::text_footer_lines ?>" offsetXPC="<?= static::text_footer_offsetXPC ?>" offsetYPC="<?= static::text_footer_offsetYPC ?>" widthPC="<?= static::text_footer_widthPC ?>" heightPC="<?= static::text_footer_heightPC ?>" fontSize="<?= static::text_footer_fontSize ?>" backgroundColor="<?= static::text_footer_backgroundColor ?>" foregroundColor="<?= static::text_footer_foregroundColor ?>">
		 <script>
			footer = "Плеер: "+type_player;
		 footer; </script>
	</text>
	
	<?php	
		
	}
	// функция выводит список фильмов
	//-------------------------------
	public function itemDisplay_content()
	{
	?>
		<text  offsetXPC="<?= static::itemdisplay_offsetXPC ?>" offsetYPC="<?= static::itemdisplay_offsetYPC ?>" widthPC="<?= static::itemdisplay_widthPC ?>" heightPC="<?= static::itemdisplay_heightPC ?>" fontSize="<?= static::itemdisplay_fontSize ?>" lines="<?= static::itemdisplay_lines ?>">
		<foregroundColor>
			<script>
				color;
			</script>
		</foregroundColor>
	    <script>name;</script>
	</text>	
	
	<?php
	}
	//-------------------------------
	public function onRefresh()
	{
	global $ua_path;
	global $ua_path_link;
	global $uakino_rss_parser_filename;
	global $ua_favorites_filename;
	?>
	
	<onRefresh>
	setRefreshTime(-1);    
	showIdle();
	if (pstyle == "1") 	type_player="Альтерн.";
		else
						type_player="Стандарт.";
	dlok = getURL(url1);
	if (dlok != null) dlok = readStringFromFile( dlok );
		if (dlok != null)
			{
				title = getStringArrayAt(dlok, 0); 
				ds1 = getStringArrayAt(dlok, 1);
				ds2 = getStringArrayAt(dlok, 2);
				ds3 = getStringArrayAt(dlok, 3);
				ds4 = getStringArrayAt(dlok, 4);
				ds5 = getStringArrayAt(dlok, 5);
				ds6 = getStringArrayAt(dlok, 6);
				ds7 = getStringArrayAt(dlok, 7);
				ds8 = getStringArrayAt(dlok, 8);
				ds9 = getStringArrayAt(dlok, 9);
				img = getStringArrayAt(dlok, 10);
				down = getStringArrayAt(dlok, 12);
				link = getStringArrayAt(dlok, 13);	 
				name = getStringArrayAt(dlok, 14);	 
				
	 		}
			
		menuArray = null;
		menucmdArray = null;
		menuCount = 3;
		menuArray = pushBackStringArray( menuArray, "Загрузить");
		menucmdArray = pushBackStringArray( menucmdArray, "download");
		menuArray = pushBackStringArray( menuArray, "Менеджер");
		menucmdArray = pushBackStringArray( menucmdArray, "manager");
		menuArray = pushBackStringArray( menuArray, "Плеер");
		menucmdArray = pushBackStringArray( menucmdArray, "player_style");
		
		
		setFocusItemIndex(itm_index);
		setFocusMenuIndex(menu);
		redrawDisplay();	
	</onRefresh>
	<?php
	}
	//-------------------------------
	public function menu()
	{
	global $ua_path;
	global $ua_path_link;
	global $key_return;
	global $ua_rss_download_filename;
	global $ua_player_parser_filename;
	include ("ua_rss_download_rss_link.inc.php");
	?>
	<menu_template>
	<displayTitle>
		<script>
			getStringArrayAt( menuArray , getQueryMenuIndex() );
		</script> 
	</displayTitle>
	<onClick>
		showIdle();
		act = getStringArrayAt( menucmdArray , menu );
		if (act == "exit") postMessage("<?=$key_return?>"); 
		if (act == "download") {
			down_jump="<?=$ua_path_link.$ua_rss_download_filename ?>"+"?title=" + urlEncode(down) + "&amp;downloadlink=" + urlEncode(link);
			jumpToLink("download");
		}
		else
		if (act == "player_style") {
			
			if (pstyle == "1") pstyle = "0"; else pstyle = "1";
			data = getURL("<?=$ua_path_link.$ua_player_parser_filename.'?player_style=send&amp;player_style_val='?>"+pstyle);
			setRefreshTime(1);
		}
		else
		if (act == "manager") {
			down_jump="<?=$ua_path_link.$ua_rss_download_filename."?display=1"?>";
			jumpToLink("download");
		}
		null;
	</onClick>
	</menu_template>
	<?php
	}
	//-------------------------------
	public function onEnters()
	{
	global $ua_path_link;
	global $uakino_parser_filename;
	global $ua_player_parser_filename;
	global $xtreamer;
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
		 if (isset($_GET['file'])) {
			$param=$_GET['file'];
			$url=$ua_path_link.$uakino_parser_filename."?file=";
			}
				
	?>
	param = "<?= $param?>";
	url1 = "<?= $url ?>"+param;
	pstyle = getURL("<?=$ua_path_link.$ua_player_parser_filename.'?player_style=req'?>");
	itm_index=0;
	menu=0;
	setRefreshTime(1);    
	}
	</onEnter>
	
	<onExit>
		<?php
		if ($xtreamer)
			{
			?>
				SwitchViewer(0);
			<?php
			}
		?>	
		writeStringToFile("/tmp/env_returnFromLink_message", "1");
	</onExit>
	<?php
	}
	//-------------------------------
	public function items()
	{
	global $ua_path_link;
	global $xtreamer;
	?>
		<playLink_OSD>
		<link>
			<script>
				url = "<?= $ua_path_link ?>ua_player.php?idx=0";
			</script>
		</link>
	</playLink_OSD>
		
	<item_template>
		<onClick>
			<script>
				if (pstyle == 1)
				{
					jumpToLink("playLink_OSD");
				} else
				{
				<?php
				if ($xtreamer)
					{
					?>
						SwitchViewer(7);
					<?php
					}
				?>
				playItemURL(link, 0);
				}
			</script>
			null;
		</onClick>
	</item_template>
	<?php
	}
	//-------------------------------
	public function channel()
	{
	global $ua_path;
	global $filmy_rss_link_filename;
	?>
	<channel>
	<title>LINK</title>
	<link><?=$ua_path.$filmy_rss_link_filename?></link>

	<itemSize>
		1
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
$view = new ua_rss_link;
$view->showRss();
exit;
?>