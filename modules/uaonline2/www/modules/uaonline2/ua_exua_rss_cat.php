<?php
/*	------------------------------
	Ukraine online services 	
	EX.ua RSS category module v1.2
	------------------------------
	Created by Sashunya 2012	
	wall9e@gmail.com			
	Some code was used from 
	Farvoice & others 
	------------------------------ */

include( 'ua_rss_view_photo.php' );

class ua_rss_cat extends ua_rss_cat_const
{
	public $language;
	public function categories()
	{	
	global $ua_path_link;
	global $ua_images_path;
	global $ua_images_category_path;
	global $exua_rss_list_filename;
	return $category = array(	
	
	"РУССКИЙ" => array 
	
		("ФИЛЬМЫ ЗАРУБЕЖНЫЕ" 	=> array (
									"link"=>$ua_path_link.$exua_rss_list_filename."?view=2",
									"image"=>$ua_images_category_path."ua_films_eng.png"),
		"ФИЛЬМЫ НАШИ" 			=> array (
									"link"=>$ua_path_link.$exua_rss_list_filename."?view=70538",
									"image"=>$ua_images_category_path."ua_films_rus.png"),
		"СЕРИАЛЫ НАШИ" 			=> array (
									"link"=>$ua_path_link.$exua_rss_list_filename."?view=422546",
									"image"=>$ua_images_category_path."ua_serials_rus.png"),
		"СЕРИАЛЫ ЗАРУБЕЖНЫЕ" 	=> array (
									"link"=>$ua_path_link.$exua_rss_list_filename."?view=1988",
									"image"=>$ua_images_category_path."ua_serials_eng.png"),
		"МУЛЬТФИЛЬМЫ" 			=> array (
									"link"=>$ua_path_link.$exua_rss_list_filename."?view=1989",
									"image"=>$ua_images_category_path."ua_mult.png"),
		"ДОКУМЕНТАЛЬНОЕ"		=> array (
									"link"=>$ua_path_link.$exua_rss_list_filename."?view=1987",
									"image"=>$ua_images_category_path."ua_docum.png"),
		"ПРИКОЛЫ"	 			=> array (
									"link"=>$ua_path_link.$exua_rss_list_filename."?view=23785",
									"image"=>$ua_images_category_path."ua_prikols.png"),
		"КЛИПЫ" 				=> array (
									"link"=>$ua_path_link.$exua_rss_list_filename."?view=1991",
									"image"=>$ua_images_category_path."ua_clips.png"),
		"КОНЦЕРТЫ" 				=> array (
									"link"=>$ua_path_link.$exua_rss_list_filename."?view=70533",
									"image"=>$ua_images_category_path."ua_concerts.png"),
		"ШОУ И ПЕРЕДАЧИ" 		=> array (
									"link"=>$ua_path_link.$exua_rss_list_filename."?view=28713",
									"image"=>$ua_images_category_path."ua_show.png"),
		"ТРЕЙЛЕРЫ" 				=> array (
									"link"=>$ua_path_link.$exua_rss_list_filename."?view=1990",
									"image"=>$ua_images_category_path."ua_trailers.png"),
		"СПОРТ" 				=> array (
									"link"=>$ua_path_link.$exua_rss_list_filename."?view=69663",
									"image"=>$ua_images_category_path."ua_sport.png"),						
		"АНИМЕ" 				=> array (
									"link"=>$ua_path_link.$exua_rss_list_filename."?view=23786",
									"image"=>$ua_images_category_path."ua_anime.png"),							
		"ТЕАТР" 				=> array (
									"link"=>$ua_path_link.$exua_rss_list_filename."?view=70665",
									"image"=>$ua_images_category_path."ua_theatre.png"),
		"ПРОПОВЕДИ" 			=> array (
									"link"=>$ua_path_link.$exua_rss_list_filename."?view=371146",
									"image"=>$ua_images_category_path."ua_religion.png"),
		"РЕКЛАМНЫЕ РОЛИКИ" 		=> array (
									"link"=>$ua_path_link.$exua_rss_list_filename."?view=371152",
									"image"=>$ua_images_category_path."ua_reklama.png"),
		"СОЦИАЛЬНАЯ РЕКЛАМА" 	=> array (
									"link"=>$ua_path_link.$exua_rss_list_filename."?view=4313886",
									"image"=>$ua_images_category_path."ua_social.png"),							
		"УРОКИ И ТРЕНИНГИ" 		=> array (
									"link"=>$ua_path_link.$exua_rss_list_filename."?view=28714",
									"image"=>$ua_images_category_path."ua_trennings.png")							
		),
						
	"УКРАИНСКИЙ" => array
	
		("ЗАРУБІЖНЕ КІНО" 	=> array (
									"link"=>$ua_path_link.$exua_rss_list_filename."?view=82470",
									"image"=>$ua_images_category_path."ua_films_eng.png"),
		"НАШЕ КІНО" 			=> array (
									"link"=>$ua_path_link.$exua_rss_list_filename."?view=82473",
									"image"=>$ua_images_category_path."ua_films_rus.png"),
		"СЕРІАЛИ" 				=> array (
									"link"=>$ua_path_link.$exua_rss_list_filename."?view=82480",
									"image"=>$ua_images_category_path."ua_serials_eng.png"),
		"МУЛЬТФІЛЬМИ" 			=> array (
									"link"=>$ua_path_link.$exua_rss_list_filename."?view=82484",
									"image"=>$ua_images_category_path."ua_mult.png"),
		"ДОКУМЕНТАЛЬНІ"			=> array (
									"link"=>$ua_path_link.$exua_rss_list_filename."?view=82476",
									"image"=>$ua_images_category_path."ua_docum.png"),
		"EXTUBE"	 			=> array (
									"link"=>$ua_path_link.$exua_rss_list_filename."?view=82499",
									"image"=>$ua_images_category_path."ua_extube.png"),
		"КЛІПИ" 				=> array (
									"link"=>$ua_path_link.$exua_rss_list_filename."?view=82489",
									"image"=>$ua_images_category_path."ua_clips.png"),
		"КОНЦЕРТИ" 				=> array (
									"link"=>$ua_path_link.$exua_rss_list_filename."?view=82490",
									"image"=>$ua_images_category_path."ua_concerts.png"),
		"ШОУ ТА ПЕРЕДАЧІ" 		=> array (
									"link"=>$ua_path_link.$exua_rss_list_filename."?view=82493",
									"image"=>$ua_images_category_path."ua_show.png"),
		"ТРЕЙЛЕРИ" 				=> array (
									"link"=>$ua_path_link.$exua_rss_list_filename."?view=82483",
									"image"=>$ua_images_category_path."ua_trailers.png"),
		"СПОРТ" 				=> array (
									"link"=>$ua_path_link.$exua_rss_list_filename."?view=82496",
									"image"=>$ua_images_category_path."ua_sport.png"),						
		"АНІМЕ" 				=> array (
									"link"=>$ua_path_link.$exua_rss_list_filename."?view=82488",
									"image"=>$ua_images_category_path."ua_anime.png"),							
		"ТЕАТР" 				=> array (
									"link"=>$ua_path_link.$exua_rss_list_filename."?view=82508",
									"image"=>$ua_images_category_path."ua_theatre.png"),
		"ПРОПОВІДІ" 			=> array (
									"link"=>$ua_path_link.$exua_rss_list_filename."?view=371167",
									"image"=>$ua_images_category_path."ua_religion.png"),
		"РЕКЛАМНІ РОЛИКИ" 		=> array (
									"link"=>$ua_path_link.$exua_rss_list_filename."?view=371168",
									"image"=>$ua_images_category_path."ua_reklama.png"),
		"УРОКИ ТА ТРЕНІНГИ" 		=> array (
									"link"=>$ua_path_link.$exua_rss_list_filename."?view=82495",
									"image"=>$ua_images_category_path."ua_trennings.png")
		),
		
	"ENGLISH" => array
		
		("MOVIES" 				=> array (
									"link"=>$ua_path_link.$exua_rss_list_filename."?view=82316",
									"image"=>$ua_images_category_path."ua_films_eng.png"),
		"SERIES" 				=> array (
									"link"=>$ua_path_link.$exua_rss_list_filename."?view=82325",
									"image"=>$ua_images_category_path."ua_serials_eng.png"),
		"CARTOONS" 				=> array (
									"link"=>$ua_path_link.$exua_rss_list_filename."?view=82329",
									"image"=>$ua_images_category_path."ua_mult.png"),
		"DOCUMENTARIES"			=> array (
									"link"=>$ua_path_link.$exua_rss_list_filename."?view=82318",
									"image"=>$ua_images_category_path."ua_docum.png"),
		"CLIPS" 				=> array (
									"link"=>$ua_path_link.$exua_rss_list_filename."?view=82333",
									"image"=>$ua_images_category_path."ua_clips.png"),
		"LIVE CONCERTS" 		=> array (
									"link"=>$ua_path_link.$exua_rss_list_filename."?view=82335",
									"image"=>$ua_images_category_path."ua_concerts.png"),
		"TRAILERS" 				=> array (
									"link"=>$ua_path_link.$exua_rss_list_filename."?view=82326",
									"image"=>$ua_images_category_path."ua_trailers.png"),
		"SPORT" 				=> array (
									"link"=>$ua_path_link.$exua_rss_list_filename."?view=82348",
									"image"=>$ua_images_category_path."ua_sport.png"),						
		"ANIME" 				=> array (
									"link"=>$ua_path_link.$exua_rss_list_filename."?view=82331",
									"image"=>$ua_images_category_path."ua_anime.png"),							
		"THEATRE & MUSICALS" 	=> array (
									"link"=>$ua_path_link.$exua_rss_list_filename."?view=82354",
									"image"=>$ua_images_category_path."ua_theatre.png"),
		"COMMERCIALS" 			=> array (
									"link"=>$ua_path_link.$exua_rss_list_filename."?view=371172",
									"image"=>$ua_images_category_path."ua_reklama.png"),
		"LESSONS & TRAININGS" 	=> array (
									"link"=>$ua_path_link.$exua_rss_list_filename."?view=82343",
									"image"=>$ua_images_category_path."ua_trennings.png")							
		));	
									
	}
	// функция вставляет бордюры все картинки и текст
	//-------------------------------
	
