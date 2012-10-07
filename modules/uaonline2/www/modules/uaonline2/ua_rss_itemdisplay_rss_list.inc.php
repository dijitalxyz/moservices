<?php
/*	------------------------------
	Ukraine online services 	
	itemdisplay part for 
	rss_list V1.0
	------------------------------
	Created by Sashunya 2011	
	wall9e@gmail.com			
	Some code was used from 
	Farvoice  & others 
	------------------------------ */?>

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
	    <script>getStringArrayAt(titleArray,idx);</script>
	</text>