<?php

$rc_buttons = array(
	'mute'	=> array(  6, 4, 30, 30,'','','m' ),
	'eject'	=> array( 84, 4, 30, 30,'','','!' ),

	'1'	=> array(  4, 42, 32, 30,'1','','1' ),
	'2'	=> array( 44, 42, 32, 30,'2','','2' ),
	'3'	=> array( 84, 42, 32, 30,'3','','3' ),

	'4'	=> array(  4, 72, 32, 30,'4','','4' ),
	'5'	=> array( 44, 72, 32, 30,'5','','5' ),
	'6'	=> array( 84, 72, 32, 30,'6','','6' ),

	'7'	=> array(  4, 102, 32, 30,'7','','7' ),
	'8'	=> array( 44, 102, 32, 30,'8','','8' ),
	'9'	=> array( 84, 102, 32, 30,'9','','9' ),

	'display'=> array(  4, 134, 32, 30,'i','','}' ),
	'0'	 => array( 44, 134, 32, 30,'0','','0' ),
	'search' => array( 84, 134, 32, 30, '','','-' ),

	'home'	=> array(  6, 170, 30, 30,'','','{' ),
	'up'	=> array( 40, 176, 40, 34,'','','W' ),
	'return'=> array( 84, 170, 30, 30,'','',',' ),

	'left'	=> array(  4, 210, 32, 40,'','','A' ),
	'ok'	=> array( 40, 210, 40, 40,'','',' ' ),
	'right'	=> array( 84, 210, 32, 40,'','','D' ),

	'menu'	=> array(  6, 258, 30, 30,'','','M' ),
	'down'	=> array( 40, 252, 40, 34,'','','X' ),
	'setup'	=> array( 84, 258, 30, 30,'','','/' ),

	'stop'	=> array(  4, 304, 32, 30,'','','Q' ),
	'play'	=> array( 44, 304, 32, 30,'','','S' ),
	'pause'	=> array( 84, 304, 32, 30,'','','Z' ),

	'frwd'	=> array(  4, 334, 32, 30,'','','f' ),
	'slow'	=> array( 44, 334, 32, 30,'','','L' ),
	'ffwd'	=> array( 84, 334, 32, 30,'','','F' ),

	'prev'	=> array(  4, 364, 32, 30,'','','P' ),
	'zoom'	=> array( 44, 364, 32, 30,'','','z' ),
	'next'	=> array( 84, 364, 32, 30,'','','N' ),

	'insrep' => array(  4, 394, 32, 30,'','','q' ),
	'zoomout'=> array( 44, 394, 32, 30,'','','j' ),
	'cmskip' => array( 84, 394, 32, 30,'','','B' ),

	'repeat'  => array(  4, 430, 32, 30,'','','+' ),
	'subtitle'=> array( 44, 430, 32, 30,'','','s' ),
	'audio'	  => array( 84, 430, 32, 30,'','','a' ),

	'ab'	=> array(  4, 460, 32, 30,'','','=' ),
	'pscan'	=> array( 44, 460, 32, 30,'','',"\\" ),
	'option'=> array( 84, 460, 32, 30,'','','E' ),

	'red'	=> array(  4, 494, 24, 24,'','','u' ),
	'green'	=> array( 33, 494, 24, 24,'','','d' ),
	'yellow'=> array( 62, 494, 24, 24,'','','w' ),
	'blue'	=> array( 91, 494, 24, 24,'','','x' ),
);

