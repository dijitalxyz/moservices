<?php

include( 'load_config.inc.php' );
//
// ------------------------------------
function rss_rodina_login_content()
{
global $rodina_config;
global $rodina_session;

	include( 'rodina_view.php' );

	class rssLoginView extends rssSkinRodinaView
	{
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
		bcolor = "<?= static::itemBackgroundColor ?>";
		fcolor = "<?= static::unFocusFontColor ?>";
	}
	else
	{
		bcolor = "<?= static::itemFocusBgColor ?>";
		fcolor = "<?= static::focusFontColor ?>";
	}
      </script>
<?php
			$px = round( static::itemTextX / static::itemWidth  * 100, 4);
			$py = round( static::itemTextY / static::itemHeight * 100, 4);
			$pw = round( static::itemTextWidth  / static::itemWidth  * 100, 4);
			$ph = round( static::itemTextHeight / static::itemHeight * 100, 4);

?>
      <text offsetXPC="<?= $px ?>" offsetYPC="<?= $py ?>" widthPC="<?= $pw ?>" heightPC="<?= $ph ?>" 
       align="<?= static::itemTextAlign ?>" lines="<?= static::itemTextLines ?>" fontSize="<?= static::itemTextFontSize ?>" >
	<backgroundColor><script>bcolor;</script></backgroundColor>
	<foregroundColor><script>fcolor;</script></foregroundColor>
	<script>
	if( idx == 0 ) login;
	else if( idx == 1 )
	{
		if( passwd == "" ) "";
		else "****";
	}
	else if( idx == 2 ) "<?= getMsg( 'rodinaEntry' ) ?>";
	else if( idx == 3 ) "<?= getMsg( 'rodinaDemo' ) ?>";
	else null;
	</script>
      </text>
    </itemDisplay>
<?php
		}
	//
	// ------------------------------------
	function showOnUserInput()
	{
?>
    <onUserInput>
	ret = "false";
	input = currentUserInput();
	if (input == "<?= getRssCommand('enter') ?>"  || input == "<?= getRssCommand('right') ?>")
	{
		idx = getFocusItemIndex();
		if( idx == 0 )
		{
			str = getInput("<?= getMsg( 'rodinaLogin' ) ?>");
			if(( str != null )&amp;&amp;( str != "" ))
			{
				login = str;
				redrawDisplay();
			}
		}
		else if( idx == 1 )
		{
			str = getInput("<?= getMsg( 'rodinaPasswd' ) ?>");
			if(( str != null )&amp;&amp;( str != "" ))
			{
				passwd = str;
				redrawDisplay();
			}
		}
		else if( idx == 2 )
		{
			url = "<?= getMosUrl().'?page=xml_rodina' ?>"
			 + "&amp;login=" + urlEncode( login )
			 + "&amp;passwd=" + urlEncode( passwd );

			setReturnString( url );
			postMessage( "<?= getRssCommand('return') ?>" );
		}
		else if( idx == 3 )
		{
			url = doModalRSS( "<?= getMosUrl().'?page=rss_rodina_demo' ?>" );
			if(( url != null )&amp;&amp;( url != "" ))
			{
				setReturnString( url );
				postMessage( "<?= getRssCommand('return') ?>" );
			}
		}
		ret = "true";
	}
	else if (input == "<?= getRssCommand('left') ?>" )
	{
		ret = "true";
	}
	ret;
    </onUserInput>
<?php
		}
		//
		// ------------------------------------

		public $login;
		public $passwd;

		//
		// ------------------------------------
		public function showScripts()
		{
?>
  <onEnter>
<?php
	if( getMosOption('sdk_version') > 3 )
	{

?>
	setParentFade(96);
<?php
	}

?>

	login  = "<?= $this->login ?>";
	passwd = "<?= $this->passwd ?>";
  </onEnter>
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
	4;
      </script>
    </itemSize>
  </channel>
<?php
		}
	}

	$view = new rssLoginView;

	$view->topTitle = $rodina_session['message'];

	$view->login  = $rodina_config['login'];
	$view->passwd = $rodina_config['passwd'];

	$view->infos[] = array(
		'type' => 'text',
		'posX' => 50,
		'posY' => 50,
		'width'  => 150,
		'height' => 40,
		'text' => getMsg( 'rodinaLogin' )
	);

	$view->infos[] = array(
		'type' => 'text',
		'posX' => 50,
		'posY' => 90,
		'width'  => 150,
		'height' => 40,
		'text' => getMsg( 'rodinaPasswd' )
	);

	$view->items = array(0,1,2,3);

	$view->showRss();
}

?>
