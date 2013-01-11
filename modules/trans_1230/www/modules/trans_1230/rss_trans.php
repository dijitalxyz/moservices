<?php

function rss_trans_content()
{
	include( 'modules/core/rss_view_list.php' );

	class rssSkinTransView extends rssSkinListView
	{
		const itemHeight = 80;

		public $itemImage = '';
		public $itemTitle = '';


		function showMoreItemDisplay()
		{
?>
      <text align="left" offsetXPC="0" offsetYPC="0" widthPC="100" heightPC="40"
       fontSize="14" backgroundColor="-1:-1:-1">
	<foregroundColor>
          <script>
            color;
          </script>
	</foregroundColor>
        <script>
	  getStringArrayAt(nameArray, idx);
	</script>
      </text>

      <text align="left" offsetXPC="1" offsetYPC="45" widthPC="50" heightPC="45"
       fontSize="12" backgroundColor="-1:-1:-1">
	<foregroundColor>
          <script>
            color;
          </script>
	</foregroundColor>
        <script>
	  getStringArrayAt(leftArray, idx);
	</script>
      </text>

      <text align="right" offsetXPC="50" offsetYPC="45" widthPC="46" heightPC="45"
       fontSize="12" backgroundColor="-1:-1:-1">
	<foregroundColor>
          <script>
            color;
          </script>
	</foregroundColor>
        <script>
	  getStringArrayAt(rightArray, idx);
	</script>
      </text>

      <image offsetXPC="1" offsetYPC="43.75" widthPC="98" heightPC="12.5" >
	<script>
	  getStringArrayAt(bgArray, idx);
	</script>
      </image>

      <image offsetXPC="1" offsetYPC="43.75" heightPC="12.5" >
	<widthPC>
	  <script>
	    ratio=getStringArrayAt(ratioArray, idx);
	    98*ratio/100;
	  </script>
	</widthPC>
        <script>
	  getStringArrayAt(barArray, idx);
	</script>
      </image>
<?php
		}
	// ----------------
		function showOnUserInput()
		{
?>
    <onUserInput>
	ret = "true";
	url = null;
	idx = getFocusItemIndex();

	userInput = currentUserInput();

	if (userInput == "<?= getRssCommand('enter') ?>")
	{
		url = getStringArrayAt(urlArray , idx);
	}
	else
	if (userInput == "<?= getRssCommand('left') ?>" || userInput == "<?= getRssCommand('menu') ?>" || userInput == "<?= getRssCommand('rewind') ?>")
	{
	        url = "<?= getMosUrl().'?page=rss_trans_menu' ?>";
	}
	else
	if (userInput == "<?= getRssCommand('play') ?>")
	{
		setRefreshTime(1);
	}
	else
	if (userInput != "<?= getRssCommand('right') ?>")
	{
		ret = "false";
	}

	if( url != null )
	{
		setRefreshTime(-1);
		url = doModalRss(url);
		if( url != null &amp;&amp; url != "" )
		{
			moUrl = url;
		}
		setRefreshTime(1);
		ret = "true";
	}
	ret;
    </onUserInput>
<?php
		}
	// ----------------
		function showScripts()
		{
?>

  <onEnter>
	moUrl = "<?= getMosUrl().'?page=xml_trans' ?>";
	setFocusItemIndex(0);
	setRefreshTime(10);
  </onEnter>

  <onRefresh>
	setRefreshTime(-1);

	savedItem = getFocusItemIndex();

	itemCount = 0;

	dlok = getURL( moUrl );
	if (dlok != null) dlok = readStringFromFile( dlok );
	if (dlok != null)
	{
		c = 0;
		itemCount  = getStringArrayAt(dlok, c); c += 1;

		nameArray  = null;
		leftArray  = null;
		rightArray = null;
		bgArray    = null;
		barArray   = null;
		ratioArray = null;
		urlArray   = null;

		count = 0;
		while( count != itemCount )
		{
			nameArray  = pushBackStringArray( nameArray,  getStringArrayAt(dlok, c)); c += 1;
			leftArray  = pushBackStringArray( leftArray,  getStringArrayAt(dlok, c)); c += 1;
			rightArray = pushBackStringArray( rightArray, getStringArrayAt(dlok, c)); c += 1;
			bgArray    = pushBackStringArray( bgArray,    getStringArrayAt(dlok, c)); c += 1;
			barArray   = pushBackStringArray( barArray,   getStringArrayAt(dlok, c)); c += 1;
			ratioArray = pushBackStringArray( ratioArray, getStringArrayAt(dlok, c)); c += 1;
			urlArray   = pushBackStringArray( urlArray,   getStringArrayAt(dlok, c)); c += 1;

			count += 1;
		}
	}

	if( itemCount == 0 ) postMessage("return");

	if( savedItem &gt; ( itemCount - 1 ))
	{
		setFocusItemIndex( itemCount - 1 );
	}
	else setFocusItemIndex( savedItem );

	redrawDisplay();
	setRefreshTime(5000);
  </onRefresh>

  <onExit>
    setRefreshTime(-1);
  </onExit>
<?php
		}
	// ----------------
		function showChannel()
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

	$view = new rssSkinTransView;

	$view->topTitle = 'Transmission';

	$view->bottomTitle = 
		getRssCommandPrompt('menu')  . getMsg( 'coreRssPromptMenu' )
	.' '.	getRssCommandPrompt('enter') . getMsg( 'coreRssPromptActs' )
	.' '.	getRssCommandPrompt('play')  . getMsg( 'coreRssPromptResume' )
	;

	$view->showRss();
}

?>
