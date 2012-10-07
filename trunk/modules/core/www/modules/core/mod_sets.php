<?php

// ------------------------------------
function sets_head()
{

?>
<link rel="stylesheet" href="modules/core/css/sets.css" type="text/css" media="screen" charset="utf-8">
<link rel="stylesheet" href="modules/core/css/buttons.css" type="text/css" media="screen" charset="utf-8">
<?php

}

// ------------------------------------
function sets_body()
{
global $nav_lang;

?>
<div id="container">

<h3><?= getMsg( 'coreSettings') ?></h3>

<table class="set_list" border="0" cellspacing="0" cellpadding="8">
<form action="" method="get">
<input type="hidden" name="page" value="sets">
<input type="hidden" name="do" value="chlng">
<tr align="center"><td><?= getMsg( 'coreTChLang') ?><br /><br />
<select name="lang">
<?php

	$d  = opendir( 'modules/core' );
	while ( $f = readdir( $d ) )
	{

	        if( preg_match( '/lang_(.*)\.php/', $f, $ss ) > 0 )
		{
			$i = $ss[1];
			$s = file_get_contents( 'modules/core/'.$f );
		        if( preg_match( '/\'language\'.*=.*\'(.*)\'/', $s, $ss ) > 0 )
			{
				$s = $ss[1];
				echo '<option';
				if( $i == $nav_lang ) echo ' selected';
				echo " value=\"$i\">$s</option>\n";
			}
		}
	}
?>
</select><br />
<button class="buttons" type="submit"><?= getMsg( 'coreCmSet') ?></button>
</td></tr></form>

<tr align="center"><td class="set_delim"><?= getMsg( 'coreTSaveSets') ?><br /><br />
<a class="buttons" href="?page=sets&do=backup"><?= getMsg( 'coreCmSave') ?></a>
</td></tr>

<form action="?page=sets&do=restore" method="post" enctype="multipart/form-data">
<tr align="center"><td class="set_delim"><?= getMsg( 'coreTLoadSets') ?><br /><br />
<input type="file" name="backup"><br />
<button class="buttons" type="submit"><?= getMsg( 'coreCmLoad') ?></button>
</td></tr></form>

<tr align="center"><td class="set_delim"><?= getMsg( 'coreTRemoveMos') ?><br /><br />
<a class="buttons" href="?page=delete"><?= getMsg( 'coreCm_delete') ?></a>
</td></tr></table>

</td></tr></table></div>
<?php

}
//
// ====================================
//
function delete_actions( $act, $log )
{
global $mos;

	if( $act == "delete" )
	{
		doCommand( "cp -af $mos/bin/mos_remove /tmp", $log );
		if( $log )
		{
?>
<script type="text/javascript">
	window.location.href="/";
</script>
<?php
		}
		doCommand( 'exec /tmp/mos_remove', $log );
	}
}

// ------------------------------------
//
function delete_body()
{

?>
<script type="text/javascript">
	isConfirmed = confirm('<?= getMsg( 'moreDelete' ) ?>');
	if(isConfirmed)
	{
		window.location.href = '?page=delete&act=delete';
	}
	else
	{
		window.location.href='?page=sets';
	}
</script>
<?php

}

?>