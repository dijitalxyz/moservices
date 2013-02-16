<?php
/*	------------------------------
	Ukraine online services 	
	FS.UA RSS link module v1.5
	------------------------------
	Created by Sashunya 2012	
	wall9e@gmail.com			
	Some code was used from 
	Farvoice  & others 
	------------------------------ */


include( 'ua_rss_view.php' );

class ua_rss_link extends ua_rss_link_const
{
	// функция вставляет бордюры все картинки и текст
	//-------------------------------
	public function mediaDisplay_content()
	{
	global $ua_images_path;
	include ("ua_rss_images_rss_link.inc.php");
	?>
		
	<image  offsetXPC="<?= static::image_site_footer_display_offsetXPC ?>" offsetYPC="<?= static::image_site_footer_display_offsetYPC ?>" widthPC="<?= static::image_site_footer_display_widthPC ?>" heightPC="<?= static::image_site_footer_display_heightPC ?>">
					<?= $ua_images_path . static::fsua_logo ?>
	</image>
	
		<?php
		include ("ua_rss_description.inc.php");
		?>
	
		<text  align="<?= static::text_footer_align ?>" redraw="<?= static::text_footer_redraw ?>" lines="<?= static::text_footer_lines ?>" offsetXPC="<?= static::text_footer_offsetXPC ?>" offsetYPC="<?= static::text_footer_offsetYPC ?>" widthPC="<?= static::text_footer_widthPC ?>" heightPC="<?= static::text_footer_heightPC ?>" fontSize="<?= static::text_footer_fontSize ?>" backgroundColor="<?= static::text_footer_backgroundColor ?>" foregroundColor="<?= static::text_footer_foregroundColor ?>">
		 <script>
			footer = "Страница :"+page+"/"+countPage+" Плеер: "+type_player; 
		 footer; </script>
	</text>
	
	<?php	
		
	}
	// функция выводит список фильмов
	//-------------------------------
	public function itemDisplay_content()
	{
	include ("ua_rss_itemdisplay_rss_link.inc.php");
	}
	//-------------------------------
	public function onRefresh()
	{
	global $ua_path;
	global $ua_path_link;
	global $fsua_parser_filename;
	global $ua_favorites_filename;
	global $tmp;
	
	?>
	<initialScript>
		
		pitemCount = 0;
		ptitleArray = null;
		plinkArray = null;
		pdownlinkArray = null;
		pdownameArray = null;
		pbooklinkArray = null;
		dlok = readStringFromFile( "<?=$tmp?>" );
		if (dlok != null)
			{
				ds1 = getStringArrayAt(dlok, 2);
				ds2 = getStringArrayAt(dlok, 3);
				ds3 = getStringArrayAt(dlok, 4);
				ds4 = getStringArrayAt(dlok, 5);
				ds5 = getStringArrayAt(dlok, 6);
				ds6 = getStringArrayAt(dlok, 7);
				ds7 = getStringArrayAt(dlok, 8);
				ds8 = getStringArrayAt(dlok, 9);
				ds9 = getStringArrayAt(dlok, 10);
				img = getStringArrayAt(dlok, 11);
				name = getStringArrayAt(dlok, 1);
				
				c = 12;
				pitemCount = getStringArrayAt(dlok, c); c += 1;
				count = 0;
				fav_idx = 0;
				found = 0;
				while( count != pitemCount )
					{
					ptitleArray = pushBackStringArray( ptitleArray, getStringArrayAt(dlok, c)); c += 1;
					plinkArray = pushBackStringArray( plinkArray, getStringArrayAt(dlok, c)); c += 1;			
					pdownlinkArray = pushBackStringArray( pdownlinkArray, getStringArrayAt(dlok, c)); c += 1;			
					pdowname = getStringArrayAt(dlok, c);
					pdownameArray = pushBackStringArray( pdownameArray, pdowname); c += 2;						
					pbooklinkArray = pushBackStringArray( pbooklinkArray, getStringArrayAt(dlok, c)); c += 1;						
					
					count += 1;
					cnt = 0;
						if ( found == 0)
						{
							print ("histCount----------------------",histCount);
							while( cnt != histCount )
							{
								histFiles=getStringArrayAt(historyFilesArray, cnt);
								print ("histFiles----------------------",histFiles);
								if (histFiles == pdowname) 
								{
									fav_idx = count;
									found = 1;
									
								} else found = 0;
								
								cnt += 1;
							}	
						}
					}
					if (found == 1)
					{
					
					page = 0;
					i = 0;
					while (i &lt; fav_idx)
					{
						i +=  20;
						page += 1;
					}
					i -=20;
					if (p == 1 ) itm_index=fav_idx-1; else itm_index = (i-fav_idx+1)*-1;
					
					print ("i----------------------",i);
					print ("page----------------------",page);
					print ("itm_index----------------------",itm_index);
					
					}
			}	
			countPage = 0;
			i = 0;
			while (i &lt; pitemCount)
			{
				i +=  20;
				countPage += 1;
			}
		print ("countPage - "+countPage);
		
	</initialScript>
	
	<onRefresh>
	setRefreshTime(-1);    
	showIdle();
    
	
	
	itemCount = 0;
	titleArray = null;
	linkArray = null;
	downlinkArray = null;
	downameArray = null;
	booklinkArray = null;
	  if (pstyle == "1") 	type_player="Альтерн.";
		else
							type_player="Стандарт.";
	
		if ( page &lt;= countPage ){
			maxCount = page*20;
			count = maxCount-20;
			if (maxCount &gt; pitemCount) maxCount = pitemCount; 
			itemCount = maxCount - count;
			print ("maxCount - "+maxCount);
			print ("count - "+count);
			while( count &lt;= maxCount )
				{
				titleArray = pushBackStringArray( titleArray, getStringArrayAt(ptitleArray, count));
				linkArray = pushBackStringArray( linkArray, getStringArrayAt(plinkArray, count));			
				downlinkArray = pushBackStringArray( downlinkArray, getStringArrayAt(pdownlinkArray, count)); 
				downameArray = pushBackStringArray( downameArray, getStringArrayAt(pdownameArray, count)); 						
				booklinkArray = pushBackStringArray( booklinkArray, getStringArrayAt(pbooklinkArray, count)); 						
				count -=- 1;
				}
		}
	
		menuArray = null;
		menucmdArray = null;
		menuCount = 3;
		if ( page &lt; countPage ){
			menuArray = pushBackStringArray( menuArray, "След.страница");
			menucmdArray = pushBackStringArray( menucmdArray, "next");
			menuCount += 1;
			}
		if (page > 1) {
			menuCount += 1;
			menuArray = pushBackStringArray( menuArray, "Пред.страница");
			menucmdArray = pushBackStringArray( menucmdArray, "prev");
			} 
		if(countPage > 2){
			if(page + 1 &lt; countPage)
			{
				menuCount += 1;
				menuArray = pushBackStringArray( menuArray, "Послед.страница");
				menucmdArray = pushBackStringArray( menucmdArray, "last");
			}
			if(page > 2)
			{
				menuCount += 1;
				menuArray = pushBackStringArray( menuArray, "Перв.страница");
				menucmdArray = pushBackStringArray( menucmdArray, "first");
			}	
			}
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
	global $fsua_parser_filename;
	global $key_return;
	global $ua_favorites_filename;
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
		idx = getQueryItemIndex();
		blink = getStringArrayAt(linkArray,idx);
		bdownlink = getStringArrayAt(downlinkArray,idx);
		bdowname = getStringArrayAt(downameArray,idx);
		bbooklink = getStringArrayAt(booklinkArray,idx);
		
		if (act == "exit") postMessage("<?=$key_return?>"); 
		else
		if (act == "player_style") {
			
			if (pstyle == "1") pstyle = "0"; else pstyle = "1";
			data = getURL("<?=$ua_path_link.$ua_player_parser_filename.'?player_style=send&amp;player_style_val='?>"+pstyle);
			setRefreshTime(1);
		}
		else
		if (act == "download") {
			down_jump="<?=$ua_path_link.$ua_rss_download_filename ?>"+"?title=" + urlEncode(bdowname) + "&amp;downloadlink=" + bdownlink;
			jumpToLink("download");
		}
		else
		if (act == "manager") {
			down_jump="<?=$ua_path_link.$ua_rss_download_filename."?display=1"?>";
			jumpToLink("download");
		}
		else
		{
			if (act == "next") {page-=-1; itm_index=0;}
			if (act == "prev") {page-=1; itm_index=0;}
			if(act == "first") { page =1; itm_index = 0;}
			if(act == "last") { page = countPage; itm_index = 0;}	
			setRefreshTime(1);
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
	global $ua_player_parser_filename;
	global $xtreamer;
	global $tmp;
	?>
	<onEnter>
	site = 2;
	returnFromLink=readStringFromFile("/tmp/env_returnFromLink_message");
	returnFromList=readStringFromFile("/tmp/env_returnFromList_message");
		if (returnFromLink == "1" || returnFromList == "1")
			{
				writeStringToFile("/tmp/env_returnFromLink_message", "");
				writeStringToFile("/tmp/env_returnFromList_message", "");
			}
	else
	{ 
	showIdle();
	
	
	pstyle = getURL("<?=$ua_path_link.$ua_player_parser_filename.'?player_style=req'?>");
	page = 1;
	itm_index=0;
	dlok = readStringFromFile( "<?=$tmp?>" );
	if (dlok != null)
		{
			param = getStringArrayAt(dlok, 0);
		}
	<?
		include ("ua_rss_historyfiles.inc.php");
	?>
	print ("param======================================================",param);
	executeScript("initialScript");
	cancelIdle();
	
	use_alt_player=readStringFromFile("/tmp/env_use_alt_player_message");
		if (use_alt_player == "1")
			{
				writeStringToFile("/tmp/env_use_alt_player_message", "");
				idx_play=readStringFromFile("/tmp/env_idx_play_message");
				print("idx_play",idx_play);
				page=idx_play / 20;
				if (page &gt; 10) div=100; else div =10;
				page=page % div;
				itm_index = idx_play-page*20;
				page-=-1;
			}
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
		writeStringToFile("/tmp/env_returnListIndex_message", listIndex);
	</onExit>
	<?php
	}
	//-------------------------------
	public function items()
	{
	include ("ua_rss_items_rss_link.inc.php");
	}
	//-------------------------------
	public function channel()
	{
	global $ua_path;
	global $fsua_rss_link_filename;
	?>
	<channel>
	<title>LINK</title>
	<link><?=$ua_path.$fsua_rss_link_filename?></link>

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
$view = new ua_rss_link;
$view->showRss();
exit;
?>