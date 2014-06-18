<?php
//
// ------------------------------------
function rss_rodina_content()
{
	class rssSkinrodinaView extends rssSkinHTile
	{
		//
		// ------------------------------------

		public $topTitle =
'
	<script>
	  pageTitle;
	</script>
';
		//
		// ------------------------------------

		const itemWidth		= 400;
		const itemHeight	= 78;

		const itemUnFocusBgColor = '0:0:0';

		//
		// ------------------------------------
		public $fields = array(
			0 => array(			// image
				'type'   => 'image',
				'posX'   => 10,
				'posY'   => 10,
				'width'  => 45,
				'height' => 45,
				'image'  => '
	<script>
	  getStringArrayAt(imgArray, idx);
	</script>'
			),
			1 => array(			// title
				'type'    => 'text',
				'posX'    => 70,
				'posY'    => 5,
				'width'   => 330,
				'height'  => 28,
				'lines'   => 0,
				'fontSize'=> 11,
				'align'   => 'left',
//				'bgColor' => '"100:100:100"',
				'text'    => '
	<script>
	  getStringArrayAt(nameArray, idx);
	</script>'
			),
			2 => array(			// epg
				'type'    => 'text',
				'posX'    => 60,
				'posY'    => 28,
				'width'   => 330,
				'height'  => 37,
				'lines'   => 2,
				'fontSize'=> 9,
				'align'   => 'left',
//				'bgColor' => '"100:100:100"',
				'text'    => '
	<script>
	  getStringArrayAt(epgArray, idx);
	</script>'
			),
		);
		//
		// ------------------------------------
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
      <text offsetXPC="0" offsetYPC="0" widthPC="100" heightPC="100" cornerRounding="4">
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

	else if (userInput == "<?= getRssCommand('stop') ?>" )
	{
		setRefreshTime(1);
		ret = "true";
	}
	else if (userInput == "<?= getRssCommand('menu') ?>" || userInput == "<?= getRssCommand('rewind') ?>")
	{
	        url = "<?= getMosUrl().'?page=rss_rodina_menu' ?>";
		url = doModalRss(url);
		if(( url != null )&amp;&amp;( url != "" ))
		{
			moUrl = url;
			savedItem = 0;
			setRefreshTime(1);
		}
		ret = "true";
	}
	else if (userInput == "<?= getRssCommand('enter') ?>" || userInput == "<?= getRssCommand('play') ?>")
	{
		showIdle();
		s = getUrl( "<?= getMosUrl().'?page=get_rodina_token' ?>" );
		if( s != "fail" )
		{
			s = doModalRss( "<?= getMosUrl().'?page=rss_rodina_player' ?>" + "&amp;cid=" + i );
			if(( s != null )&amp;&amp;( s != "" )) savedItem = s;
		}
		cancelIdle();
		setRefreshTime(1);
		ret = "true";
	}
	ret;
    </onUserInput>
<?php
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
	moUrl = "<?= getMosUrl().'?page=xml_rodina' ?>";

	savedItem = 0;
	itemCount = 0;

	pageTitle = "rodina.TV";

	setRefreshTime(100);
  </onEnter>

  <onRefresh>
	setRefreshTime(-1);
	showIdle();

	respond = "list";

	dlok = getURL( moUrl );
	if (dlok != null) dlok = readStringFromFile( dlok );
	if (dlok != null)
	{
		c = 0;
		respond = getStringArrayAt(dlok, c); c += 1;

		if( respond == "rss" )
		{
			url = getStringArrayAt(dlok, c); c += 1;
			url = doModalRss( url );
			if(( url != null )&amp;&amp;( url != "" ))
			{
				moUrl = url;
			}
			else respond = "";
		}
		else
		{
			pageTitle = respond;
			itemCount = getStringArrayAt(dlok, c); c += 1;

			nameArray = null;
			imgArray  = null;
			urlArray  = null;
			epgArray  = null;

			count = 0;
			while( count != itemCount )
			{
				nameArray = pushBackStringArray( nameArray, getStringArrayAt(dlok, c)); c += 1;
				urlArray  = pushBackStringArray( urlArray,  getStringArrayAt(dlok, c)); c += 1;
				imgArray  = pushBackStringArray( imgArray,  getStringArrayAt(dlok, c)); c += 1;
				epgArray  = pushBackStringArray( epgArray,  getStringArrayAt(dlok, c)); c += 1;

				count += 1;
			}
		}
	}
	cancelIdle();

	if( respond == "rss" )
	{
		setRefreshTime(100);
	}
	else if( respond != "" )
	{
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
		redrawDisplay();
	}
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
	$view = new rssSkinrodinaView;

	$view->bottomTitle = 
	 getRssCommandPrompt('rewind') . getMsg( 'coreRssPromptMenu' )
	 .' '.getRssCommandPrompt('enter') . getMsg( 'coreRssPromptPlay' )
	 .' '.getRssCommandPrompt('stop') . getMsg( 'rodinaUpdate' )
	;

	$view->showRss();
}

?>
