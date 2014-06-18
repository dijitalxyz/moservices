<?php
/*	------------------------------
	Ukraine online services 	
	RSS global search module v1.0
	------------------------------
	Created by Sashunya 2013	
	wall9e@gmail.com			
	Some code was used from 
	Farvoice & others 
	------------------------------ */

include( 'ua_rss_view.php' );
class ua_rss_search2 extends ua_rss_list_const
{
	const itemdisplay_widthPC	=	'80';
	
	const item_site_display_offsetXPC	=	'90';
	const item_site_display_offsetYPC	=	'20';
	const item_site_display_widthPC		=	'6';
	const item_site_display_heightPC	=	'50';

}
class ua_rss_search  extends ua_rss_search2
{
	// функция вставляет бордюры все картинки и текст
	//-------------------------------
	public function mediaDisplay_content()
	{
	global $ua_images_path;
	?>
	
			
	<text  align="<?= static::text_header_align ?>" redraw="<?= static::text_header_redraw ?>" lines="<?= static::text_header_lines ?>" offsetXPC="<?= static::text_header_offsetXPC ?>" offsetYPC="<?= static::text_header_offsetYPC ?>" widthPC="<?= static::text_header_widthPC ?>" heightPC="<?= static::text_header_heightPC ?>" fontSize="<?= static::text_header_fontSize ?>" backgroundColor="<?= static::text_header_backgroundColor ?>" foregroundColor="<?= static::text_header_foregroundColor ?>">
		<script>
			head;
		</script>
	</text>
	
	<text  align="<?= static::text_footer_align ?>" redraw="<?= static::text_footer_redraw ?>" lines="<?= static::text_footer_lines ?>" offsetXPC="<?= static::text_footer_offsetXPC ?>" offsetYPC="<?= static::text_footer_offsetYPC ?>" widthPC="<?= static::text_footer_widthPC ?>" heightPC="<?= static::text_footer_heightPC ?>" fontSize="<?= static::text_footer_fontSize ?>" backgroundColor="<?= static::text_footer_backgroundColor ?>" foregroundColor="<?= static::text_footer_foregroundColor ?>">
		 <script>
			footer = "Страница :"+dpage; 
		 footer; </script>
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
			<script> getStringArrayAt(imageArray,idx);</script>
	</image>
	
	<text offsetXPC="<?= static::itemdisplay_offsetXPC ?>" offsetYPC="<?= static::itemdisplay_offsetYPC ?>" widthPC="<?= static::itemdisplay_widthPC ?>" heightPC="<?= static::itemdisplay_heightPC ?>" fontSize="<?= static::itemdisplay_fontSize ?>" lines="<?= static::itemdisplay_lines ?>">
		<foregroundColor>
			<script>
				if (bookCount !=0 ){
					cnt = 0;
					blink = getStringArrayAt(booklinkArray,idx); 
					while( cnt != bookCount )
					{
						bookLink=getStringArrayAt(linkBookArray, cnt);
						if (bookLink == blink) 
						{
							color = "255:239:69";
							break;
						}
						
						cnt += 1;
						
					}	
				}
				color;
			</script>
		</foregroundColor>
	    <script> 
			getStringArrayAt(titleArray,idx);
		</script>
	</text>	
	<image  offsetXPC="<?= static::item_site_display_offsetXPC ?>" offsetYPC="<?= static::item_site_display_offsetYPC ?>" widthPC="<?= static::item_site_display_widthPC ?>" heightPC="<?= static::item_site_display_heightPC ?>">
			<script>
				site_logo=getStringArrayAt(sitelogoArray,getStringArrayAt(siteArray,idx));
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
	<initialScript>
		pitemCount = 0;
		ptitleArray =  null;
		plinkArray = null;
		pbooklinkArray = null;
		pimageArray = null;
		psiteArray = null;
		ptypeArray = null;
		dlok = getUrl( url+gpage );
		if (dlok != null) dlok = readStringFromFile(dlok);
		if (dlok != null)
			{
				c = 0;
				head = getStringArrayAt(dlok, c); c += 1;
				pitemCount = getStringArrayAt(dlok, c); c += 1;
				count = 0;
				while( count != pitemCount )
					{
					ptitleArray = pushBackStringArray(ptitleArray, getStringArrayAt(dlok, c));  c += 1;
					plinkArray = pushBackStringArray(plinkArray, getStringArrayAt(dlok, c));  c += 1;
					pimageArray = pushBackStringArray(pimageArray, getStringArrayAt(dlok, c));  c += 1;
					pbooklinkArray = pushBackStringArray(pbooklinkArray, getStringArrayAt(dlok, c));  c += 1;
					ptypeArray = pushBackStringArray(ptypeArray, getStringArrayAt(dlok, c));  c += 1;
					psiteArray = pushBackStringArray(psiteArray, getStringArrayAt(dlok, c));  c += 1;
					count += 1;
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
		titleArray =  null;
		linkArray = null;
		booklinkArray = null;
		imageArray = null;
		siteArray = null;
		typeArray = null;
		menuArray = null;
		menucmdArray = null;
		menuCount = 3;
		menuArray = pushBackStringArray( menuArray, "След.страница");
		menucmdArray = pushBackStringArray( menucmdArray, "next");
		if (dpage > 1) {
			menuCount = 4;
			menuArray = pushBackStringArray( menuArray, "Пред.страница");
			menucmdArray = pushBackStringArray( menucmdArray, "prev");
		} 
		menuArray = pushBackStringArray( menuArray, "В избранное");
		menucmdArray = pushBackStringArray( menucmdArray, "bookmark");
				
		menuArray = pushBackStringArray( menuArray, "Выход");
		menucmdArray = pushBackStringArray( menucmdArray, "exit");
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
				imageArray = pushBackStringArray( imageArray, getStringArrayAt(pimageArray, count)); 						
				booklinkArray = pushBackStringArray( booklinkArray, getStringArrayAt(pbooklinkArray, count)); 
				typeArray = pushBackStringArray( typeArray, getStringArrayAt(ptypeArray, count)); 						
				siteArray = pushBackStringArray( siteArray, getStringArrayAt(psiteArray, count)); 						
				count -=- 1;
				}
			}
			
		titleBookArray = null;
		linkBookArray = null;
		imageBookArray = null;
		typelinkBookArray = null;
		sitelinkBookArray = null;
		bookCount = 0;
		dlok = readStringFromFile( "<?=$ua_favorites_filename?>" );
		if (dlok != null)
			{
				c = 0;
				bookCount = getStringArrayAt(dlok, c); c += 1;
				count = 0;
				while( count != bookCount )
					{
					titleBookArray = pushBackStringArray(titleBookArray, getStringArrayAt(dlok, c));  c += 1;
					linkBookArray = pushBackStringArray(linkBookArray, getStringArrayAt(dlok, c));  c += 1;
					imageBookArray = pushBackStringArray(imageBookArray, getStringArrayAt(dlok, c));  c += 1;
					typelinkBookArray = pushBackStringArray(typelinkBookArray, getStringArrayAt(dlok, c));  c += 1;					
					sitelinkBookArray = pushBackStringArray(sitelinkBookArray, getStringArrayAt(dlok, c));  c += 1;					
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
	//-------------------------------
	public function menu()
	{
	global $ua_path;
	global $ua_path_link;
	global $key_return;
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
		if (act == "bookmark") {
			idx = getQueryItemIndex();
			btitle = getStringArrayAt(titleArray,idx);
			blink = getStringArrayAt(booklinkArray,idx);
			bimage = getStringArrayAt(imageArray,idx);
			bsitelink = getStringArrayAt(siteArray,idx);;
			btype = getStringArrayAt(typeArray,idx);
			titleBookArray = pushBackStringArray(titleBookArray, btitle);
			imageBookArray = pushBackStringArray(imageBookArray, bimage);
			linkBookArray = pushBackStringArray(linkBookArray, blink);
			typelinkBookArray = pushBackStringArray(typelinkBookArray, btype);
			sitelinkBookArray = pushBackStringArray(sitelinkBookArray, bsitelink);
			saveBookArray = null;
			bookCount -= -1;
			saveBookArray = pushBackStringArray(saveBookArray, bookCount);
			count = 0;
			while( count != bookCount )
				{				
				saveBookArray = pushBackStringArray(saveBookArray, getStringArrayAt(titleBookArray,count));
				saveBookArray = pushBackStringArray(saveBookArray, getStringArrayAt(linkBookArray,count));
				saveBookArray = pushBackStringArray(saveBookArray, getStringArrayAt(imageBookArray,count));
				saveBookArray = pushBackStringArray(saveBookArray, getStringArrayAt(typelinkBookArray,count));
				saveBookArray = pushBackStringArray(saveBookArray, getStringArrayAt(sitelinkBookArray,count));				
				count += 1;
				}
						
			writeStringToFile("<?=$ua_favorites_filename?>", saveBookArray);
			setRefreshTime(1);
		} else
		{
			if (act == "next") {page+=1; dpage+=1;itm_index=0;}
			if (act == "prev") {page-=1; dpage-=1;itm_index=0;}
			if ( page &gt; countPage )
			{
				gpage +=1;
				page = 1;
				executeScript("initialScript");
			} else
			if ( page == 0 )
			{
				gpage -=1;
				executeScript("initialScript");
				page = countPage;
			}
			print("PAGE=====================",page);
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
	global $ua_rss_keyboard_filename;
	global $key_return;
	global $built_in_keyb;
	
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
			
			keyword = readStringFromFile("/tmp/ua_temp.tmp");
			url = "<?=$ua_path_link."ua_search_parser.php?search_global="?>"+urlEncode(keyword)+"&amp;page_global=";
		showIdle();
		gpage = 1;
		dpage = 1;
		page = 1;
		executeScript("initialScript");
		itm_index=0;
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
	global $ua_path_link;
	?>
	<item_template>
		<link>
			<script>
			idx = getFocusItemIndex();
			url_link = getStringArrayAt( linkArray , idx );
			titl = getStringArrayAt( titleArray , idx );
			if (fsua == 1) writeStringToFile("/tmp/ua_title.tmp", titl);
			url_link;
			</script>
		</link>
	</item_template>
	<?php
	}
	//-------------------------------
	public function channel()
	{
	global $ua_path;
	?>
	<channel>
	<title>GLOBAL_SEARCH</title>
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
$view = new ua_rss_search;
$view->showRss();
exit;
?>