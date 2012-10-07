<?php

//
// ====================================
function parole_actions( $act, $log )
{
	if( $act != 'set' ) return;

	// lighttpd.conf

	$s = file_get_contents( '/usr/local/etc/mos/etc/lighttpd.conf' );
	// remove auth
	if(( $p1 = strpos( $s, "\n# auth begin" )) > 0 )
	{
		if(( $p2 = strpos( $s, "# auth end", $p1 )) > 0 )
		{
			$p2 += 10;
			$s = substr( $s, 0, $p1 ) . substr( $s, $p2 );
		}
	}
	$s = preg_replace( '/\n([ 	]*"mod_auth")/', "\n#\$1", $s );

	if( isset( $_REQUEST['cm_set'] ))
	{
		$pw = $_REQUEST['passwd'];
		// set root password
		exec( 'echo -e "'. $pw .'\n'. $pw .'\n" | passwd root' );

		// lighttpd.conf
		$s .= '# auth begin
auth.backend = "plain"
auth.backend.plain.userfile = "/usr/local/etc/mos/etc/lighttpd.plain"
$HTTP["remoteip"] !~ "127.0.0.1" {
auth.require = (
	"/" =>
		(
			"method"  => "digest",
			"realm"   => "moServices3 web interface",
			"require" => "valid-user"
		)
	)
}
# auth end
';
		$s = preg_replace( '/\n#([ 	]*"mod_auth")/', "\n\$1", $s );

		// set root password for lighttpd
		exec( 'echo "root:'. $pw .'" > /usr/local/etc/mos/etc/lighttpd.plain' );

	}
	elseif( isset( $_REQUEST['cm_clear'] ))
	{
		// delete passwords
		exec( 'passwd -d root' );
	}
	else return;

	file_put_contents( '/usr/local/etc/mos/etc/lighttpd.conf', $s );

	// reboot
	if( $log )
	{
?>
<script type="text/javascript">
	window.location.href='/';
</script>
<?php
	}
	doCommand( 'reboot', $log );

}

// ------------------------------------
function parole_head()
{

?>
<link rel="stylesheet" href="/modules/core/css/buttons.css" type="text/css" media="screen" charset="utf-8">
<link rel="stylesheet" href="/modules/core/css/sets.css" type="text/css" media="screen" charset="utf-8">
<script type="text/javascript">

var showPass = 0;

function ShowHidePasswd()
{
	d = document.getElementById( 'pswd' );
	if( showPass == 0 )
	{
		showPass = 1;
		d.type = 'text';
	}
	else
	{
		showPass = 0;
		d.type = 'password';
	}
}
</script>
<?php

}

// ------------------------------------
function parole_body()
{
	// check parole
	$ep = false;
	$s = file_get_contents( '/usr/local/etc/passwd' );
	if( strpos( $s, 'root::0:0:root' ) === false )
	{
		$ep = true;
		$ss = file( '/usr/local/etc/mos/etc/lighttpd.plain' );
		foreach( $ss as $s )
		{
			$a = explode( ':', trim( $s ) );
			if( $a[0] == 'root' )
			{
				$passwd = $a[1];
				break;
			}
		}
	}

?>
<div id="container">
<h3><?= getMsg( 'paroleTitle' ) ?></h3>
<div class="set_card">
<table class="set_list" border="0" cellspacing="0" cellpadding="8">
<tr><td colspan="2"><?= getMsg( 'paroleSubtitle') ?></h4></tr>
<form action="?page=parole&act=set" method="post">
<tr>
<td><?= getMsg( 'parolePasswd') ?></td>
<td align="left"><input id="pswd" name="passwd" type="password" size="30" value="<?= $passwd ?>" /></td>
</tr>
<tr><td />
<td align="left"><input type="checkbox" onclick="ShowHidePasswd();" /><?= getMsg( 'paroleShow') ?></td>
</td></tr>
<tr><td /><td align="right">
<button class="buttons" type="submit" name="cm_set"><?= getMsg( 'paroleSet') ?></button>
<?php
	if( $ep )
	{

?>
<button class="buttons" type="submit" name="cm_clear"><?= getMsg( 'paroleClear') ?></button>
<?php
	}

?>
</td></tr></form></table>
</div>
</div>
<?php

}

?>