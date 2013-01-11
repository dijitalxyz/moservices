<?php
/*	------------------------------
	Ukraine online services 	
	itemdisplay part for 
	rss_link V1.2
	------------------------------
	Created by Sashunya 2012	
	wall9e@gmail.com			
	Some code was used from 
	Farvoice  & others 
	------------------------------ */

	// функция выводит список фильмов
	//-------------------------------
?>
		<text offsetXPC="<?= static::itemdisplay_offsetXPC ?>" offsetYPC="<?= static::itemdisplay_offsetYPC ?>" widthPC="<?= static::itemdisplay_widthPC ?>" heightPC="<?= static::itemdisplay_heightPC ?>" fontSize="<?= static::itemdisplay_fontSize ?>" lines="<?= static::itemdisplay_lines ?>">
		<foregroundColor>
			<script>
				if (histCount !=0 ){
					cnt = 0;
					titleArr = getStringArrayAt(downameArray,idx); 
					while( cnt != histCount )
					{
						histFiles=getStringArrayAt(historyFilesArray, cnt);
						if (histFiles == titleArr) 
						{
							color = "159:255:143";
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