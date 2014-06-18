<?php
//
// ------------------------------------
function rss_radio_top50_content()
{

class rssSkinRadioTop50View extends rssSkinHTile
	{
		const itemWidth		= 400;	//300
		const itemHeight	= 84;	//120
		
		const itemRows		= 6;
		const itemOffsetY	= 8;

		const itemAddFgColor	 = '160:160:160';
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
		public $bottomTitle =
'
	<script>
	  btmTitle;
	</script>';
		//
		// ------------------------------------
	public $fields = array(
		0 => array(			// image
			'type'   => 'image',
			'posX'   => 295,
			'posY'   => 27,
			'width'  => 85,
			'height' => 31,
			'image'  => '
	<script>
	  getStringArrayAt(logoArray, idx);
	</script>'
			),
		1 => array(			// title
			'type'    => 'text',
			'posX'    => 10,
			'posY'    => 28,
			'width'   => 290,
			'height'  => 28,
			'lines'   => 1,
			'fontSize'=> 13,
			'align'   => 'left',
//			'bgColor' => '"100:100:100"',
			'text'    => '
	<script>
	  getStringArrayAt(nameArray, idx);
	</script>'
			),
		2 => array(			// subtitle
			'type'    => 'text',
			'posX'    => 10,
			'posY'    => 8,
			'width'   => 180,
			'height'  => 18,
			'lines'   => 0,
			'fontSize'=> 10,
			'align'   => 'left',
			'fgColor' => 'acolor',
//			'bgColor' => '"100:100:100"',
			'text'    => '
	<script>
	  getStringArrayAt(cityArray, idx);
	</script>'
			),
		3 => array(			// subtitle
			'type'    => 'text',
			'posX'    => 210,
			'posY'    => 8,
			'width'   => 180,
			'height'  => 18,
			'lines'   => 0,
			'fontSize'=> 10,
			'align'   => 'right',
			'fgColor' => 'acolor',
//			'bgColor' => '"100:100:100"',
			'text'    => '
	<script>
	  getStringArrayAt(languageArray, idx);
	</script>'
			),
		4 => array(			// subtitle
			'type'    => 'text',
			'posX'    => 10,
			'posY'    => 60,
			'width'   => 180,
			'height'  => 16,
			'lines'   => 0,
			'fontSize'=> 10,
			'align'   => 'left',
			'fgColor' => 'acolor',
//			'bgColor' => '"100:100:100"',
			'text'    => '
	<script>
	  getStringArrayAt(genreArray, idx);
	</script>'
			),
		5 => array(			// subtitle
			'type'    => 'text',
			'posX'    => 210,
			'posY'    => 60,
			'width'   => 180,
			'height'  => 16,
			'lines'   => 0,
			'fontSize'=> 10,
			'align'   => 'right',
			'fgColor' => 'acolor',
//			'bgColor' => '"100:100:100"',
			'text'    => '
	<script>
	  getStringArrayAt(bitrateArray, idx);
	</script>'
			),
	);
	//
	
	public function showDisplay()
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

		$th = static::topHeight + static::topY + static::itemOffsetY;

		$iw = static::itemWidth;
		$ih = static::itemHeight;

		$nc = floor( $vw / $iw );
		$nr = static::itemRows;

		$this->_columnCount = 3;
		$this->_rowCount = 6;
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

   itemOffsetXPC="<?= round(($vw - $iw * $nc)/2/$vw*100, 4) ?>"
   itemOffsetYPC="<?= round($th/$vh*100, 4) ?>"
   itemWidthPC="<?= round($iw/$vw*100, 4) ?>"
   itemHeightPC="<?= round($ih/$vh*100, 4) ?>"
   
   itemGapXPC="0"
   itemGapYPC="0"

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
  <previewWindow windowColor="0:0:0" offsetXPC="99" widthPC="1" offsetYPC="99" heightPC="1"></previewWindow>
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
	// ------------------------------------
		public function showOnUserInput()
		{
?>
<onUserInput>
<script>
	ret = "false";
	i = getFocusItemIndex();
	userInput = currentUserInput();

	if (userInput == "<?= getRssCommand('up') ?>")
	{
		screen_time = 0;
		if( ( i % <?= $this->_rowCount ?> ) == 0 ) ret = "true";
	}
	else if (userInput == "<?= getRssCommand('down') ?>")
	{
		screen_time = 0;
		if( ( ( i - -1 ) % <?= $this->_rowCount ?> ) == 0 ) ret = "true";
	}
	else if (userInput == "<?= getRssCommand('left') ?>")
	{
		screen_time = 0;
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
		screen_time = 0;
		if( nPage != "" )
		if( i &gt; ( itemCount - <?= $this->_rowCount ?> - 1 ) )
		{
			moUrl = nPage;
			setRefreshTime(1);
			savedItem = i - ( itemCount - <?= $this->_rowCount ?> );
			ret = "true";
		}
	}
	else if (userInput == "<?= getRssCommand('enter') ?>")
	{
		screen_time = 0;
		showIdle();
    	playItemURL(-1, 1);
    	setRefreshTime(-1);
		url = getStringArrayAt(linkArray, i);
		url = "<?= getMosUrl().'?page=xml_radio_top50_link&amp;link=' ?>" + url;
		url = getUrl(url);

		dlok = loadXMLFile(url);
		if (dlok != null)
		{
		stream_url = getXMLText("channel","item","link");
		stream_title = getXMLText("channel","item","genre");
		stream_img = getXMLText("channel","item","logo");
		}
		if ((stream_url != null)&amp;&amp;( stream_url != "Flash-Access" ))
		{		
		playItemURL(stream_url, 0, "mediaDisplay", "previewWindow");
		stream_type = null;
		stream_name = null;
		stream_bitrate = null;
		stream_current_song = null;	
			pageTitle = getStringArrayAt(nameArray, -1);
		
		 setRefreshTime(100);
		 refresh_time_chag = 0;
		 refresh_time = 100; 
		} else {
		doModalRss("<?= getMosUrl().'?page=rss_radio_top50_information' ?>");
		 setRefreshTime(100);
		 refresh_time_chag = 0;
		 refresh_time = 100; 
		}
		redrawDisplay();
		cancelIdle();
		ret = "true";
	}
	else if (userInput == "menu" || userInput == "<?= getRssCommand('rewind') ?>")
	{
	     screen_time = 0;
		 url = "<?= getMosUrl().'?page=rss_radio_top50_menu' ?>";
		url = doModalRss( url );
		if(( url != null )&amp;&amp;( url != "" ))
		{
			moUrl = url;
			refresh_time = 1;
			savedItem = 0;
			setRefreshTime(1);
		}
		ret = "true";
	}
	else if (userInput == "<?= getRssCommand('display') ?>")
	{
		screen_time = 0;
		refresh_time_chag = 0;
		setRefreshTime(1000);
	}
	else if (userInput == "<?= getRssCommand('forward') ?>")
	{
	    screen_time = 0;
			link = null;
			logo = null;
			name = null;
			bitrate = null;
			city = null;
			language = null;
			genre = null;
			link = getStringArrayAt(linkArray, i);
			logo = getStringArrayAt(logoArray, i);
			if (logo == "null") {
			logo = stream_img;
			}			
			name = urlEncode(getStringArrayAt(nameArray, i));
			bitrate = getStringArrayAt(bitrateArray, i);
			city = urlEncode(getStringArrayAt(cityArray, i));
			language = urlEncode(getStringArrayAt(languageArray, i));
			genre = urlEncode(getStringArrayAt(genreArray, i));
			url = "<?= getMosUrl().'?page=rss_add_favorites_menu&amp;link=' ?>" + link 
			+ "&amp;logo=" + logo 
			+ "&amp;name=" + name 
			+ "&amp;bitrate=" + bitrate
			+ "&amp;city=" + city
			+ "&amp;language=" + language
			+ "&amp;genre=" + genre;
		url = doModalRss( url );
		if(( url != null )&amp;&amp;( url != "" ))
		{
			url = getUrl(url);
		}
			refresh_time = 1;
			savedItem = 0;
			setRefreshTime(1);
		ret = "true";
	}
	
	ret;
</script>
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
		acolor = "<?= static::itemAddFgColor ?>"; 
		bgcolor = "<?= static::itemUnFocusBgColor ?>";
	}
	else
	{
		color = "<?= static::focusFontColor ?>";
		acolor = "<?= static::focusFontColor ?>"; 
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

<!-- playing station -->
<?php
		$px = 0;
		$py = round( 583 / static::viewAreaHeight * 100, 4);
		$pw = 100;
		$ph = round( 3 / static::viewAreaHeight * 100, 4);

?>
    <image offsetXPC="<?= $px ?>" offsetYPC="<?= $py ?>" widthPC="<?= $pw ?>" heightPC="<?= $ph ?>">
      <?= getSkinPath().static::topBackground ?> 
    </image>

	 <text offsetXPC="10" offsetYPC="72" widthPC="80" heightPC="12"
       alignt="centr" fontSize="10"  lines="0" redraw="yes">
	<backgroundColor>"0:0:0"</backgroundColor><foregroundColor>"255:255:255"</foregroundColor>	
	<script>
	  stream_title;
	</script>'
     </text>
	 <text offsetXPC="80" offsetYPC="76" widthPC="40" heightPC="12"
       alignt="centr" fontSize="14"  lines="0" redraw="yes">
	<backgroundColor>"0:0:0"</backgroundColor><foregroundColor>"255:255:255"</foregroundColor>	
	<script>
	  play_status_text;
	</script>'
     </text>
	 <text offsetXPC="18" offsetYPC="76" widthPC="57" heightPC="12"
       alignt="centr" fontSize="12"  lines="0" redraw="yes">
	<backgroundColor>"0:0:0"</backgroundColor><foregroundColor>"255:255:255"</foregroundColor>	
	<script>
	  stream_current_song;
	</script>'
     </text>
	 <text offsetXPC="10" offsetYPC="81" widthPC="10" heightPC="12"
       alignt="centr" fontSize="12"  lines="0" redraw="yes">
	<backgroundColor>"0:0:0"</backgroundColor><foregroundColor>"255:255:255"</foregroundColor>	
	<script>stream_bitrate;</script>'
     </text>
	 <text offsetXPC="15" offsetYPC="81" widthPC="20" heightPC="12"
       alignt="centr" fontSize="12"  lines="0" redraw="yes">
	<backgroundColor>"0:0:0"</backgroundColor><foregroundColor>"255:255:255"</foregroundColor>	
	<script>stream_type;</script>'
     </text>
	 <image offsetXPC="10" offsetYPC="80" widthPC="6" heightPC="4"redraw="yes">
	<script>stream_img;	</script>'
     </image>

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
$radio_top50_fc = '/usr/local/etc/mos/www/modules/radio_top50/radio_top50.config.php';
if( is_file( $radio_top50_fc ) )
{
	include( $radio_top50_fc );
}		
?>
  <onEnter>
	moUrl = "<?= getMosUrl().'?page=xml_radio_top50' ?>";
	savedItem = 0;
    playItemURL(-1, 1);
	setRefreshTime(1);
	refresh_time = 1;
	refresh_time_chag = 0;
	screen_time = 0;	
	play_status_temp = 0;
refresh_time_chag_ust = <?= $radio_top50_config['refresh_time_chag'] ?>;
screen_time_ust = <?= $radio_top50_config['screen_time'] ?>;	
  </onEnter>

  <onRefresh>
	setRefreshTime(-1);
	showIdle();
	
	if (refresh_time == 1) {
	
	dlok = getURL( moUrl );
	if (dlok != null &amp;&amp; dlok != "") dlok = readStringFromFile( dlok );
	if (dlok != null &amp;&amp; dlok != "")
	{
		itemCount = 0;
		c = 0;
		pageTitle = getStringArrayAt(dlok, c); c += 1;
		pPage = getStringArrayAt(dlok, c); c += 1;
		btmTitle = getStringArrayAt(dlok, c); c += 1;

		itemCount = getStringArrayAt(dlok, c); c += 1;

		logoArray = null;
		linkArray  = null;
		nameArray  = null;
		bitrateArray = null;
		cityArray = null;
		languageArray = null;
		genreArray = null;
		textArray = null;

		count = 0;
		while( count != itemCount )
		{
			c += 1;
			logoArray  = pushBackStringArray( logoArray,  getStringArrayAt(dlok, c)); c += 1;
			linkArray  = pushBackStringArray( linkArray,  getStringArrayAt(dlok, c)); c += 1;
			nameArray = pushBackStringArray( nameArray, getStringArrayAt(dlok, c)); c += 1;
			bitrateArray  = pushBackStringArray( bitrateArray,  getStringArrayAt(dlok, c)); c += 1;
			cityArray  = pushBackStringArray( cityArray,  getStringArrayAt(dlok, c)); c += 1;
			languageArray  = pushBackStringArray( languageArray,  getStringArrayAt(dlok, c)); c += 1;
			genreArray  = pushBackStringArray( genreArray,  getStringArrayAt(dlok, c)); c += 1;
			textArray  = null; c += 1;

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
	refresh_time = 100; 
	setRefreshTime(100);
	cancelIdle();
	redrawDisplay();	
	} else {
	
	if (refresh_time_chag_ust != 0) {
	stream_progress  = getPlaybackStatus();
	buffer_progress  = getCachedStreamDataSize(0, 262144);
	play_elapsed     = getStringArrayAt(stream_progress, 0);
	play_total       = getStringArrayAt(stream_progress, 1);
	play_status      = getStringArrayAt(stream_progress, 3);
	
	if (play_status == 0) {	play_status_text = "молчу"; }
	if (play_status == 2) {	play_status_text = "играю"; }	
	if (play_status != play_status_temp) {
	play_status_temp = play_status;
	redrawDisplay();
	}



		refresh_time_chag = add(refresh_time_chag,1);
		if ( refresh_time_chag==1 || refresh_time_chag==refresh_time_chag_ust) {
		url_teg = "<?= getMosUrl().'?page=xml_radio_top50_teg&amp;link=' ?>" + stream_url;
		url_teg = getUrl(url_teg);

		dlok = loadXMLFile(url_teg);
		if (dlok != null)
		{
		stream_type = getXMLText("channel","item","stream_tip");
		stream_name = getXMLText("channel","item","icy-name");
		stream_bitrate = getXMLText("channel","item","icy-br");
		stream_current_song = getXMLText("channel","item","song");
		}
		if (refresh_time_chag==refresh_time_chag_ust) { refresh_time_chag=1; }
		
		redrawDisplay();
		}
		
		}
		setRefreshTime(1000);

	if (screen_time_ust != 0) {		
		screen_time =add(screen_time,1);
		if (screen_time == screen_time_ust) {

			if (stream_img == null) {
			screensaver ="<?= getMosUrl().'modules/radio_top50/button.png' ?>";
			} else {
			screensaver = stream_img;
			}

		url = "<?= getMosUrl().'?page=rss_radio_top50_screensaver&amp;url_screensaver=' ?>" + screensaver;
		setRefreshTime(-1);
		doModalRss( url );
		screen_time = 0;
		refresh_time_chag = 0;
		setRefreshTime (1000);
		}		
	 }	
	cancelIdle();
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
	$view = new rssSkinRadioTop50View;

	$view->showRss();
}

?>
