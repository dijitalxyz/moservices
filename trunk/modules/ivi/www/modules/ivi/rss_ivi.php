<?php
//
// ------------------------------------
function rss_ivi_content()
{
	class rssSkinIviView extends rssSkinHTile
	{
		const itemWidth		= 300;
		const itemHeight	= 120;

		const itemImageX	= 10;
		const itemImageY	= 10;
		const itemImageWidth	= 70;
		const itemImageHeight	= 100;

		const itemTextX		= 85;
		const itemTextY		= 10;
		const itemTextWidth	= 210;
		const itemTextHeight	= 80;

		const itemTextLines	= 3;

		const itemUnFocusBgColor = '0:0:0';
		//
		// ------------------------------------
		public $topTitle =
'
	<script>
	  pageTitle;
	</script>';
		//
		// ------------------------------------
	public $fields = array(
		0 => array(			// image
			'type'   => 'image',
			'posX'   => 10,
			'posY'   => 10,
			'width'  => 70,
			'height' => 100,
			'image'  => '
	<script>
	  getStringArrayAt(imgArray, idx);
	</script>'
			),
		1 => array(			// title
			'type'    => 'text',
			'posX'    => 85,
			'posY'    => 10,
			'width'   => 210,
			'height'  => 80,
			'lines'   => 3,
			'fontSize'=> 14,
			'align'   => 'left',
//			'bgColor' => '"100:100:100"',
			'text'    => '
	<script>
	  getStringArrayAt(nameArray, idx);
	</script>'
			),
		2 => array(			// subtitle
			'type'    => 'text',
			'posX'    => 85,
			'posY'    => 64,
			'width'   => 210,
			'height'  => 80,
			'lines'   => 0,
			'fontSize'=> 12,
			'align'   => 'left',
//			'bgColor' => '"100:100:100"',
			'text'    => '
	<script>
	  getStringArrayAt(subArray, idx);
	</script>'
			),
	);
	//
		// ------------------------------------
		public function showOnUserInput()
		{
?>
    <onUserInput>
	ret = "false";
	i = getFocusItemIndex();
	userInput = currentUserInput();

	if (userInput == "<?= getRssCommand('up') ?>")
	{
		if( ( i % <?= $this->_rowCount ?> ) == 0 ) ret = "true";
	}
	else if (userInput == "<?= getRssCommand('down') ?>")
	{
		if( ( ( i - -1 ) % <?= $this->_rowCount ?> ) == 0 ) ret = "true";
	}
	else if (userInput == "<?= getRssCommand('left') ?>")
	{
		if( pPage != "" )
		if( i &lt; <?= $this->_rowCount ?> )
		{
			moUrl = pPage;
			setRefreshTime(1);
			savedItem = i - ( <?= $this->_rowCount ?> - itemCount );
			ret = "true";
		}
	}
	else if (userInput == "<?= getRssCommand('right') ?>")
	{
		if( nPage != "" )
		if( i &gt; ( itemCount - <?= $this->_rowCount ?> - 1 ) )
		{
			moUrl = nPage;
			setRefreshTime(1);
			savedItem = i - ( itemCount - <?= $this->_rowCount ?> );
			ret = "true";
		}
	}
	else if (userInput == "<?= getRssCommand('stop') ?>")
	{
		showIdle();
		url = getStringArrayAt(urlArray, i);
		url = "<?= getMosUrl().'?page=get_ivi_icon_player' ?>"
		 + "&amp;url=" + url;
		url = getUrl(url);
		if(( url != null )&amp;&amp;( url != "" ))
		{
			doModalRss( url );
		}
		cancelIdle();
		ret = "true";
	}
	else if (userInput == "<?= getRssCommand('play') ?>")
	{
		showIdle();
		url = getStringArrayAt(urlArray, i);
/*
		url = "<?= getMosUrl().'?page=rss_player&amp;mod=ivi' ?>"
		 + "&amp;url=" + url;
		doModalRss( url );
*/
		url = "<?= getMosUrl().'?page=get_ivi' ?>"
		 + "&amp;url=" + url;
		url = getUrl(url);
		if(( url != null )&amp;&amp;( url != "" ))
		{
			playItemURL( url, 0 );
		}
		cancelIdle();
		ret = "true";
	}
	else if (userInput == "<?= getRssCommand('enter') ?>")
	{
		showIdle();
		url = getStringArrayAt(urlArray, i);
		url = "<?= getMosUrl().'?page=rss_ivi_info' ?>"
		 + "&amp;url=" + url;
		doModalRss( url );
		cancelIdle();
		ret = "true";
	}
	else if (userInput == "menu" || userInput == "<?= getRssCommand('rewind') ?>")
	{
	        url = "<?= getMosUrl().'?page=rss_ivi_menu' ?>";
		url = doModalRss( url );
		if(( url != null )&amp;&amp;( url != "" ))
		{
			moUrl = url;
			savedItem = 0;
			setRefreshTime(1);
		}
		ret = "true";
	}
	ret;
    </onUserInput>
<?php
		}
		//
	public function showItemDisplay()
	{

?>
    <itemDisplay>
      <script>
	idx = getQueryItemIndex();
	drawState = getDrawingItemState();
	if (drawState == "unfocus")
	{
		color = "<?= static::unFocusFontColor ?>";
		bgcolor = "<?= static::itemUnFocusBgColor ?>";
	}
	else
	{
		color = "<?= static::focusFontColor ?>";
		bgcolor = "<?= static::itemFocusBgColor ?>";
	}
      </script>
      <text offsetXPC="0" offsetYPC="0" widthPC="100" heightPC="100" cornerRounding="8">
	<backgroundColor>
          <script>
            bgcolor;
          </script>
	</backgroundColor>
      </text>
<?php
		foreach( $this->fields as $info )
		{
			$px = round( $info['posX'] / static::itemWidth * 100, 4);
			$py = round( $info['posY'] / static::itemHeight * 100, 4);
			$pw = round( $info['width']  / static::itemWidth  * 100, 4);
			$ph = round( $info['height'] / static::itemHeight * 100, 4);

			if( $info['type'] == 'image' )
			{

?>
      <image offsetXPC="<?= $px ?>" offsetYPC="<?= $py ?>" widthPC="<?= $pw ?>" heightPC="<?= $ph ?>" ><?= $info['image'] ?> 
      </image>
<?php
			}
			else
			{
				$pa = isset( $info['align'] )    ? $info['align']     : static::itemTextAlign;
				$ps = isset( $info['fontSize'] ) ? $info['fontSize']  : static::itemTextFontSize;
				$pb = isset( $info['bgColor'] )  ? $info['bgColor']   : '"'. static::itemTextBackgroundColor .'"';
				$pf = isset( $info['fgColor'] )  ? $info['fgColor']   : 'color';

				$pl = isset( $info['lines'] ) ? $info['lines'] : static::itemTextLines;
				$pl = ( $pl > 0 ) ? ' lines="'. $pl .'"' : '';

?>
      <text offsetXPC="<?= $px ?>" offsetYPC="<?= $py ?>" widthPC="<?= $pw ?>" heightPC="<?= $ph ?>"
       align="<?= $pa ?>"<?= $pl ?> fontSize="<?= $ps ?>">
	<backgroundColor>
          <script>
            <?= $pb ?>;
          </script>
	</backgroundColor>
	<foregroundColor>
          <script>
            <?= $pf ?>;
          </script>
	</foregroundColor><?= $info['text'] ?> 
      </text>
<?php
			}
		}

?>
    </itemDisplay>
<?
	}
	//
	// ------------------------------------
		function showMoreDisplay()
		{
?>
    <text redraw="yes" align="center" lines="1" fontSize="22"
     offsetXPC="18.2353" offsetYPC="15.625" widthPC="63.5294" heightPC="7.8125"
     backgroundColor="-1:-1:-1"
     foregroundColor="<?= static::unFocusFontColor ?>">
      <script>
	msgText;
      </script>
    </text>
<?php

		}
		//
		// ------------------------------------
		public function showScripts()
		{
?>
  <onEnter>
	moUrl = "<?= getMosUrl().'?page=xml_ivi' ?>";
	savedItem = 0;
	setRefreshTime(1);
  </onEnter>

  <onRefresh>
	setRefreshTime(-1);
	showIdle();
	itemCount = 0;

	dlok = getURL( moUrl );
	if (dlok != null) dlok = readStringFromFile( dlok );
	if (dlok != null)
	{
		c = 0;
		pageTitle = getStringArrayAt(dlok, c); c += 1;

		pPage = getStringArrayAt(dlok, c); c += 1;
		nPage = getStringArrayAt(dlok, c); c += 1;

		itemCount = getStringArrayAt(dlok, c); c += 1;

		nameArray = null;
		imgArray  = null;
		urlArray  = null;
		subArray = null;

		count = 0;
		while( count != itemCount )
		{
			nameArray = pushBackStringArray( nameArray, getStringArrayAt(dlok, c)); c += 1;
			imgArray  = pushBackStringArray( imgArray,  getStringArrayAt(dlok, c)); c += 1;
			urlArray  = pushBackStringArray( urlArray,  getStringArrayAt(dlok, c)); c += 1;
			subArray  = pushBackStringArray( subArray,  getStringArrayAt(dlok, c)); c += 1;

			count += 1;
		}
	}
	msgText = "";
	if( itemCount == 0 )
	{
		msgText = "<?= getMsg('coreRssPromptNothing') ?>";
		setFocusItemIndex( 0 );
	}
	else
	{
		if( savedItem &gt; ( itemCount - 1 ))
		{
			setFocusItemIndex( itemCount - 1 );
		}
		else setFocusItemIndex( savedItem );
	}

	cancelIdle();
	redrawDisplay();
  </onRefresh>

  <onExit>
	setRefreshTime(-1);
  </onExit>
<?php
		}
		//
		// ------------------------------------
		public function showChannel()
		{
?>
  <channel>
    <itemSize>
      <script>
	itemCount;
      </script>
    </itemSize>
  </channel>
<?php
		}
	}
	$view = new rssSkinIviView;

	$view->bottomTitle = 
		getRssCommandPrompt('rewind')    . ' меню '
		. getRssCommandPrompt('play')  . ' смотреть '
		. getRssCommandPrompt('enter') . ' информация '
	;

	$view->showRss();
}

?>
