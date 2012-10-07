<?php
//
// ------------------------------------
function getUpdates()
{
global $mos;


	$installed = array();
	$installed = parse_ini_file( $mos.'/etc/pm/installed', true );

	$packs = array();
	$packs = parse_ini_file( $mos.'/etc/pm/packages', true );

	$updates = array();

	foreach( $installed as $mod => $item )
	{
		if( ! isset( $packs[ $mod ] )) continue;

		$irev = $item['revision'];
		if( $irev == 'emb' ) continue;

		$arev = '';
		$arev = $packs[ $mod ]['revision'];
		if( $arev != $irev )
		{
			 $updates[ $mod ] = $item;
			 $updates[ $mod ][ '_arev' ] = $arev;
		}
	}
	return $updates;
}
//
// ------------------------------------
function installMod ( $mod, $log )
{
global $mos;

	return doCommand( "$mos/bin/pm install $mod", $log ) == 0;
}

// ------------------------------------
function removeMod ( $mod, $log )
{
global $mos;

	return doCommand( "$mos/bin/pm remove $mod", $log ) == 0;
}

// ------------------------------------

function doAction( $mod, $act, $log = false )
{
global $mos;

	if( $mod == '' ) return;

	$m = array ();
	$m = parse_ini_file( $mos.'/etc/pm/installed', true );
	$opts = $m[ $mod ];

	if( loadModuleOptions( $mod, $opts ) !== false )
	{
		$st   = $opts['_status'];
		$init = $mos.'/etc/init/'.$opts['init'];
		$haveInit = is_file( $init );
	}
	else
	{
		$st   = 'noinstall';
		$init = '';
		$haveInit = false;
	}

	if( $act == 'start' )
	{
		if( $haveInit )
			doCommand( "$init start", $log );
	}
	elseif( $act == 'stop'  )
	{
		if( $haveInit )
		if(( $st == 'start' )or( $st == 'enable' ))
		{
			doCommand( "$init stop", $log );
		}
	}
	elseif( $act == 'restart'  )
	{
		if( $haveInit )
		{
			doCommand( "$init stop", $log );
			doCommand( "$init start", $log );
		}
	}
	elseif( $act == 'enable'  )
	{
		if( $haveInit )
		{
			chmod( $init, 0755 );
			doCommand( "$init enable", $log );
		}
	}
	elseif( $act == 'disable'  )
	{
		if( $haveInit )
		if( is_executable( "$init" ))
		{
			if( $st <> 'stop' )
				doCommand( "$init stop", $log );

			doCommand( "$init disable", $log );
			chmod( $init, 0644 );
		}
	}
	elseif( $act == 'delete' )
	{
		if( $haveInit )
		{
			if( is_executable( "$init" ))
			if( $st <> 'stop' )
				doCommand( "$init stop", $log );

			chmod( $init, 0755 );
			doCommand( "$init disable", $log );
		}
		removeMod( $mod, $log );
		getPagesMsgs();

	}
	elseif( $act == 'install' )
	{
		if ( installMod( $mod, $log ) )
		{
			doAction( $mod, 'enable', $log );
			doAction( $mod, 'start', $log );
			getPagesMsgs();
		}
	}
	elseif( $act == 'update' )
	{
		// store status, activity
		if( $st <> 'disable' )
		{
			if(( $s = $opts['config_before'] ) <> '' )
				doCommand( "$init $s", $log );

			// stopping, disable module
			doAction( $mod, 'disable', $log );
		}

		// store backup
		if(( $bc = $opts['backup'] ) <> '' )
		{
			doCommand( "tar -cf /tmp/backup_$mod.tar -C $mos/ $bc", $log );
			$bc = "backup_$mod.tar";
		}

		// update package
		if( doCommand( "$mos/bin/pm update $mod", $log ) == 0 )
		// restore backup
		if( $bc <> '' ) 
		{
			doCommand( "tar -xf /tmp/$bc -C $mos/", $log );
			doCommand( "rm -Rf /tmp/$bc", $log );
		}
		// restore status
		if( $st <> 'disable' )
		{
			doAction( $mod, 'enable', $log );
			if( $st <> 'stop' ) doAction( $mod, 'start', $log );
			getPagesMsgs();
		}
		else if( $haveInit ) chmod( $init, 0644 );
	}
	else
	if( strpos( $opts['actions'], $act ) !== false )
	 if( $haveInit )
	  if( is_executable( "$init" )) doCommand( "$init ".$act, $log );
}

// ------------------------------------

function core_actions ( $act, $log )
{
global $mos;

	if( $act == 'getrep' )
	{
		doCommand( "$mos/bin/pm updatelist", $log );
	}
	if( $act == 'update_all' )
	{
		doCommand( "$mos/bin/pm updatelist", $log );

		$updates = getUpdates();
		foreach( $updates as $mod => $item )
		{
			doAction( $mod, 'update', $log );
		}
	}
	else
	{
		if( isset( $_REQUEST[ 'mod' ] ))
			doAction( $_REQUEST[ 'mod' ], $act, $log );
	}
}

?>