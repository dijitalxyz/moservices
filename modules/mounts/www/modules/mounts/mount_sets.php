<?php

// load options
$m_options = array(
	'wAddr'  => 0,
	'wMount' => 0,
	'nTries' => 5
);
$m_opts_file = $mos.'/etc/mounts.ini';
if( file_exists( $m_opts_file )) $m_options = parse_ini_file( $m_opts_file, false );

//
// ====================================
function mount_sets_actions( $act, $log )
{
global $m_options;
global $m_opts_file;

	if( $act == 'set' )
	{
		if( isset( $_REQUEST['waitaddr'] ))
		{ $m_options['wAddr'] = 1; }
		else $m_options['wAddr'] = 0;

		if( isset( $_REQUEST['waitmount'] ))
		{ $m_options['wMount'] = 1; }
		else $m_options['wMount'] = 0;

		if( isset( $_REQUEST['triesmount'] ))
			$m_options['nTries'] = $_REQUEST['triesmount'];

		// save options
		$s = '';
		foreach( $m_options as $n => $v )
			$s .= "$n=$v\n";

		file_put_contents( $m_opts_file, $s );
	}
}

// ------------------------------------
function mount_sets_head()
{

//<link rel="stylesheet" href="/modules/core/css/services.css" type="text/css" media="screen" charset="utf-8">

?>
<link rel="stylesheet" href="modules/core/css/buttons.css" type="text/css" media="screen" charset="utf-8">
<link rel="stylesheet" href="modules/core/css/sets.css" type="text/css" media="screen" charset="utf-8">
<?php

}

// ------------------------------------
function mount_sets_body()
{
global $m_options;

?>
<div id="container">
<h3><?= getMsg( 'mountSettings' ) ?></h3>
<div class="set_card">
<table class="set_list" border="0" cellspacing="0" cellpadding="8">
<tr><td colspan="2"><?= getMsg( 'mountSetTitle') ?></h4></tr>
<form action="?page=mount_sets&act=set" method="post">
<tr><td align="center"><?php

	$sel = '';
	if( $m_options['wAddr'] == 1 ) $sel = ' checked';
	echo '<input name="waitaddr" type="checkbox"'.$sel.' />';

?></td><td><?= getMsg( 'mountWaitIP') ?></td></tr>
<tr><td align="center"><?php

	$sel = '';
	if( $m_options['wMount'] == 1 ) $sel = ' checked';
	echo '<input name="waitmount" type="checkbox"'.$sel.' />';

?></td><td><?= getMsg( 'mountWaitMount') ?></td></tr>
<tr><td align="center"><input name="triesmount" type="text" size="3" value="<?= $m_options['nTries'] ?>" />
</td><td><?= getMsg( 'mountNTries') ?></td></tr>

<tr><td /><td align="right">
<button class="buttons" type="submit"><?= getMsg( 'coreCmSave') ?></button>
</td></tr></form></table>
</div>
</div>
<?php

}

?>