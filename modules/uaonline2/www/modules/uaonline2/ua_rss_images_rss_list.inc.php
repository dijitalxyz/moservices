<?php
/*	------------------------------
	Ukraine online services 	
	Images & borders part for 
	rss_list V1.0
	------------------------------
	Created by Sashunya 2011	
	wall9e@gmail.com			
	Some code was used from 
	Farvoice  & others 
	------------------------------ */
?>

	<image offsetXPC="<?= static::item_offsetXPC ?>" offsetYPC="<?= static::item_offsetYPC ?>" widthPC="<?= static::item_widthPC ?>" heightPC="<?= static::item_heightPC ?>" >
      <?= $ua_images_path . static::item_image ?>
    </image>
	
	<image offsetXPC="<?= static::menu_offsetXPC ?>" offsetYPC="<?= static::menu_offsetYPC ?>" widthPC="<?= static::menu_widthPC ?>" heightPC="<?= static::menu_heightPC ?>" >
		<?= $ua_images_path . static::menu_image ?>
    </image>
	
			
	<text  align="<?= static::text_header_align ?>" redraw="<?= static::text_header_redraw ?>" lines="<?= static::text_header_lines ?>" offsetXPC="<?= static::text_header_offsetXPC ?>" offsetYPC="<?= static::text_header_offsetYPC ?>" widthPC="<?= static::text_header_widthPC ?>" heightPC="<?= static::text_header_heightPC ?>" fontSize="<?= static::text_header_fontSize ?>" backgroundColor="<?= static::text_header_backgroundColor ?>" foregroundColor="<?= static::text_header_foregroundColor ?>">
		<script>title;</script> 
	</text>