<?php

// ------------------------------------
function getSmartResult( $cmd )
{
	exec( $cmd, $ss );
	echo "<pre>\n". implode( "\n", $ss )."</pre>\n";
}

// ------------------------------------
function smarttool_head()
{

?>
<link rel="stylesheet" href="modules/core/css/info.css" type="text/css" media="screen" charset="utf-8">
<link rel="stylesheet" href="modules/core/css/toolbar.css" type="text/css" media="screen" charset="utf-8">
<style>

div.smart_topic
{
	position: relative;
	background: #ccc;
	padding:4px;
	margin :0;
	border:0;
}

td.smart_btn
{
	background: #ccc no-repeat right top url(modules/core/images/btn_hide.png);
}
table.smart_table
{
	white-space:nowrap;
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

function look(list, topic)
{
	plist=document.getElementById(list);
	ptopic=document.getElementById(topic);
	if(plist.style.display == "none")
	{
		plist.style.display = "block";
		ptopic.style.background = "#ccc no-repeat right top url(modules/core/images/btn_hide.png)";
	}
	else {
		plist.style.display = "none";
		ptopic.style.background = "#ccc no-repeat right top url(modules/core/images/btn_show.png)";
	}
}
</script>
<?php

}

// ------------------------------------
function smarttool_body()
{
global $mos;

?>
<div id="container">
<h3><?= getMsg( 'smartToolTitle' ) ?></h3>
<?

	exec( "$mos/bin/smartctl --scan-open", $devices);

	foreach ( $devices as $id => $device )
	{
		$a = explode( ' ', $device );
		if( ( $dev = $a[0] ) == '' ) continue;

?>
<div class="info_frame">
<div class="smart_topic">
<table class="smart_table" border="0" cellspacing="0" cellpadding="0">
<tr><td><h3><?= $dev ?></h3></td>
<td>&nbsp;</td>
<td width="100%">
<div class="mod_toolbar">

<a class="mod_button" href="#" onclick="getInfo('?page=get_smart&cmd=info&dev=<?= $dev ?>','l_<?= $id ?>')" title="<?= getMsg( 'smartToolInfo') ?>">
<img src="modules/smarttool/images/database_refresh.png" /></a>

<a class="mod_button" href="#" onclick="getInfo('?page=get_smart&cmd=short&dev=<?= $dev ?>','l_<?= $id ?>')" title="<?= getMsg( 'smartToolShort') ?>">
<img src="modules/smarttool/images/control_fastforward_blue.png" /></a>

<a class="mod_button" href="#" onclick="getInfo('?page=get_smart&cmd=long&dev=<?= $dev ?>','l_<?= $id ?>')" title="<?= getMsg( 'smartToolLong') ?>">
<img src="modules/smarttool/images/control_play_blue.png" /></a>

<a class="mod_button" href="#" onclick="getInfo('?page=get_smart&cmd=abort&dev=<?= $dev ?>','l_<?= $id ?>')" title="<?= getMsg( 'smartToolAbort') ?>">
<img src="modules/smarttool/images/control_stop_blue.png" /></a>

<a class="mod_button" href="#" onclick="getInfo('?page=get_smart&cmd=result&dev=<?= $dev ?>','l_<?= $id ?>')" title="<?= getMsg( 'smartToolTestInfo') ?>">
<img src="modules/smarttool/images/storage.png" /></a>

</div></td>

<td class="smart_btn" id="t_<?= $id ?>">
<a href="#" onclick="look('l_<?= $id ?>','t_<?= $id ?>')">
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a>
</td>

</tr></table>
</div>
<div id="l_<?= $id ?>" class="info_list"><pre>
<?php
		getSmartResult("$mos/bin/smartctl $dev -x");

?>
</pre></div></div>
<?php
	}

?>
</div>
<?php

}
// ------------------------------------
function get_smart_content()
{
global $mos;

	if( ! isset( $_REQUEST['cmd'] )) return;
	$cmd = $_REQUEST['cmd'];

	if( ! isset( $_REQUEST['dev'] )) return;
	$dev = $_REQUEST['dev'];

	if( $cmd == 'info' )
	{
		getSmartResult("$mos/bin/smartctl $dev -x");
	}
	else if( $cmd == 'short' )
	{
		getSmartResult("$mos/bin/smartctl $dev -t short");
	}
	else if( $cmd == 'long' )
	{
		getSmartResult("$mos/bin/smartctl $dev -t long");
	}
	else if( $cmd == 'abort' )
	{
		getSmartResult("$mos/bin/smartctl $dev -X");
	}
	else if( $cmd == 'result' )
	{
		getSmartResult("$mos/bin/smartctl $dev -l selftest");
	}
}
?>
