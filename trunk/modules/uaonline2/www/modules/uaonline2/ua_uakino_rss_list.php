<?php
/*	------------------------------
	Ukraine online services 	
	uakino.net RSS list module v1.3
	------------------------------
	Created by Sashunya 2012	
	wall9e@gmail.com			
	Some code was used from 
	Farvoice & others 
	------------------------------ */

include( 'ua_rss_view.php' );

class ua_rss_list extends ua_rss_list_const
{
	// функция вставляет бордюры все картинки и текст
	//-------------------------------
	public function mediaDisplay_content()
	{
	global $ua_images_path;
	include ("ua_rss_images_rss_list.inc.php");
	?>
		
		<image  offsetXPC="<?= static::image_site_footer_display_offsetXPC ?>" offsetYPC="<?= static::image_site_footer_display_offsetYPC ?>" widthPC="<?= static::image_site_footer_display_widthPC ?>" heightPC="<?= static::image_site_footer_display_heightPC ?>">
					<?= $ua_images_path . static::uakino_logo ?>
		</image>
	
			<text  align="<?= static::text_footer_align ?>" redraw="<?= static::text_footer_redraw ?>" lines="<?= static::text_footer_lines ?>" offsetXPC="<?= static::text_footer_offsetXPC ?>" offsetYPC="<?= static::text_footer_offsetYPC ?>" widthPC="<?= static::text_footer_widthPC ?>" heightPC="<?= static::text_footer_heightPC ?>" fontSize="<?= static::text_footer_fontSize ?>" backgroundColor="<?= static::text_footer_backgroundColor ?>" foregroundColor="<?= static::text_footer_foregroundColor ?>">
		 <script>
			footer = "Страница :"+page; 
		 footer; </script>
	</text>
	
	<?php	
		
	}
	// функция выводит список фильмов
	//-------------------------------
	public function itemDisplay_content()
	{
	include("ua_rss_itemdisplay_rss_list.inc.php");
	}
	
	//-------------------------------
	public function onRefresh()
	{
	global $ua_path;
	global $ua_path_link;
	global $uakino_parser_filename;
	global $ua_favorites_filename;
	?>
	<onRefresh>
		setRefreshTime(-1);    
		ua_sort = getURL("<?=$ua_path_link.$uakino_parser_filename.'?sort=req'?>");
		showIdle();
		itemCount = 0;
		title = null;
		titleArray = null;
		imageArray = null;
		linkArray = null;
		typelinkArray = null;
		booklinkArray = null;
		menuArray = null;
		menucmdArray = null;
		menuCount = 4;
		menuArray = pushBackStringArray( menuArray, "След.страница");
		menucmdArray = pushBackStringArray( menucmdArray, "next");
		if (page > 1) {
			menuCount = 5;
			menuArray = pushBackStringArray( menuArray, "Пред.страница");
			menucmdArray = pushBackStringArray( menucmdArray, "prev");
		}
		
		if (ua_sort == "date") menuArray = pushBackStringArray( menuArray, "Сорт.Дата");
		if (ua_sort == "rating") menuArray = pushBackStringArray( menuArray, "Сорт.Рейтинг");
		if (ua_sort == "views") menuArray = pushBackStringArray( menuArray, "Сорт.Просмотр");
		menucmdArray = pushBackStringArray( menucmdArray, "sort");
		
		menuArray = pushBackStringArray( menuArray, "В избранное");
		menucmdArray = pushBackStringArray( menucmdArray, "bookmark");
				
		menuArray = pushBackStringArray( menuArray, "Выход");
		menucmdArray = pushBackStringArray( menucmdArray, "exit");
		
		dlok = getURL(url1+page);
		print("***dlok"+dlok);
		if (dlok != null) dlok = readStringFromFile( dlok );
		if (dlok != null)
		{
			title=getStringArrayAt(dlok, 0);
			c = 1;
			itemCount = getStringArrayAt(dlok, c); c += 1;
			count = 0;
			while( count != itemCount )
				{
					titleArray = pushBackStringArray( titleArray, getStringArrayAt(dlok, c)); c += 1;
					linkArray = pushBackStringArray( linkArray, getStringArrayAt(dlok, c)); c += 1;			
					imageArray = pushBackStringArray( imageArray, getStringArrayAt(dlok, c)); c += 1;			
					booklinkArray = pushBackStringArray( booklinkArray, getStringArrayAt(dlok, c)); c += 1;			
					typelinkArray = pushBackStringArray( typelinkArray, getStringArrayAt(dlok, c)); c += 1;
					count += 1;
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
	global $uakino_parser_filename;
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
		act = getStringArrayAt( menucmdArray , menu );
		if (act == "exit") postMessage("<?=$key_return?>"); else
		if (act == "bookmark") {
			idx = getQueryItemIndex();
			btitle = getStringArrayAt(titleArray,idx);
			blink = getStringArrayAt(booklinkArray,idx);
			bimage = getStringArrayAt(imageArray,idx);
			bsitelink = 3;
			btype = getStringArrayAt(typelinkArray,idx);
			titleBookArray = pushBackStringArray(titleBookArray, btitle);
			linkBookArray = pushBackStringArray(linkBookArray, blink);
			imageBookArray = pushBackStringArray(imageBookArray, bimage);
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
		}  else
		if (act == "sort") {
			if (ua_sort == "date") ua_sort = "rating"; else
			if (ua_sort == "rating") ua_sort = "views"; else
			if (ua_sort == "views") ua_sort = "date";
			data = getURL("<?=$ua_path_link.$uakino_parser_filename.'?sort=send&amp;ua_sort_val='?>"+ua_sort);
			setRefreshTime(1);
		}
		
		{
			if (act == "next") {page+=1; itm_index=0;}
			if (act == "prev") {page-=1; itm_index=0;}
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
		 if   (isset($_GET['view'])) {
			$param=$_GET['view'];
			$url=$ua_path_link."ua_uakino_parser.php?view=";
		}
		 if   (isset($_GET['search'])) {
		$param=$_GET['search'];
		$url= $ua_path_link."ua_uakino_parser.php?search=";
		}
	?>
	param = "<?= $param?>";
	param2 = "&amp;page=";
	page = 1;
	url1 = "<?= $url?>"+urlEncode(param)+param2;
	print("!!!!!!!!___"+param);
	print("!!!!!!!!___"+url1);
	setRefreshTime(1);    
	itm_index=0;
	menu=0;
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
	include ("ua_rss_items_rss_list.inc.php");
	}
	//-------------------------------
	public function channel()
	{
	global $ua_path;
	global $uakino_rss_list_filename;
	?>
	<channel>
	<title>TEST</title>
	<link><?=$ua_path.$uakino_rss_list_filename?></link>

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
$view = new ua_rss_list;
$view->showRss();
exit;
?>