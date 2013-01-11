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
	function showOnUserInput()
	{
?>
    <onUserInput>
	ret = "false";
	userInput = currentUserInput();

	if (userInput == "<?= getRssCommand('enter') ?>"  || userInput == "<?= getRssCommand('right') ?>")
	{
		idx = getFocusItemIndex();
		url = getItemInfo( idx, "link" );
		act = getItemInfo( idx, "action" );
		if( act == "rss" )
		{
			url = doModalRss(url);
		}
		else if ( act == "search" )
		{
<?php

		if( getYoutubeConfigParameter( 'keyboard' ) == 'rss' )
		{
?>
			str = doModalRss("<?= getMosUrl().'?page=rss_keyboard' ?>");
<?php
		}
		else
		{
?>
			str = getInput("Search", "doModal");
<?php
		}
?>
			if(( str != null )&amp;&amp;( str != "" ))
			{
				url = url + urlEncode(str);
			}
			else url = "";
		}
		setReturnString( url );
		postMessage( "<?= getRssCommand('return') ?>" );
		ret = "true";
	}
	else
	if (userInput == "<?= getRssCommand('left') ?>")
	{
		postMessage( "<?= getRssCommand('return') ?>" );
		ret = "true";
	}
	ret;
    </onUserInput>
<?php
	}
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
