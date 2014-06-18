<?php

//
// ====================================
function apower_sets_actions( $act, $log )
{
	if( $act == 'set' )
	{
		if( isset( $_REQUEST['model'] ))
		{
			$s = $_REQUEST['model'];
			// save model
			exec( 'sed -ri "s/^(player=).*$/\1'.$s.'/" /usr/local/etc/rc.init/S01aPower.sh' );
		}
	}
}

// ------------------------------------
function apower_sets_head()
{

//<link rel="stylesheet" href="/modules/core/css/services.css" type="text/css" media="screen" charset="utf-8">

?>
<link rel="stylesheet" href="modules/core/css/buttons.css" type="text/css" media="screen" charset="utf-8">
<link rel="stylesheet" href="modules/core/css/sets.css" type="text/css" media="screen" charset="utf-8">
<?php

}

// ------------------------------------
function apower_sets_body()
{
	$amodels = array(
		'r1'  => 'Asus O!Play R1/R3',
		'hd2' => 'Asus O!Play HD2',
		'mele'=> 'Mele players',
		'mele1283'=> 'Mele players 1283',
		'xtr' => 'xTreamer players',
	);

	// load player's model
	$aplayer = exec( 'sed -rn "s/^player=(.*)$/\1/p" /usr/local/etc/rc.init/S01aPower.sh' );
	$aplayer = trim( $aplayer );

?>
<div id="container">
<h3><?= getMsg( 'apowerSettings' ) ?></h3>
<div class="set_card">
<table class="set_list" border="0" cellspacing="0" cellpadding="8">
<tr><td colspan="2"><?= getMsg( 'apowerSetTitle') ?></h4></tr>

<form action="?page=apower_sets&act=set" method="post">

<tr><td colspan="2" align="center"><select name="model" size=1>
<?php

	foreach( $amodels as $m => $title )
	{
		$sel = '';
		if( $m == $aplayer ) $sel = ' selected';
		echo "<option value=\"$m\"$sel>$title</option>\n";
	}

?>
</select></td></tr>

<tr><td /><td align="right">
<button class="buttons" type="submit"><?= getMsg( 'coreCmSave') ?></button>
</td></tr></form></table>
</div>
</div>
<?php

}

?>