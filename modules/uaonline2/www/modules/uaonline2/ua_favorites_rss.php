<?php
/*	------------------------------
	Ukraine online services 	
	RSS bookmarks module v1.4
	------------------------------
	Created by Sashunya 2012	
	wall9e@gmail.com			
	Some code was used from 
	Farvoice & others 
	------------------------------ */

include( 'ua_rss_view.php' );
class ua_rss_favorites2 extends ua_rss_list_const
{
	const itemdisplay_widthPC	=	'80';
	
	const item_site_display_offsetXPC	=	'90';
	const item_site_display_offsetYPC	=	'20';
	const item_site_display_widthPC		=	'6';
	const item_site_display_heightPC	=	'50';

}
class ua_rss_favorites  extends ua_rss_favorites2
{
	// функция вставляет бордюры все картинки и текст
	//-------------------------------
	public function mediaDisplay_content()
	{
	global $ua_images_path;
	?>
	
			
	<text  align="<?= static::text_header_align ?>" redraw="<?= static::text_header_redraw ?>" lines="<?= static::text_header_lines ?>" offsetXPC="<?= static::text_header_offsetXPC ?>" offsetYPC="<?= static::text_header_offsetYPC ?>" widthPC="<?= static::text_header_widthPC ?>" heightPC="<?= static::text_header_heightPC ?>" fontSize="<?= static::text_header_fontSize ?>" backgroundColor="<?= static::text_header_backgroundColor ?>" foregroundColor="<?= static::text_header_foregroundColor ?>">
		Избранное
	</text>
	
	<text  align="<?= static::text_footer_align ?>" redraw="<?= static::text_footer_redraw ?>" lines="<?= static::text_footer_lines ?>" offsetXPC="<?= static::text_footer_offsetXPC ?>" offsetYPC="<?= static::text_footer_offsetYPC ?>" widthPC="<?= static::text_footer_widthPC ?>" heightPC="<?= static::text_footer_heightPC ?>" fontSize="<?= static::text_footer_fontSize ?>" backgroundColor="<?= static::text_footer_backgroundColor ?>" foregroundColor="<?= static::text_footer_foregroundColor ?>">
		
	</text>
	
	<?php	
		
	}
	// функция выводит список фильмов
	//-------------------------------
	public function itemDisplay_content()
	{
	global $ua_path_link;
	?>
	<image  offsetXPC="<?= static::item_image_display_offsetXPC ?>" offsetYPC="<?= static::item_image_display_offsetYPC ?>" widthPC="<?= static::item_image_display_widthPC ?>" heightPC="<?= static::item_image_display_heightPC ?>">
			<script> getStringArrayAt(imageBookArray,idx);</script>
	</image>
	
	<text offsetXPC="<?= static::itemdisplay_offsetXPC ?>" offsetYPC="<?= static::itemdisplay_offsetYPC ?>" widthPC="<?= static::itemdisplay_widthPC ?>" heightPC="<?= static::itemdisplay_heightPC ?>" fontSize="<?= static::itemdisplay_fontSize ?>" lines="<?= static::itemdisplay_lines ?>">
		<foregroundColor><script>color;</script></foregroundColor>
	    <script> 
			dname = getStringArrayAt( downameBookArray , idx );
			if (dname == "none") getStringArrayAt(titleBookArray,idx);
				else
					getStringArrayAt(titleBookArray,idx);
		</script>
	</text>	
	<image  offsetXPC="<?= static::item_site_display_offsetXPC ?>" offsetYPC="<?= static::item_site_display_offsetYPC ?>" widthPC="<?= static::item_site_display_widthPC ?>" heightPC="<?= static::item_site_display_heightPC ?>">
			<script>
				site_logo=getStringArrayAt(sitelogoArray,getStringArrayAt(siteBookArray,idx));
				site_logo;
			</script>
	</image>
	<?php
	}
	//-------------------------------
	public function onRefresh()
	{
	global $ua_path;
	global $ua_path_link;
	global $exua_parser_filename;
	global $ua_favorites_filename;
	?>
	<onRefresh>
		setRefreshTime(-1);    
		showIdle();
		itemCount = 0;
		titleBookArray =  null;
		linkBookArray = null;
		imageBookArray = null;
		siteBookArray = null;
		typeBookArray = null;
		menuArray = null;
		menucmdArray = null;
		menuCount = 3;
		menuArray = pushBackStringArray( menuArray, "Удалить");
		menucmdArray = pushBackStringArray( menucmdArray, "delete");
		menuArray = pushBackStringArray( menuArray, "Обновить");
		menucmdArray = pushBackStringArray( menucmdArray, "refresh");
		menuArray = pushBackStringArray( menuArray, "Выход");
		menucmdArray = pushBackStringArray( menucmdArray, "exit");
			
		dlok = readStringFromFile( "<?=$ua_favorites_filename?>" );
		if (dlok != null)
			{
				c = 0;
				itemCount = getStringArrayAt(dlok, c); c += 1;
				count = 0;
				while( count != itemCount )
					{
					titleBookArray = pushBackStringArray(titleBookArray, getStringArrayAt(dlok, c));  c += 1;
					linkBookArray = pushBackStringArray(linkBookArray, getStringArrayAt(dlok, c));  c += 1;
					imageBookArray = pushBackStringArray(imageBookArray, getStringArrayAt(dlok, c));  c += 1;
					typeBookArray = pushBackStringArray(typeBookArray, getStringArrayAt(dlok, c));  c += 1;
					siteBookArray = pushBackStringArray(siteBookArray, getStringArrayAt(dlok, c));  c += 1;
					count += 1;
					}
			}
		
		cancelIdle();
		tmp_index =itemCount-1;
		if (itm_index &gt; tmp_index) itm_index = tmp_index;
		
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
	global $exua_parser_filename;
	global $key_return;
	global $ua_rss_keyboard_filename;
	global $ua_favorites_filename;
	?>
	<menu_template>
	<displayTitle>
		<script>
			getStringArrayAt( menuArray , getQueryMenuIndex() );
		</script> 
	</displayTitle>
	<onClick>
		showIdle();
		idx = getFocusItemIndex();
		act = getStringArrayAt( menucmdArray , menu );
		if (act == "exit") postMessage("<?=$key_return?>");
		if (act == "delete") {
			titleBookArray = deleteStringArrayAt(titleBookArray, idx);
			linkBookArray = deleteStringArrayAt(linkBookArray, idx);
			imageBookArray = deleteStringArrayAt(imageBookArray, idx);
			siteBookArray = deleteStringArrayAt(siteBookArray, idx);
			typeBookArray = deleteStringArrayAt(typeBookArray, idx);
			itemCount -= 1;
			saveBookArray = null;
			saveBookArray = pushBackStringArray(saveBookArray, itemCount);
			count = 0;
			while( count != itemCount )
				{				
				saveBookArray = pushBackStringArray(saveBookArray, getStringArrayAt(titleBookArray,count));
				saveBookArray = pushBackStringArray(saveBookArray, getStringArrayAt(linkBookArray,count));
				saveBookArray = pushBackStringArray(saveBookArray, getStringArrayAt(imageBookArray,count));
				saveBookArray = pushBackStringArray(saveBookArray, getStringArrayAt(typeBookArray,count));
				saveBookArray = pushBackStringArray(saveBookArray, getStringArrayAt(siteBookArray,count));
				count += 1;
				}
				writeStringToFile("<?=$ua_favorites_filename?>", saveBookArray);
				setRefreshTime(1);
			} 
		
		if (act == "refresh") 
		{
			itemCount = 0;
			titleBookArray =  null;
			linkBookArray = null;
			imageBookArray = null;
			siteBookArray = null;
			typeBookArray = null;
			dlok = readStringFromFile( "<?=$ua_favorites_filename?>" );
			if (dlok != null)
			{
				c = 0;
				itemCount = getStringArrayAt(dlok, c); c += 1;
				count = 0;
				while( count != itemCount )
					{
					titleBookArray = pushBackStringArray(titleBookArray, getStringArrayAt(dlok, c));  c += 1;
					linkBookArray = pushBackStringArray(linkBookArray, getStringArrayAt(dlok, c));  c += 1;
					imageBookArray = pushBackStringArray(imageBookArray, getStringArrayAt(dlok, c));  c += 1;
					typeBookArray = pushBackStringArray(typeBookArray, getStringArrayAt(dlok, c));  c += 1;
					siteBookArray = pushBackStringArray(siteBookArray, getStringArrayAt(dlok, c));  c += 1;
					count += 1;
					}
			}

			site = getStringArrayAt( siteBookArray , idx );
			type = getStringArrayAt( typeBookArray , idx );
			link2 = getStringArrayAt( linkBookArray , idx );
			saveBookArray = null;
			saveBookArray = pushBackStringArray(saveBookArray, itemCount);
			count = 0;
			if (site!="1")
			{
				if (site!="-1")
				{ 
				link1 = getURL("<?=$ua_path_link.'ua_paths.inc.php?get_fav_site='?>"+site+"&amp;get_fav_type=parser");
				link= link1+link2+"&amp;fav_refresh=1";
				title1 = getURL(link);
				} 
				while( count != itemCount )
					{				
					if (count == idx &amp;&amp; site!="-1") 
					{
						saveBookArray = pushBackStringArray(saveBookArray, title1);					
					} 	else	saveBookArray = pushBackStringArray(saveBookArray, getStringArrayAt(titleBookArray,count));
					saveBookArray = pushBackStringArray(saveBookArray, getStringArrayAt(linkBookArray,count));
					saveBookArray = pushBackStringArray(saveBookArray, getStringArrayAt(imageBookArray,count));
					saveBookArray = pushBackStringArray(saveBookArray, getStringArrayAt(typeBookArray,count));
					saveBookArray = pushBackStringArray(saveBookArray, getStringArrayAt(siteBookArray,count));
					count += 1;
					}
				writeStringToFile("<?=$ua_favorites_filename?>", saveBookArray);
			}
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
	global $sites_logos_filename;
	global $ua_images_foldername;
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
		sitelogoArray = null;
		dlok = readStringFromFile( "<?=$sites_logos_filename?>" );
		if (dlok != null)
			{
				c = 0;
				itmCount = getStringArrayAt(dlok, c); c += 1;
				count = 0;
				while( count != itmCount )
					{
						logos = "<?=$ua_path_link.$ua_images_foldername?>"+getStringArrayAt(dlok, c);
						sitelogoArray = pushBackStringArray(sitelogoArray, logos); c += 1;
						count += 1;
					}
			}
		itm_index=0;
		menu=0;
		setRefreshTime(1);    
	}
	</onEnter>
	<?php
	}
	//-------------------------------
	public function items()
	{
	global $ua_path_link;
	?>
	<startLink>
		<link>
			<script>
				link;
			</script>
		</link>
	</startLink>
	
	
	<item_template>
		<onClick>
			<script>
				idx = getFocusItemIndex();
				link2 = getStringArrayAt( linkBookArray , idx );
				site = getStringArrayAt( siteBookArray , idx );
				type = getStringArrayAt( typeBookArray , idx );
				link1 = getURL("<?=$ua_path_link.'ua_paths.inc.php?get_fav_site='?>"+site+"&amp;get_fav_type="+type);
				link= link1+link2;
				jumpToLink("startLink");
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
	global $exua_rss_list_filename;
	?>
	<channel>
	<title>BOOKMARK</title>
	<link></link>

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
$view = new ua_rss_favorites;
$view->showRss();
exit;
?>