<?php

// ------------------------------------
function edit_actions( $act, $log )
{
global $mos;
global $nav_modules;

	if( ! isset( $_REQUEST[ 'mod' ] )) return;
	if( ! isset( $_REQUEST[ 'edit_text' ] )) return;

	if( $act == "editsave" )
	{
		$s = $_REQUEST[ 'edit_text' ];

		if( get_magic_quotes_gpc() )
			$s = stripslashes( $s );

		$s = str_replace( "\r", '', $s );

		$mod = $_REQUEST[ 'mod' ];

		$m = array ();
		$m = parse_ini_file( $mos.'/etc/pm/installed', true );
		$opts = $m[ $mod ];
		if( loadModuleOptions( $mod, $opts ) )
		{
			$conf = "$mos/".$opts['config_edit'];
			file_put_contents( $conf, $s );

			if(( $st = $opts['_status'] ) <> 'disable' )
			{
				if( isset( $opts['config_after'] ))
				 exec( "$mos/etc/init/".$opts['init'].' '.$opts['config_after'] );

				if( $st <> 'stop' )
				 doAction( $mod, 'restart', $log );
			}
		}
	}
}

// ------------------------------------
function edit_head()
{
?>
<link rel="stylesheet" href="modules/core/css/edit.css" type="text/css" media="screen" charset="utf-8">
<link rel="stylesheet" href="modules/core/css/toolbar.css" type="text/css" media="screen" charset="utf-8">
<?php

}
// ------------------------------------

function edit_body()
{
global $mos;
global $nav_modules;

	if( ! isset( $_REQUEST['mod'] )) return;

	$mod = $_REQUEST['mod'];

	if( isset( $nav_modules[ $mod ]['config_before'] ))
	if( $nav_modules[ $mod ]['_status'] <> 'disable' )
		exec( "$mos/etc/init/".$nav_modules[ $mod ]['init'].' '.$nav_modules[ $mod ]['config_before'] );

	$conf = "$mos/".$nav_modules[ $mod ]['config_edit'];
	if( ! file_exists( $conf )) return;

?>
<form action="?page=edit&mod=<?= $mod ?>" method="post" enctype="multipart/form-data">
<div id="container">
<table border="0" cellspacing="0" cellpadding="0">
<tr><td><h3><?= $conf ?></h3></td>
<td width="20">&nbsp;</td>
<td>
<button class="mod_button" type="submit" name="act" value="editsave" title="<?= getMsg( 'coreCmSave') ?>">
<img src="modules/core/images/btn_ok.png" /></button>

<a class="mod_button" href="/" title="<?= getMsg( 'coreCmCancel') ?>">
<img src="modules/core/images/btn_cancel.png" /></a>

<button class="mod_button" type="submit" name="do" value="editexport" title="<?= getMsg( 'coreCmExport') ?>">
<img src="modules/core/images/btn_export.png" /></button>

</td></tr></table>

<div id="edit_container">
<textarea id="edit_area" name="edit_text" class="edit_area" wrap="off"><?php readfile( $conf ); ?></textarea>
</div>

</div></form>
<?php

}

?>