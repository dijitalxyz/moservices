<?php

include( 'modules/core/rss_view_left.php' );

class rssYouTubeLeftView extends rssSkinLeftView
{
	const viewAreaWidth	= 440;
	const itemWidth		= 436;
	const itemTextWidth	= 320;

	public $currentItem = 0;
	//
	// ------------------------------------
	function showScripts()
	{
?>
  <onEnter>
	cancelIdle();
	setFocusItemIndex( <?= $this->currentItem ?> );
  </onEnter>
<?php
	}
}

?>