//
// ------------------------------------
function send_command_content()
{
	header( "Content-type: text/plain" );

	if( !isset( $_REQUEST['command'] ) ) return;

	$c = urldecode( $_REQUEST['command'] );
	exec( "echo -n '$c' > /tmp/ir" );

	echo 'ok' .PHP_EOL;
}
//
// ------------------------------------
function getCaptureImage()
{
global $mos;
global $mosPath;

	$s = '/modules/capture/noimage.png';
	exec( "echo -n '*' > /tmp/ir" );
	exec( "sleep 1" );
	if(( $ns = glob( '/tmp/nfs/*.bmp' )) !== false )
	{
		exec( 'rm -f /tmp/www/sc_*.jpg' );
		foreach( $ns as $n )
		{
			$s = 'sc_'. str_replace( '.bmp', '.jpg', basename( $n ));
			exec( "$mos/bin/bmp2jpg -q 80  $n /tmp/www/$s" );
			unlink( $n );
		}
	}
	return $s;
}
//
// ------------------------------------
function get_capture_content()
{
	$src = getCaptureImage();
	echo '<img class="rc_image" src="'. $src .'" width="100%"/>';
}
//
// ------------------------------------
function capture_head()
{
global $rc_buttons;

?>
<link rel="stylesheet" href="/modules/core/css/toolbar.css" type="text/css" media="screen" charset="utf-8">
<style>
div.rc_remote
{
	display:block;
	position:relative;
	float:left;
	width:112px;
	height:514px;

	padding:4px;
	margin:4px;

	background-color: #aaa;

	border:1px solid #ccc;
	border-radius:5px; -moz-border-radius:5px; -webkit-border-radius:5px;
}
div.btn_inscr
{
	display:block;
	position:absolute;
	top:4px;
	left:0;
	width:100%;

	text-align:center;
	color:white;
	font-weight:bold;
	font-size:10px;
}
div.btn_subscr
{
	display:block;
	position:absolute;
	bottom:0;
	left:0;
	width:100%;

	text-align:center;
	color:white;
	font-weight:bold;
	font-size:9px;
}
<?php
	foreach( $rc_buttons as $id => $btn )
	{
		echo 'div.btn_btn_'. $id
		.'{display:block;position:absolute;'
		.'left:'. $btn[0] .'px;'
		.'top:'. $btn[1] .'px;'
		.'width:'. $btn[2] .'px;'
		.'height:'. $btn[3] .'px;'
		.'text-align:center;'
		.'color:white;'
		.'font-weight:bold;'
		.'font-size:10px;'
//		.'border:1px solid black;'
		.'}'. PHP_EOL;
	}

?>
div.rc_capt
{
	display:table-cell;
/*	position:relative; */
	width:100%;

	padding:4pt;
	margin:0;
}
.rc_image
{
	max-width:1280px;
}

</style>

<script type="text/javascript">

function getInfo(url)
{
	if (window.XMLHttpRequest) {
		xmlHttp = new XMLHttpRequest();
        }
	else
	{// code for IE6, IE5
		xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
	}
	xmlHttp.open("GET", url, false);
	xmlHttp.send();

	return xmlHttp.responseText;
}

function updateCaption() {
	df = document.getElementById( "capt_image" );
	df.innerHTML = getInfo( "?page=get_capture" );
/*	window.setTimeout('updateCaption()', 10000); */
}

function sendCmd( cmd ) {
	getInfo( "?page=send_command&command=" + cmd );
	updateCaption();
}

function showRemote()
{
	p=document.getElementById("remote");
	if(p.style.display == "none") p.style.display = "table-cell";
	else p.style.display = "none";
}

/* window.setTimeout('updateCaption()', 10000); */

</script>
<?php

}

// ------------------------------------
function capture_body()
{
global $rc_buttons;

	$src = getCaptureImage();

?>
<div id="container">
<table border="0" cellspacing="0" cellpadding="0">
<tr><td><h3><?= getMsg( 'captureTitle' ) ?></h3></td>
<td width="20">&nbsp;</td>
<td>
<div class="mod_toolbar">
<a class="mod_button" href="#" title="<?= getMsg( 'captureRemote') ?>" onclick="showRemote();">
<img src="/modules/capture/images/btn_remote.png" /></a>
<a class="mod_button" href="#" title="<?= getMsg( 'captureUpdate') ?>" onclick="updateCaption();">
<img src="/modules/core/images/btn_refresh.png" /></a>
</div></td></tr></table>

<div style="display:table">
<div style="display:table-row">
<div style="display:table-cell;vertical-align:top;" id="remote">
<div class="rc_remote">
<?php

	foreach( $rc_buttons as $id => $btn )
	{
		echo '<a href="#" onclick="sendCmd('."'". urlencode( $btn[6] ) ."'" .');"';
		echo ' title="'. getMsg( 'button_'. $id ) .'">';
		echo '<div class="btn_btn_'. $id .'">';

		if( $btn[4] != '' ) $img = 'btn';
		else  $img = 'btn_'. $id ;
		echo '<img src="/modules/capture/images/'. $img .'.png" width="100%" />';

		if( $btn[4] != '' ) echo '<div class="btn_inscr">'. $btn[4].'</div>';
		if( $btn[5] != '' ) echo '<div class="btn_subscr">'. $btn[5].'</div>';

		echo'</div></a>'. PHP_EOL;
	}

?>
</div>
</div>

<div id="capt_image" class="rc_capt" >
  <img class="rc_image" src="<?= $src ?>" width="100%"/>
</div>

</div>
</div>
</div>
<?php

}

?>
