<?php

include( 'modules/core/rss_view_left.php' );

class rssRadio_top50LeftView extends rssSkinLeftView
{
//	const viewAreaWidth	= 440;
	const viewAreaHeight	= 606; // 600;
//	const itemWidth		= 436;
//	const itemHeight	= 40;

//	const itemTextWidth	= 320;

	public $currentItem = 0;

	//
	// ------------------------------------
	public function showMoreItemDisplay()
	{
		// top border
		$px = 0;
		$py = 0;
		$pw = 100;
		$ph = round( 3 / static::itemHeight * 100, 4);
?>
      <image offsetXPC="<?= $px ?>" offsetYPC="<?= $py ?>" widthPC="<?= $pw ?>" heightPC="<?= $ph ?>" ><?= $this->itemImage ?>
        <script>
	if( getItemInfo( idx, "border" ) == "top" ) "<?= getSkinPath().static::imageFocus ?>";
	else null;
        </script>
      </image>
<?php
		// bottom border
		$px = 0;
		$py = round(( static::itemHeight - 3 ) / static::itemHeight * 100, 4);
		$pw = 100;
		$ph = round( 3 / static::itemHeight * 100, 4);
?>
      <image offsetXPC="<?= $px ?>" offsetYPC="<?= $py ?>" widthPC="<?= $pw ?>" heightPC="<?= $ph ?>" ><?= $this->itemImage ?>
        <script>
	if( getItemInfo( idx, "border" ) == "bottom" ) "<?= getSkinPath().static::imageFocus ?>";
	else null;
        </script>
      </image>
<?php

	}
	//
	// ------------------------------------
	function showOnUserInput()
	{
?>
    <onUserInput>
	ret = "true";
	userInput = currentUserInput();

	idx = getFocusItemIndex();
	open = getItemInfo( idx, "open" );

	if (userInput == "<?= getRssCommand('enter') ?>"  || userInput == "<?= getRssCommand('right') ?>")
	{
		url = getItemInfo( idx, "link" );
		act = getItemInfo( idx, "action" );

		if( act == "rss" )
		{
			url = doModalRss(url);
		}
		else if ( act == "search" )
		{

			str = doModalRss("<?= getMosUrl().'?page=rss_keyboard' ?>");


			if(( str != null )&amp;&amp;( str != "" ))
			{
				url = url + urlEncode(str);
			}
			else url = "";
		}
		setReturnString( url );
		postMessage( "<?= getRssCommand('return') ?>" );
	}
	else if (userInput == "<?= getRssCommand('left') ?>")
	{
		postMessage( "<?= getRssCommand('return') ?>" );
	}
	else if (userInput == "<?= getRssCommand('return') ?>" || userInput == "<?= getRssCommand('up') ?>" || userInput == "<?= getRssCommand('down') ?>")
	{
		ret = "false";
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
