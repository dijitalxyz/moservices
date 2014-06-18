<?php

// get config
$peerstv_config = array(
	'channel' => '',
	'cast'    => 'patch',	// list, patch
	'vfd'     => 'none',	// none, mele, inext
);

$peerstv_config_path = dirname( __FILE__ ) .'/peerstv.config.php';

if( is_file( $peerstv_config_path ) )
{
	include( $peerstv_config_path );
}

// get session
$peerstv_session = array(
	'channels' => array(),
);

$peerstv_session_path = '/tmp/peerstv.session.php';

if( is_file( $peerstv_session_path ) )
{
	include( $peerstv_session_path );
}
//
// ------------------------------------
function getPeerstvConfigParameter( $name )
{
global $peerstv_config;

	return $peerstv_config[ $name ];
}
//
// ------------------------------------
function peerstvSaveConfig()
{
global $peerstv_config;
global $peerstv_config_path;

if( isset( $_REQUEST['debug'])) print_r( $peerstv_config );

	file_put_contents( $peerstv_config_path, '<?php $peerstv_config = '.var_export( $peerstv_config, true ).'; ?>' );
}
//
// ------------------------------------
function peerstvSaveSession()
{
global $peerstv_session;
global $peerstv_session_path;

if( isset( $_REQUEST['debug'])) print_r( $peerstv_session );

	file_put_contents( $peerstv_session_path, '<?php $peerstv_session = '.var_export( $peerstv_session, true ).'; ?>' );
}

?>