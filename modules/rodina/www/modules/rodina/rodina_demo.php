<?php

include( 'load_config.inc.php' );
//
// ------------------------------------
function rss_rodina_demo_content()
{
global $rodina_config;
global $rodina_session;

	include( 'rodina_view.php' );

	class rssDemoView extends rssSkinRodinaView
	{

	// items
		const itemOffsetX	= 15;
		const itemOffsetY	= 15;

		const itemX		= 150; //200
		const itemY		= 80;

		const itemHeight	= 40;
		const itemWidth		= 280;

		const itemTextX		= 20;
		const itemTextY		= 0;
		const itemTextWidth	= 260;
		const itemTextHeight	= 40;

		public $topTitle = '
      <script> title; </script>';

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
	if(      idx == 0 ) code;
	else if( idx == 1 ) "<?= getMsg( 'rodinaTestGen' ) ?>";
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
			str = getInput("<?= getMsg( 'rodinaTestCode' ) ?>");
			if(( str != null )&amp;&amp;( str != "" ))
			{
				code = str;
				redrawDisplay();
			}
		}

		else if( idx == 1 )
		{
			moUrl = "<?= getMosUrl().'?page=xml_rodina_demo' ?>&amp;captcha=" + code + "&amp;token=" + token;
			setRefreshTime(10);
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
		public function showScripts()
		{
?>
  <onEnter>
	moUrl = "<?= getMosUrl().'?page=xml_rodina_demo' ?>";

	title = "";
	captcha = null;
	code  = "";
	token = "";

	setRefreshTime(100);
  </onEnter>

  <onRefresh>
	setRefreshTime(-1);
	showIdle();

	dlok = getURL( moUrl );
	if (dlok != null) dlok = readStringFromFile( dlok );
	if (dlok != null)
	{
		respond = getStringArrayAt(dlok, 0);
		if( respond == "ok" )
		{
			setReturnString( getStringArrayAt(dlok, 1) );
			postMessage( "<?= getRssCommand('return') ?>" );
		}
		else
		{
			title = respond;
			token   = getStringArrayAt(dlok, 1);
			captcha = getStringArrayAt(dlok, 2);
			code  = "";
			redrawDisplay();
		}
	}
	cancelIdle();
  </onRefresh>
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
	2;
      </script>
    </itemSize>
  </channel>
<?php
		}
	}

	$view = new rssDemoView;

	$view->infos[] = array(
		'type' => 'image',
		'posX' => 165,
		'posY' => 46,
		'width'  => 180,
		'height' => 80,
		'image' => '<script> captcha; </script>'
// '/tmp/www/capcha.png'
	);

	$view->infos[] = array(
		'type' => 'text',
		'posX' => 50,
		'posY' => 132,
		'width'  => 50,
		'height' => 40,
		'align' => 'right',
		'text' => getMsg( 'rodinaTestCode' )
	);

	$view->items = array(0,1);

	$view->showRss();
}

?>
