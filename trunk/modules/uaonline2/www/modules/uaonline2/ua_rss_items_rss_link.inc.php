<?php
/*	------------------------------
	Ukraine online services 	
	items part for 
	rss_link V1.1
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
				url = "<?= $ua_path_link ?>ua_player.php?idx="+idx_play;
			</script>
		</link>
	</playLink_OSD>
		
	
	<item_template>
		<onClick>
			<script>
				idx = getFocusItemIndex();
				act = getStringArrayAt( linkArray , idx );
				print("act==============",act);
				idx_play = (page * 20)-20;
				idx_play -=-idx;
				if (pstyle==1) jumpToLink("playLink_OSD");
				else
				{
				<?php
				if ($xtreamer)
					{
					?>
						SwitchViewer(7);
					<?php
					}
					?>
				playItemURL(act, 0);
				}
			</script>	
		</onClick>
	</item_template>