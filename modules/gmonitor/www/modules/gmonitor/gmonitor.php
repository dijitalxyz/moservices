<?php

// ------------------------------------
function gmonitor_head()
{

?>
<link rel="stylesheet" href="/modules/status/status.css" type="text/css" media="screen" charset="utf-8">
<script type="text/javascript" src="/modules/status/javascript/processes.js"></script>
<?php

}

// ------------------------------------
function gmonitor_body()
{
global $mos_web;

	$src = $mos_web .'modules/gmonitor/cpug.htm';

?>
<iframe class="cont_frame" src="<?php echo $src; ?>" width="100%" height="100%" scrolling="auto" frameborder="0">
</iframe>

<?php

}

?>
