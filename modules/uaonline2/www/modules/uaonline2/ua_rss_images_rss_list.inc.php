<?php
/*	------------------------------
	Ukraine online services 	
	Images & borders part for 
	rss_list V1.1
	------------------------------
	Created by Sashunya 2014	
	wall9e@gmail.com			
	Some code was used from 
	Farvoice  & others 
	------------------------------ */
	global $ua_images_foldername;
	global $ua_path_link;
?>

	<text  align="<?= static::text_header_align ?>" redraw="<?= static::text_header_redraw ?>" lines="<?= static::text_header_lines ?>" offsetXPC="<?= static::text_header_offsetXPC ?>" offsetYPC="<?= static::text_header_offsetYPC ?>" widthPC="<?= static::text_header_widthPC ?>" heightPC="<?= static::text_header_heightPC ?>" fontSize="<?= static::text_header_fontSize ?>" backgroundColor="<?= static::text_header_backgroundColor ?>" foregroundColor="<?= static::text_header_foregroundColor ?>">
		<script>title;</script> 
	</text>
	<scrollbar offsetXPC=93 offsetYPC=13 widthPC=2.26 heightPC=75.0 backgroundImage="<?=$ua_path_link.$ua_images_foldername?>ua_scroll_bar_01.png" foregroundImage="<?=$ua_path_link.$ua_images_foldername?>ua_scroll_bar_02.png" border=7 offset=0 direction="vertical" redraw="yes">
		<totalSize>
			<script>
				getPageInfo("itemCount");
			</script>
		</totalSize>
		<startIndex>
			<script>
				getFocusItemIndex();
			</script>
		</startIndex>
	</scrollbar>