<?php

$sovok_config = array(
	'login'  => '',
	'passwd' => '',
);

if( is_file( $mos .'/www/modules/sovok/sovok.config.php' ) )
{
	include( $mos .'/www/modules/sovok/sovok.config.php' );
}

//
// ------------------------------------
function rss_sovok_login_content()
{
global $sovok_config;

	include( 'sovok_view.php' );

	class rssLoginView extends rssSkinSovokView
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
	else "<?= getMsg( 'sovokEntry' ) ?>";
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
			str = getInput("<?= getMsg( 'sovokLogin' ) ?>");
			if(( str != null )&amp;&amp;( str != "" ))
			{
				login = str;
				redrawDisplay();
			}
		}
		else if( idx == 1 )
		{
			str = getInput("<?= getMsg( 'sovokPasswd' ) ?>");
			if(( str != null )&amp;&amp;( str != "" ))
			{
				passwd = str;
				redrawDisplay();
			}
		}
		else if( idx == 2 )
		{
			url = "<?= getMosUrl().'?page=xml_sovok' ?>"
			 + "&amp;login=" + urlEncode( login )
			 + "&amp;passwd=" + urlEncode( passwd );

			setReturnString( url );
			postMessage( "<?= getRssCommand('return') ?>" );
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
	3;
      </script>
    </itemSize>
  </channel>
<?php
		}
	}

	$view = new rssLoginView;

	$view->topTitle = getMsg( 'sovokEntryOn' ) .'Sovok.TV';

	$view->login  = $sovok_config['login'];
	$view->passwd = $sovok_config['passwd'];

	$view->infos[] = array(
		'type' => 'text',
		'posX' => 50,
		'posY' => 50,
		'width'  => 150,
		'height' => 40,
		'text' => getMsg( 'sovokLogin' )
	);

	$view->infos[] = array(
		'type' => 'text',
		'posX' => 50,
		'posY' => 90,
		'width'  => 150,
		'height' => 40,
		'text' => getMsg( 'sovokPasswd' )
	);

	$view->items = array(0,1,2);

	$view->showRss();
}

?>
