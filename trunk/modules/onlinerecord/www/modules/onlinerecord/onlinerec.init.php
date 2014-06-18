<?php

// get config
$onlinerec_config = array(
	'channel' => '',
	'quality' => 'middle',	// low, middle, high
	'cast'    => 'stream',	// stream, list
	'vfd'     => 'none',	// none, mele, inext
);

$onlinerec_config_path = dirname( __FILE__ ) .'/onlinerec.config.php';

if( is_file( $onlinerec_config_path ) )
{
	include( $onlinerec_config_path );
}

// get session
$onlinerec_session = array(
	'channels' => array(),
);

$onlinerec_session_path = '/tmp/onlinerec.session.php';

if( is_file( $onlinerec_session_path ) )
{
	include( $onlinerec_session_path );
}
//
// ------------------------------------
function getOnlinerecConfigParameter( $name )
{
global $onlinerec_config;

	return $onlinerec_config[ $name ];
}
//
// ------------------------------------
function onlinerecSaveConfig()
{
global $onlinerec_config;
global $onlinerec_config_path;

if( isset( $_REQUEST['debug'])) print_r( $onlinerec_config );

	file_put_contents( $onlinerec_config_path, '<?php $onlinerec_config = '.var_export( $onlinerec_config, true ).'; ?>' );
}
//
// ------------------------------------
function onlinerecSaveSession()
{
global $onlinerec_session;
global $onlinerec_session_path;

if( isset( $_REQUEST['debug'])) print_r( $onlinerec_session );

	file_put_contents( $onlinerec_session_path, '<?php $onlinerec_session = '.var_export( $onlinerec_session, true ).'; ?>' );
}

?>