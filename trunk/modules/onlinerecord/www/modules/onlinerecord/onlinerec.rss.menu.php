<?php

include( 'modules/core/rss_view_left.php' );

class rssOnlineRecLeftView extends rssSkinLeftView
{
	public $currentItem = 0;
	//
	// ------------------------------------
	function showScripts()
	{
?>
  <onEnter>
	cancelIdle();
<?php

	if( $this->position == 0 )
	if( getMosOption('sdk_version') > 3 )
	{

?>
	setParentFade(96);
<?php
	}

?>
	setFocusItemIndex( <?= $this->currentItem ?> );
  </onEnter>
<?php
	}
}

?>
