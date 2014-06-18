<?php

$keyboards = array(
	
	'ru' => array(
		'title' => 'Русский',
		'layout' => array(
			'1','й','ф','я',
			'2','ц','ы','ч',
			'3','у','в','с',
			'4','к','а','м',
			'5','е','п','и',
			'6','н','р','т',
			'7','г','о','ь',
			'8','ш','л','б',
			'9','щ','д','ю',
			'0','з','ж','х',
			'ё','э','ъ',' '
		)
	),
	'hu' => array(
		'title' => 'Magyar (ö,ü)',
		'layout' => array (
			'1','q','a','z',
			'2','w','s','x',
			'3','e','d','c',
			'4','r','f','v',
			'5','t','g','b',
			'6','y','h','n',
			'7','u','j','m',
			'8','i','k','í',
			'9','o','l','á',
			'0','p','ú','é',
			'ö','ü','ó',' '
		)
	),
	'hu2' => array(
		'title' => 'Magyar (ő,ű)',
		'layout' => array (
			'1','q','a','z',
			'2','w','s','x',
			'3','e','d','c',
			'4','r','f','v',
			'5','t','g','b',
			'6','y','h','n',
			'7','u','j','m',
			'8','i','k','í',
			'9','o','l','á',
			'0','p','ú','é',
			'ő','ű','ó',' '
		)
	),
	'en' => array(
		'title' => 'English',
		'layout' => array (
			'1','q','a','z',
			'2','w','s','x',
			'3','e','d','c',
			'4','r','f','v',
			'5','t','g','b',
			'6','y','h','n',
			'7','u','j','m',
			'8','i','k',',',
			'9','o','l','.',
			'0','p',':','&',
			'-','@',';',' '
		)
	),
	'ukr' => array(
		'title' => 'Українська',
		'layout' => array (
			'1','й','ф','я',
			'2','ц','і','ч',
			'3','у','в','с',
			'4','к','а','м',
			'5','е','п','и',
			'6','н','р','т',
			'7','г','о','ь',
			'8','ш','л','б',
			'9','щ','д','ю',
			'0','з','ж','х',
			'-','є','ї',' '	
		)
	),
	'el' => array(
		'title' => 'Ελληνικά (α,ε)',
		'layout' => array (
			'1',';','α','ζ',
			'2','ς','σ','χ',
			'3','ε','δ','ψ',
			'4','ρ','φ','ω',
			'5','τ','γ','β',
			'6','υ','η','ν',
			'7','θ','ξ','μ',
			'8','ι','κ',',',
			'9','ο','λ','.',
			'0','π',':','/',
			'-','_','΄',' '	
		)
	),
	'el2' => array(
		'title' => 'Ελληνικά (Α,ά)',
		'layout' => array (
			'1','Μ','Δ','Β',
			'2','Ε','Φ','Ν',
			'3','Ρ','Γ','ώ',
			'4','Τ','Η','ά',
			'5','Υ','Ξ','ό',
			'6','Θ','Κ','έ',
			'7','Ι','Λ','ί',
			'8','Ο','Ζ','ή',
			'9','Π','Χ','ύ',
			'0','Α','Ψ','ϊ',
			'-','Σ','Ω','ϋ'	
		)
	),
	'sym' => array(
		'title' => getMsg('coreKbdSymbols'),
		'layout' => array (
			'1','!','~','`',
			'2','@','"',"'",
			'3','#','|','\\',
			'4','$',';',' ',
			'5','%','<','>',
			'6','^',':',' ',
			'7','&','?','/',
			'8','*','.',',',
			'9','(','{','[',
			'0',')','}',']',
			'-','+','=','_'
		)
	)
);
//
// ====================================
function xml_keyboard_content()
{
global $nav_lang;
global $keyboards;

	$lang = $nav_lang;
	if( isset( $_REQUEST['lang'] )) $l = $_REQUEST['lang'];
	if( isset( $keyboards[ $l ] )) $lang = $l;

	// generate list

	$s = '';

	// title
	$s .= getMsg('coreKbdTitle').' - ' . $keyboards[ $lang ]['title'] . PHP_EOL;

	// number of items
	$s .= count( $keyboards[ $lang ]['layout'] ) . PHP_EOL;

	foreach( $keyboards[ $lang ]['layout'] as $item )
	{
		$s .= $item . PHP_EOL;
	}

	header( "Content-type: text/plain" );
	echo $s;

//	file_put_contents( '/tmp/put.dat', $s );
//	echo "/tmp/put.dat";

}
//
// ====================================
function rss_keyboard_menu_content()
{
global $keyboards;

	include( 'rss_view_popup.php' );

	$view = new rssSkinPopupView;

	$view->topTitle = getMsg('coreKbdSel');

	foreach( $keyboards as $id => $kbd )
	{
		$view->items[] = array(
			'title'	=> $kbd['title'],
			'action'=> 'ret',
			'link'	=> getMosUrl()."?page=xml_keyboard&amp;lang=$id"
		);
	}

	$view->items[] = array(
		'title'	=> getMsg( 'coreCmCancel' ),
		'action'=> 'ret',
		'link'	=> ''
	);

	$view->showRss();
}
//
// ====================================
function rss_keyboard_content()
{
	class rssSkinKeyboardView extends rssSkinHTile
	{
		const itemRowCount	= 4;
		const itemColumnCount	= 11;

		const itemGapX		= 10;
		const itemGapY		= 10;

		const itemWidth		= 86;
		const itemHeight	= 80;

		const itemTextX		= 0;
		const itemTextY		= 0;
		const itemTextWidth	= 86;
		const itemTextHeight	= 80;

		const itemTextFontSize	= 22;
		const itemTextAlign	= 'center';
		const itemTextLines	= 1;

		const itemUnFocusBgColor = '80:80:80';
		const itemFocusBgColor   = '255:255:255';

		const inputY		= 120;
		const inputWidth	= 864;
		const inputHeight	= 60;

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
	else if (userInput == "<?= getRssCommand('menu') ?>" || userInput == "<?= getRssCommand('rewind') ?>")
	{
	        url = "<?= getMosUrl().'?page=rss_keyboard_menu' ?>";
		url = doModalRss(url);
		if(( url != null )&amp;&amp;( url != "" ))
		{
			moUrl = url;
			setRefreshTime(1);
		}
		ret = "true";
	}
	else if (userInput == "<?= getRssCommand('enter') ?>")
	{
		item = getStringArrayAt(nameArray, i);
		inputText += item;
		inputTextArray  = pushBackStringArray(inputTextArray, item);
		inputTextCount -= -1;
		redrawDisplay();
		ret = "true";
	}
	else if (userInput == "<?= getRssCommand('stop') ?>")
	{
		if(inputTextCount &gt; 0)
		{
			inputTextCount -= 1;
			inputTextArray = deleteStringArrayAt(inputTextArray, inputTextCount);
			inputText = "";
			cnt = 0;
			while( cnt &lt;= inputTextCount )
			{
				inputText += getStringArrayAt(inputTextArray, cnt);
				cnt += 1;
			}
			redrawDisplay();
			ret = "true";
		}
	}
	else if (userInput == "<?= getRssCommand('play') ?>")
	{
		setReturnString( inputText );
		postMessage( "<?= getRssCommand('return') ?>" );
		ret = "true";
	}
	ret;
    </onUserInput>
<?php
		}
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
		bgcolor = "<?= static::itemUnFocusBgColor ?>";
		color = "<?= static::unFocusFontColor ?>";
	}
	else
	{
		bgcolor = "<?= static::itemFocusBgColor ?>";
		color = "<?= static::focusFontColor ?>";
	}
      </script>
