<?php
/*	------------------------------
	Ukraine online services 	
	fs.ua RSS category module v1.9
	------------------------------
	Created by Sashunya 2014
	wall9e@gmail.com			
	Some code was used from 
	Farvoice & others 
	------------------------------ */

include( 'ua_rss_view_photo.php' );

class ua_rss_cat_const2 extends ua_rss_cat_const
{
	const text_site_footer_offsetXPC	=	'75';
	const text_site_footer_widthPC		=	'20';
}

class ua_rss_cat extends ua_rss_cat_const2
{
	public function categories()
	{	
	global $ua_path_link;
	global $fsua_parser_cat_filename;
		// получаем категории и их ссылки для FS.UA	
		$url="http://brb.to";
		$s = file_get_contents($url);
		$cats=array();
		$doc = new DOMDocument();
		libxml_use_internal_errors( true );
		$doc->loadHTML($s);
		$divs= $doc->getElementsByTagName('div');
		foreach( $divs as $div )
				if( $div->hasAttribute('class'))
				{
				$div_class=$div->getAttribute('class');
				if( preg_match("/b-header__menu-section m-header__menu-section_type_(video|audio)/",$div_class))
				{
					$as = $div->getElementsByTagName('a');
						foreach( $as as $a )
						{
							if ($a->getAttribute('class') == "b-header__menu-subsections-item")
							{
								$link = $url.$a->getAttribute('href');
								$name = $a->textContent;
								$cats[trim($name)." ЗАРУБЕЖНЫЕ"]=$link.'fl_foreign/';
								$cats[trim($name)." НАШИ"]=$link.'fl_our/';
							}
						}
				}
				}
	
	return $cats; 
	}
		
	
	public function mediaDisplay_content()
	{
	global $ua_images_path;
	?>

		<text  align="<?= static::text_header_align ?>" redraw="<?= static::text_header_redraw ?>" lines="<?= static::text_header_lines ?>" offsetXPC="<?= static::text_header_offsetXPC ?>" offsetYPC="<?= static::text_header_offsetYPC ?>" widthPC="<?= static::text_header_widthPC ?>" heightPC="<?= static::text_header_heightPC ?>" fontSize="<?= static::text_header_fontSize ?>" backgroundColor="<?= static::text_header_backgroundColor ?>" foregroundColor="<?= static::text_header_foregroundColor ?>">
		Выбор категории
		</text>
	
		<image  offsetXPC="<?= static::image_site_footer_display_offsetXPC ?>" offsetYPC="<?= static::image_site_footer_display_offsetYPC ?>" widthPC="<?= static::image_site_footer_display_widthPC ?>" heightPC="<?= static::image_site_footer_display_heightPC ?>">
					<?= $ua_images_path . static::fsua_logo ?>
		</image>
	
		<text  align="<?= static::text_footer_align ?>" redraw="<?= static::text_footer_redraw ?>" lines="<?= static::text_footer_lines ?>" offsetXPC="<?= static::text_footer_offsetXPC ?>" offsetYPC="<?= static::text_footer_offsetYPC ?>" widthPC="<?= static::text_footer_widthPC ?>" heightPC="<?= static::text_footer_heightPC ?>" fontSize="<?= static::text_footer_fontSize ?>" backgroundColor="<?= static::text_footer_backgroundColor ?>" foregroundColor="<?= static::text_footer_foregroundColor ?>">
			Выход
		</text>
		<image redraw="no" offsetXPC="17" offsetYPC="90" widthPC="3" heightPC="6">
			<?=$ua_images_path?>ua_back.png
		</image>
	<?php	
		
	}
	
	
	//-------------------------------
	public function channel()
	{
	global $ua_path;
	global $ua_images_path;
	global $ua_path_link;
	global $ua_rss_keyboard_filename;
	global $ua_rss_favorites_filename;
	global $fsua_rss_list_filename;
	global $fsua_parser_filename;
	global $built_in_keyb;
	?>

	<searchLink>
			<link>
				<script>
					url;
				</script>
			</link>
	</searchLink>

	<channel>
	<item>
		<title>ПОИСК</title>
			<onClick>
				showIdle();
				<?
				if ($built_in_keyb == "1")
				{
					?>
					keyword = getInput("Search", "doModal");	
					<?
				} else
				{
				?>
					rss = "<?=$ua_path_link.$ua_rss_keyboard_filename?>";
					keyword = doModalRss(rss);
				<?
				}
				?>
				cancelIdle();
				if (keyword!=null)
				{
					url = "<?= $ua_path_link.$fsua_rss_list_filename."?search="?>"+urlEncode(keyword)+"&amp;";
					jumpToLink("searchLink");
				}
			</onClick>
		<image><?=$ua_images_path ?>ua_search.png</image>		
	</item>
		
	<item>
		<title>ИЗБРАННОЕ</title>
		<link><?=$ua_path_link.$ua_rss_favorites_filename?></link>
		<image><?=$ua_images_path ?>ua_favorites.png</image>		
	</item>
<?php
	$categ=$this->categories();
	foreach ($categ as $key=>$cat)
	{
		if (preg_match("/\/video\//",$cat)) $fold= "ua_folder.png";
		if (preg_match("/\/audio\//",$cat)) $fold= "ua_folder_audio.png";
		echo "	<item>\n";
		echo "			<title>".$key."</title>\n";
		echo "			<link>".$ua_path_link.$fsua_rss_list_filename."?view=".urlencode($cat)."&amp;en_sort=1&amp;name=".urlencode($key)."</link>\n"; 
		echo "			<image>".$ua_images_path.$fold."</image>\n";
		echo "	</item>\n";
	}
	
	?>

	</channel>
	<?php
	}

}
//-------------------------------

$view = new ua_rss_cat;
$view->showRss();

exit;
?>