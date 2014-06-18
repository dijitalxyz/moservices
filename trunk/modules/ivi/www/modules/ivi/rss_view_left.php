<?php

include( 'modules/core/rss_view_left.php' );

class rssIviLeftView extends rssSkinLeftView
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

		if( getIviConfigParameter( 'keyboard' ) == 'rss' )
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