<?php
			// item title
			$px = round( static::itemTextX / static::itemWidth  * 100, 4);
			$py = round( static::itemTextY / static::itemHeight * 100, 4);
			$pw = round( static::itemTextWidth  / static::itemWidth  * 100, 4);
			$ph = round( static::itemTextHeight / static::itemHeight * 100, 4);

?>
      <text align="<?= static::itemTextAlign ?>" lines="<?= static::itemTextLines ?>" offsetXPC="<?= $px ?>" offsetYPC="<?= $py ?>" widthPC="<?= $pw ?>" heightPC="<?= $ph ?>" 
       fontSize="<?= static::itemTextFontSize ?>">
	<backgroundColor>
          <script>
            bgcolor;
          </script>
	</backgroundColor>
	<foregroundColor>
          <script>
            color;
          </script>
	</foregroundColor>
	<script>
	  getStringArrayAt(nameArray, idx);
	</script>
      </text>
    </itemDisplay>
<?php
		}
		//
		// ------------------------------------
		function showMoreDisplay()
		{
			$px = round( (static::viewAreaWidth - static::inputWidth) / 2 / static::viewAreaWidth * 100, 4);
			$py = round( static::inputY / static::viewAreaHeight * 100, 4);
			$pw = round( static::inputWidth  / static::viewAreaWidth  * 100, 4);
			$ph = round( static::inputHeight / static::viewAreaHeight * 100, 4);

?>
    <text redraw="yes" align="left" lines="1"
     offsetXPC="<?= $px ?>" offsetYPC="<?= $py ?>" widthPC="<?= $pw ?>" heightPC="<?= $ph ?>"
     fontSize="<?= static::itemTextFontSize ?>"
     backgroundColor="<?= static::itemUnFocusBgColor ?>"
     foregroundColor="<?= static::unFocusFontColor ?>">
      <script>
	inputText + "_";
      </script>
    </text>
<?php

		}
		//
		// ------------------------------------
		function showDisplay()
		{

			$sw = static::screenWidth;
			$sh = static::screenHeight;

			$kw = static::skinWidth;
			$kh = static::skinHeight;

			$sx = ($sw - $kw)/2;
			$sy = ($sh - $kh)/2;

			$vx = static::viewAreaX;
			$vy = static::viewAreaY;
			$vw = static::viewAreaWidth;
			$vh = static::viewAreaHeight;

			$th = static::topHeight;
			$bh = static::bottomHeight;

			$iw = static::itemWidth;
			$ih = static::itemHeight;

			$gx = static::itemGapX;
			$gy = static::itemGapY;

			$ph = static::inputHeight;

			$ah = $vh - $th - $bh - $ph;

			$nc = static::itemColumnCount;
			$nr = static::itemRowCount;

			$this->_columnCount = $nc;
			$this->_rowCount = $nr;


?>
  <mediaDisplay name="photoView"

   viewAreaXPC="<?= round(( $sx + $vx )/$sw*100, 4) ?>"
   viewAreaYPC="<?= round(( $sy + $vy )/$sh*100, 4) ?>"
   viewAreaWidthPC="<?= round(( $vw )/$sw*100, 4) ?>"
   viewAreaHeightPC="<?= round(( $vh )/$sh*100, 4) ?>"

   backgroundColor="<?= static::backgroundColor ?>"

   sideTopHeightPC="0"
   sideBottomHeightPC="0"

   sideColorBottom="<?= static::sideColorBottom ?>"
   sideColorTop="<?= static::sideColorTop ?>"

   rowCount="<?= $nr ?>"
   columnCount="<?= $nc ?>"

   itemOffsetXPC="<?= round(($vw - ($iw + $gx) * $nc)/2/$vw*100, 4) ?>"
   itemOffsetYPC="<?= round((($ah - ($ih + $gy) * $nr)/2 + $th + $ph )/$vh*100, 4) ?>"
   itemWidthPC="<?= round($iw/$vw*100, 4) ?>"
   itemHeightPC="<?= round($ih/$vh*100, 4) ?>"

   itemGapXPC="<?= round($gx/$vw*100, 4) ?>"
   itemGapYPC="<?= round($gy/$vh*100, 4) ?>"

   itemBackgroundColor="<?= static::itemBackgroundColor ?>"

   sliding="no"
   rollItems="no"
   drawItemText="no"
   forceFocusOnItem="yes"

   showHeader="no"
   showDefaultInfo="no"

   idleImageXPC="<?= round( static::idleImageX/$vw*100, 4) ?>"
   idleImageYPC="<?= round( static::idleImageY/$vh*100, 4) ?>"
   idleImageWidthPC="<?= round( static::idleImageWidth/$vw*100, 4) ?>"
   idleImageHeightPC="<?= round( static::idleImageHeight/$vh*100, 4) ?>"
  >
<?php
			$this->showIdleBg();
			$this->showTop();
			$this->showBottom();

			$this->showMoreDisplay();

			$this->showItemDisplay();
			$this->showMenuDisplay();
			$this->showOnUserInput();

?>
  </mediaDisplay>
<?php
		}
		//
		// ------------------------------------
		public function showScripts()
		{
?>

  <onEnter>
	moUrl = "<?= getMosUrl().'?page=xml_keyboard' ?>";

	inputText = '';
	inputTextArray = null;
	inputTextCount = 0;

	setRefreshTime(1);
  </onEnter>

  <onRefresh>
	setRefreshTime(-1);
	showIdle();
	itemCount = 0;

	dlok = getURL( moUrl );
	if (dlok != null)
	{
		c = 0;
		pageTitle = getStringArrayAt(dlok, c); c += 1;
		itemCount = getStringArrayAt(dlok, c); c += 1;

		nameArray = null;

		count = 0;
		while( count != itemCount )
		{
			nameArray = pushBackStringArray( nameArray, getStringArrayAt(dlok, c)); c += 1;
			count += 1;
		}
	}
	if( itemCount == 0 ) postMessage( "<?= getRssCommand('return') ?>" );

	setFocusItemIndex( 0 );

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

	$view = new rssSkinKeyboardView;

	$view->topTitle =
'
	<script>
	  pageTitle;
	</script>';

	$view->bottomTitle = 
		getRssCommandPrompt('menu') . getMsg( 'coreRssPromptKbdSel' ).'   '.
		getRssCommandPrompt('stop') . getMsg( 'coreRssPromptKbdDel' ).'   '.
		getRssCommandPrompt('play') . getMsg( 'coreRssPromptKbdOk' );

	$view->showRss();

}

?>