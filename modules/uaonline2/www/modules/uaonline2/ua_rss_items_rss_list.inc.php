<?php
/*	------------------------------
	Ukraine online services 	
	items part for 
	rss_list V1.2
	------------------------------
	Created by Sashunya 2013	
	wall9e@gmail.com			
	Some code was used from 
	Farvoice  & others 
	------------------------------ */?>
	
	<item_template>
		<link>
			<script>
			idx = getFocusItemIndex();
			url = getStringArrayAt( linkArray , idx );
			titl = getStringArrayAt( titleArray , idx );
			if (fsua == 1) writeStringToFile("/tmp/ua_title.tmp", titl);
			url;
			</script>
		</link>
	</item_template>
