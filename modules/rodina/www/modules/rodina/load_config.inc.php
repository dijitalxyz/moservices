<?php

$rodina_config = array(
	'login'  => '',
	'passwd' => '',
);

$fconfig = dirname( __FILE__ ) .'/rodina.config.php';

if( is_file( $fconfig ) )
{
	include( $fconfig );
}

$rodina_session = array(
	'portal' => '',
	'token' => '',
	'ttl' => 0,

	'gid' => '',
	'cid' => '',

	'code' => '',

	'categories' => array(),
	'channels' => array()
);

if( is_file( '/tmp/rodina.session.php' ) )
{
	include( '/tmp/rodina.session.php' );
}
//
// ------------------------------------
function saveRodinaConfig()
{
global $rodina_config;

	$fconfig = dirname( __FILE__ ) .'/rodina.config.php';
	file_put_contents( $fconfig, '<?php $rodina_config = '.var_export( $rodina_config, true ).'; ?>' );
}
//
// ------------------------------------
function saveRodinaSession()
{
global $rodina_session;

	file_put_contents( '/tmp/rodina.session.php', '<?php $rodina_session = '.var_export( $rodina_session, true ).'; ?>' );
}
//
// ------------------------------------
function getRodinaConfigParameter( $name )
{
global $rodina_config;

	return $rodina_config[ $name ];
}

?>
