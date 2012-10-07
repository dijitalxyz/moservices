<?php

//
// ------------------------------------
function getCaptureImage()
{
global $mos;
global $mosPath;

	$src = '/modules/capture/noimage.png';
	exec( "wget -q 'http://127.0.0.1/cgi-bin/IpodCGI.cgi?id=0&command=option_blue' -O /dev/null 2>/dev/null" );
	$nn = '';
	if(( $ns = glob( '/tmp/nfs/*.bmp' )) !== false )
	{
		exec( 'rm -f /tmp/www/sc_*' );
		foreach( $ns as $n )
		{
			$nn = 'sc_'. str_replace( '.bmp', '.jpg', basename( $n ));
			exec( "$mos/bin/bmp2jpg -q 95  $n /tmp/www/$nn" );
			unlink( $n );
		}
	}
	return $nn;
}
//
// ------------------------------------
function get_capture_content()
{
	$src = getCaptureImage();
	echo '<img src="'. $src .'" width="100%"/>';
}
//
// ------------------------------------
function capture_head()
{

?>
<link rel="stylesheet" href="/modules/core/css/toolbar.css" type="text/css" media="screen" charset="utf-8">
<style>
.rc_frame
{
	display:table-cell;
	position: relative;
	left: 0px;
	top: 0px;
	width:336px;
	height:700px;
	padding:0;
	margin:0px 4px;
	overflow:hidden;
	border:0px;
	border-radius:5px; -moz-border-radius:5px; -webkit-border-radius:5px;
}
.rc_image
{
	max-width:1280px;
}
</style>

<script type="text/javascript">

function getInfo(url, dst)
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

	df = document.getElementById(dst);
	df.innerHTML = xmlHttp.responseText;
}

function update_caption() {
	getInfo("?page=get_capture", "capt_image");
/*	window.setTimeout('update_caption()', 10000); */
}


function update_caption_wait() {
	update_caption();
}

/* window.setTimeout('update_caption()', 10000); */

</script>
<?php

}

// ------------------------------------
function capture_body()
{
	$src = getCaptureImage();
	$rc = 'http://'.$_SERVER['HTTP_HOST']. '/rc.htm';

?>
<div id="container">
<table border="0" cellspacing="0" cellpadding="0">
<tr><td><h3><?= getMsg( 'captureTitle' ) ?></h3></td>
<td width="20">&nbsp;</td>
<td>
<div class="mod_toolbar">
<a class="mod_button" href="#" title="<?= getMsg( 'captureUpdate') ?>" onclick="update_caption();">
<img src="/modules/core/images/btn_refresh.png" /></a>
</div></td></tr></table>

<table border="0" cellspacing="0" cellpadding="0">
<tr valign="top">
<!-- <td><iframe class=rc_frame src="<?php echo $rc; ?>" width="100%" height="100%" scrolling="auto" frameborder="0" onclick="update_caption_wait();"></iframe></td> -->
<td id=capt_image width="100%"><img class=rc_image src="<?= $src ?>" width="100%"/></td>
</tr></table>
</div>
<?php

}

?>