	public function labels($lang)
	{
		$title = array( 
						"РУССКИЙ"=>"Русский - Выбор категории",
						"УКРАИНСКИЙ"=>"Український - Вибір категорії",
						"ENGLISH"=>"English - category choose"
						);
		return $title[$lang];				
	}
	
	public function search($lang)
	{
		$search = array( 
						"РУССКИЙ"=>"ПОИСК",
						"УКРАИНСКИЙ"=>"ПОШУК",
						"ENGLISH"=>"SEARCH"
						);
		return $search[$lang];				
	}
	
	public function favorites($lang)
	{
		$fav = array( 
						"РУССКИЙ"=>"ИЗБРАННОЕ",
						"УКРАИНСКИЙ"=>"ВИБРАНЕ",
						"ENGLISH"=>"FAVORITES"
						);
		return $fav[$lang];				
	}
	
	public function footer($lang)
	{
		$footer = array( 
						"РУССКИЙ"=>"RETURN - выход",
						"УКРАИНСКИЙ"=>"RETURN - вихід",
						"ENGLISH"=>"RETURN - exit"
						);
		return $footer[$lang];				
	}
	
	
	public function mediaDisplay_content()
	{
	global $ua_images_path;
	?>

		<text  align="<?= static::text_header_align ?>" redraw="<?= static::text_header_redraw ?>" lines="<?= static::text_header_lines ?>" offsetXPC="<?= static::text_header_offsetXPC ?>" offsetYPC="<?= static::text_header_offsetYPC ?>" widthPC="<?= static::text_header_widthPC ?>" heightPC="<?= static::text_header_heightPC ?>" fontSize="<?= static::text_header_fontSize ?>" backgroundColor="<?= static::text_header_backgroundColor ?>" foregroundColor="<?= static::text_header_foregroundColor ?>">
		<?=$this->labels($this->language)?>
		</text>
	
		<image  offsetXPC="<?= static::image_site_footer_display_offsetXPC ?>" offsetYPC="<?= static::image_site_footer_display_offsetYPC ?>" widthPC="<?= static::image_site_footer_display_widthPC ?>" heightPC="<?= static::image_site_footer_display_heightPC ?>">
					<?= $ua_images_path . static::exua_logo ?>
		</image>
	
			<text  align="<?= static::text_footer_align ?>" redraw="<?= static::text_footer_redraw ?>" lines="<?= static::text_footer_lines ?>" offsetXPC="<?= static::text_footer_offsetXPC ?>" offsetYPC="<?= static::text_footer_offsetYPC ?>" widthPC="<?= static::text_footer_widthPC ?>" heightPC="<?= static::text_footer_heightPC ?>" fontSize="<?= static::text_footer_fontSize ?>" backgroundColor="<?= static::text_footer_backgroundColor ?>" foregroundColor="<?= static::text_footer_foregroundColor ?>">
		 <?=$this->footer($this->language)?>
	</text>
	
	<?php	
		
	}
	
	
	//-------------------------------
	public function channel()
	{
	global $ua_path;
	global $exua_rss_list_filename;
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
		<title><?=$this->search($this->language)?></title>
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
					url = "<?= $ua_path_link.$exua_rss_list_filename."?search="?>"+urlEncode(keyword)+"&amp;";
					jumpToLink("searchLink");
				}
			</onClick>
		<image><?=$ua_images_path ?>ua_search.png</image>		
	</item>
	<item>
		<title><?=$this->favorites($this->language)?></title>
			<onClick>
				<script>
					url = "<?=$ua_path_link.$ua_rss_favorites_filename?>";
				</script>
			</onClick>
		<image><?=$ua_images_path ?>ua_favorites.png</image>		
	</item>
<?php
	$categ=$this->categories();
	foreach ($categ[$this->language] as $key=>$cat){
		echo "	<item>\n";
		echo "			<title>".$key."</title>\n";
		foreach ($cat as $key2=>$value){
			echo "			<".$key2.">".$value."</".$key2.">\n";
		}
		echo "	</item>\n";
	}
	
	?>

	</channel>
	<?php
	}

	// тут выбираем, какой ленгвич будет использоваться
	function __construct($lang){
			switch ($lang) {
				case "r":
								$this->language="РУССКИЙ";
								
								break;
				case "u":
								$this->language="УКРАИНСКИЙ";
								break;
				case "e":
								$this->language="ENGLISH";
								break;
			}
	}	

}
//-------------------------------
if(isset($_GET["lang"])){
$view = new ua_rss_cat($_GET["lang"]);
$view->showRss();
}
exit;
?>