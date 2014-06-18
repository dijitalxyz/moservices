<?php
//
// ====================================

$rodina_session = array(
	'message' => '',
);

if( is_file( '/tmp/rodina.session.php' ) )
{
	include( '/tmp/rodina.session.php' );
}

//
// ------------------------------------
function rss_rodina_message_content()
{
global $rodina_session;

	class rssSkinRodinaMessage extends rssSkin
	{
	const titleBackgroundColor = '200:200:200';
	const itemBackgroundColor  = '20:20:20';

//	const titleBackgroundColor = '245:173:29';
//	const itemBackgroundColor  = '0:9:39';

		//
		// ------------------------------------
		function showOnUserInput()
		{

?>
    <onUserInput>
	input = currentUserInput();
	ret = "false";

	if( input == "<?= getRssCommand('enter') ?>" )
	{
		ret = "true";
		postMessage( "<?= getRssCommand('return') ?>" );
	}
	ret;
    </onUserInput>
<?php
		}
		//
		// ------------------------------------
		public $message;

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
	setRefreshTime(2000);
  </onEnter>

  <onRefresh>
	setRefreshTime(-1);
	postMessage( "<?= getRssCommand('return') ?>" );
  </onRefresh>

  <onExit>
	setRefreshTime(-1);
  </onExit>

<?php
		}
		//
		// ------------------------------------
		function showDisplay()
		{

?>
  <mediaDisplay name="onePartView"
   viewAreaXPC="34.375"
   viewAreaYPC="42.3611"
   viewAreaWidthPC="31.25"
   viewAreaHeightPC="15.2777"

   backgroundColor="0:0:0"
   cornerRounding="15"

   sideLeftWidthPC="0"
   sideRightWidthPC="0"
   sideColorLeft="0:0:0"
   sideColorRight="0:0:0"

   showHeader="no"
   showDefaultInfo="no"

   itemPerPage="0"
   itemXPC="0"
   itemYPC="0"
   itemWidthPC="0"
   itemHeightPC="0"
   itemBackgroundColor="0:10:40"

   drawItemText="no"
   forceFocusOnItem="yes"

   itemGapXPC="0"
   itemGapYPC="0"

   focusBorderColor = "0:0:0"
   unFocusBorderColor = "0:0:0"
  >

    <backgroundDisplay>
      <text offsetXPC="0" offsetYPC="0" widthPC="100" heightPC="100"
       cornerRounding="15" backgroundColor="<?= static::titleBackgroundColor ?>" >
      </text>
      <text offsetXPC="0.25" offsetYPC="1" widthPC="99.5" heightPC="98"
       cornerRounding="15" backgroundColor="<?= static::itemBackgroundColor ?>" >
      </text>
    </backgroundDisplay>

    <text offsetXPC="0.25" offsetYPC="11" widthPC="99.5" heightPC="78" 
     lines="2" align="center" fontSize="16" cornerRounding="15"
     backgroundColor="-1:-1:-1" foregroundColor="<?= static::unFocusFontColor ?>" >
      <script>
	"<?= $this->message ?>";
      </script>
    </text>
<?php

			$this->showOnUserInput();

?>
  </mediaDisplay>
<?php
		}
		//
		// ------------------------------------
		function showChannel()
		{

?>
  <channel>
    <itemSize>
      <script>
	0;
      </script>
    </itemSize>
  </channel>
<?php
		}
	}

	$view = new rssSkinRodinaMessage;

	$view->message = $rodina_session['message'];

	$view->showRss();
}

?>
