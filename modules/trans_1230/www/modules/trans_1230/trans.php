<?php
function trans_head()
{
global $nav_title;

	$nav_title = 'Transmission web interface';
}

function trans_body()
{
	$src = 'http://'.$_SERVER["SERVER_ADDR"].':9091';

?>
<iframe class="cont_frame" src="<?php echo $src; ?>" width="100%" height="100%" scrolling="auto" frameborder="0">
</iframe>

<?php

}

?>