<?php
/*	------------------------------
	Ukraine online services 	
	items part for 
	rss_link V2.0
	------------------------------
	Created by Sashunya 2012	
	wall9e@gmail.com			
	Some code was used from 
	Farvoice  & others 
	------------------------------ */

	global $ua_path_link;
	global $xtreamer;
	global $player_style;
	
	
	?>
	<playLink_OSD>
		<link>
			<script>
				if (pstyle==1)	url = "<?= $ua_path_link ?>ua_player_alt.php?idx="+idx_play+"&amp;param="+param+"&amp;site="+site;
				else
				url = "<?= $ua_path_link ?>ua_player_std.php?link="+urlEncode(act)+"&amp;idx="+idx_play;	
				url;
			</script>
		</link>
	</playLink_OSD>
	
	<item_template>
		<onClick>
			<script>
				idx = getFocusItemIndex();
				act = getStringArrayAt( linkArray , idx )+ " autoReconnect";
				idx_play = (page * 20)-20;
				idx_play -=-idx;
				<?
					include ("ua_rss_history_check.inc.php");
				?>
				 jumpToLink("playLink_OSD");
				
				
			
			</script>	
		</onClick>
	</item_template>