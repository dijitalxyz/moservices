<?php
function quixplorer_head()
{
global $nav_lang;

	$l = 'en';
	$s = file_get_contents( 'modules/quixplorer/_lang/_info.php' );
        if( preg_match_all( '/value="(.+)"/m', $s, $ss ) > 0 )
	if( in_array( $nav_lang, $ss[1] ) ) $l = $nav_lang;

	$s = file_get_contents( 'modules/quixplorer/.config/conf.php' );
	$s = preg_replace( '/(GLOBALS\["language"\] = )"(.+)"/m', "\\1\"$l\"", $s );
	file_put_contents( 'modules/quixplorer/.config/conf.php', $s );
}

function quixplorer_body()
{
	$src = 'modules/quixplorer/index.php';

?>
<iframe class="cont_frame" src="<?php echo $src; ?>" width="100%" height="100%" scrolling="auto" frameborder="0">
</iframe>

<?php

}

?>