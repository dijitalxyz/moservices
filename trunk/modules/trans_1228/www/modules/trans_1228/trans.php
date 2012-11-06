<?php
function trans_head()
{
global $mos;
global $nav_lang;
global $nav_title;

	$l = $mos.'/trans';

	exec( "rm -f $l/web" );
	if( $nav_lang == 'ru' )
	{
		exec( "ln -s $l/web_ru/ $l/web" );
	}
	else exec( "ln -s $l/web_en/ $l/web" );

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