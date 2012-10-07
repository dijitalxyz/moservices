<?php

function rss_sskin_content()
{
	class rssSkinSelectView extends rssSkinVTile
	{
		public $itemImage =
'
	<script>
	  getStringArrayAt(imgArray, idx);
	</script>
';
		// ----------------
		public $itemTitle =
'
	<script>
	  getStringArrayAt(nameArray, idx);
	</script>
';
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
	        url = "<?= getMosUrl().'?page=rss_sskin_menu' ?>";
		url = doModalRss(url);
		if(( url != null )&amp;&amp;( url != "" ))
		{
			moUrl = url;
			savedItem = i;
			setRefreshTime(1);
		}
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
	moUrl = "<?= getMosUrl().'?page=xml_sskin' ?>";
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
		freeMem = getStringArrayAt(dlok, c); c += 1;
		itemCount = getStringArrayAt(dlok, c); c += 1;

		nameArray = null;
		imgArray  = null;
		urlArray  = null;

		count = 0;
		while( count != itemCount )
		{
			nameArray = pushBackStringArray( nameArray, getStringArrayAt(dlok, c)); c += 1;
			imgArray  = pushBackStringArray( imgArray,  getStringArrayAt(dlok, c)); c += 1;
			urlArray  = pushBackStringArray( urlArray,  getStringArrayAt(dlok, c)); c += 1;

			count += 1;
		}
	}
	if( itemCount == 0 ) postMessage("return");

	if( savedItem &gt; ( itemCount - 1 ))
	{
		setFocusItemIndex( itemCount - 1 );
	}
	else setFocusItemIndex( savedItem );

	cancelIdle();
	redrawDisplay();
  </onRefresh>

  <onExit>
	setRefreshTime(-1);
  </onExit>

  <item_template>
    <onClick>
	idx = getFocusItemIndex();
	url = getStringArrayAt(urlArray , idx);
	ret = doModalRss(url);
	if(( ret != null )&amp;&amp;( ret != "" ))
	{
		moUrl = ret;
		savedItem = idx;
		setRefreshTime(1);
	}
	null;
    </onClick>
  </item_template>
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

	$view = new rssSkinSelectView;

	$view->topTitle = getMsg( 'sskinTitle' );
	$view->bottomTitle = 
	 getRssCommandPrompt('menu')  . getMsg( 'coreRssPromptMenu' ).'   '.
	 getRssCommandPrompt('enter') . getMsg( 'coreRssPromptActs' );


	$view->showRss();
}

?>
