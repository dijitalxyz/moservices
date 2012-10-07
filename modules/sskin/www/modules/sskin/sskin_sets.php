<?php

// load options
$s_options = array(
	'direct'  => 0
);
$s_opts_file = $mos.'/etc/sskin.conf';
if( file_exists( $s_opts_file )) $s_options = parse_ini_file( $s_opts_file, false );

//
// ====================================
function sskin_sets_actions( $act, $log )
{
global $s_options;
global $s_opts_file;

	if( $act == 'set' )
	{
		if( isset( $_REQUEST['direct'] ))
		 $s_options['direct'] = $_REQUEST['direct'];

		// save options
		$s = '';
		foreach( $s_options as $n => $v )
			$s .= "$n=$v\n";

		file_put_contents( $s_opts_file, $s );
	}
}

// ------------------------------------
function sskin_sets_head()
{

?>
<link rel="stylesheet" href="/modules/core/css/buttons.css" type="text/css" media="screen" charset="utf-8">
<link rel="stylesheet" href="/modules/core/css/sets.css" type="text/css" media="screen" charset="utf-8">
<?php

}

// ------------------------------------
function sskin_sets_body()
{
global $s_options;

?>
<div id="container">
<h3><?= getMsg( 'sskinSettings' ) ?></h3>
<div class="set_card">
<table class="set_list" border="0" cellspacing="0" cellpadding="8">
<form action="?page=sskin_sets&act=set" method="post">

<tr><td align="center"><?php

	$sel = '';
	if( $s_options['direct'] == 1 ) $sel = ' checked';
	echo '<input type="radio" name="direct" value="1"'.$sel.' />';

?></td><td><?= getMsg( 'sskinDirect') ?></td></tr>
<tr><td align="center"><?php

	$sel = '';
	if( $s_options['direct'] == 0 ) $sel = ' checked';
	echo '<input type="radio" name="direct" value="0"'.$sel.' />';

?></td><td><?= getMsg( 'sskinNoDirect') ?></td></tr>

<tr><td /><td align="right">
<button class="buttons" type="submit"><?= getMsg( 'coreCmSave') ?></button>
</td></tr></form></table>
</div>
</div>
<?php

}

?>