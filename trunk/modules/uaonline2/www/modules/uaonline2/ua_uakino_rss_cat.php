<?php
/*	------------------------------
	Ukraine online services 	
	uakino.net RSS category module v1.4
	------------------------------
	Created by Sashunya 2012
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
	//	парсим категории для их отображения
	$s=get_page("uakino.net","/video/");
	$doc = new DOMDocument();

	libxml_use_internal_errors( true );
	$doc->loadHTML($s);
	$divs= $doc->getElementsByTagName('div');
	foreach( $divs as $div )
	if( $div->hasAttribute('class'))
	if( $div->getAttribute('class') == 'catalog_tree' )
	{
		$uls = $div->getElementsByTagName('ul');
		$cats = array();
		foreach( $uls as $ul )
			{
				$lis = $ul->getElementsByTagName('li');
					foreach( $lis as $li )
					{
						$as = $li->getElementsByTagName('a');
						foreach( $as as $a )
							{
								$cat = $a->getAttribute('href');
								if(preg_match("/category\/video\//", $cat)) 
								{
									$cat = str_replace( 'category/video/','', $cat );
									$name = $a->textContent;
									$category [trim(uakino_utf8_check($name))] = array ("link"=>$cat);
									//$category [trim(utf8_decode($name))] = array ("link"=>$cat);
								}
							}
					}
			}
	}
	
	
	return $category; 
					
	}
		
	
	public function mediaDisplay_content()
	{
	global $ua_images_path;
	?>

		<text  align="<?= static::text_header_align ?>" redraw="<?= static::text_header_redraw ?>" lines="<?= static::text_header_lines ?>" offsetXPC="<?= static::text_header_offsetXPC ?>" offsetYPC="<?= static::text_header_offsetYPC ?>" widthPC="<?= static::text_header_widthPC ?>" heightPC="<?= static::text_header_heightPC ?>" fontSize="<?= static::text_header_fontSize ?>" backgroundColor="<?= static::text_header_backgroundColor ?>" foregroundColor="<?= static::text_header_foregroundColor ?>">
		Выбор категории
		</text>
	
		<image  offsetXPC="<?= static::image_site_footer_display_offsetXPC ?>" offsetYPC="<?= static::image_site_footer_display_offsetYPC ?>" widthPC="<?= static::image_site_footer_display_widthPC ?>" heightPC="<?= static::image_site_footer_display_heightPC ?>">
					<?= $ua_images_path . static::uakino_logo ?>
		</image>
	
		<text  align="<?= static::text_footer_align ?>" redraw="<?= static::text_footer_redraw ?>" lines="<?= static::text_footer_lines ?>" offsetXPC="<?= static::text_footer_offsetXPC ?>" offsetYPC="<?= static::text_footer_offsetYPC ?>" widthPC="<?= static::text_footer_widthPC ?>" heightPC="<?= static::text_footer_heightPC ?>" fontSize="<?= static::text_footer_fontSize ?>" backgroundColor="<?= static::text_footer_backgroundColor ?>" foregroundColor="<?= static::text_footer_foregroundColor ?>">
			Выход - RETURN
		</text>
	
	<?php	
		
	}
	
	
	//-------------------------------
	public function channel()
	{
	global $ua_path;
	global $uakino_rss_list_filename;
	global $ua_images_path;
	global $ua_path_link;
	global $ua_rss_keyboard_filename;
	global $ua_rss_favorites_filename;
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
					url = "<?= $ua_path_link.$uakino_rss_list_filename."?search="?>"+urlEncode(keyword)+"&amp;";
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
	<item>
		<title>Последние поступления</title>
		<link><?=$ua_path_link.$uakino_rss_list_filename."?view=last"?></link>
		<image><?=$ua_images_path ?>ua_folder.png</image>		
	</item>
<?php
	$categ=$this->categories();
	foreach ($categ as $key=>$cat){
		echo "	<item>\n";
		echo "			<title>".$key."</title>\n";
		foreach ($cat as $key2=>$value){
			echo "			<".$key2.">".$ua_path_link.$uakino_rss_list_filename."?view=".$value."</".$key2.">\n"; 
			echo "			<image>".$ua_images_path."ua_folder.png"."</image>\n";
		}
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