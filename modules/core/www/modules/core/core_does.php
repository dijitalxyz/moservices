<?php

$act = $_REQUEST[ 'do' ];
// ------------------------------------

if( $act == "reboot" )
{
	exec( 'reboot' );
}
// ------------------------------------

elseif( $act == "chlng" )
{
	if( isset( $_REQUEST[ 'lang' ] ) )
	{
		$f = $mos.'/etc/core.ini';
		$l = $_REQUEST[ 'lang' ];
		$cl = $nav_options['language'];
		if( $cl == '' )
		{
			exec( "echo \"language = $l\" >> $f" );
		}
		else
		{
			exec( 'sed -ir "s/^([ \t]*language[ \t]*=[ \t]*).*$/\1'.$l.'/" '.$f );
		}
		$nav_lang = $l;
		$nav_options['language'] = $l;
	}
}
// ------------------------------------

elseif( $act == "backup" )
{

	$b_name = '/tmp/mos_backup.tar';
	$b_dir = '/tmp/backup';

	exec( "rm -f $b_name" );
	exec( "rm -Rf $b_dir" );

	mkdir( $b_dir );

	loadOptions();

	$smods = '';
	foreach( $nav_modules as $mod => $item )
	{
		// store status, activity
		$st = $item['_status'];
		if( isset( $item['config_before'] ))
		if( $st <> 'disable' )
			exec( "$mos/etc/init/".$item['init'].' '.$item['config_before'] );

		// store backup
		if(( $bc = $item['backup'] ) <> '' )
		{
			exec( "tar -cf $b_dir/$mod.tar -C $mos/ $bc" );
			$bc = "$mod.tar";
		}
		$smods .= "[$mod]\nstatus = $st\n";
		if( $bc <> '' )	$smods .= "backup = \"$bc\"\n";
	}
	file_put_contents( "$b_dir/mos_backup.ini", $smods  );
	exec( "tar -cf $b_name -C /tmp/ backup/" );
	exec( "rm -Rf $b_dir" );

	if( file_exists( $b_name ) )
	{
		$d = exec( 'date +%Y-%m-%d' );
		// send file
		header( "Content-type: application/x-tar" );
		header( "Content-Lenght: ".filesize( $b_name ) );
		header( "Content-Disposition: attachment; filename=\"mos_backup_$d.tar\"" );
		readfile( $b_name );
		unlink( $b_name );

		exit;
	}
}
// ------------------------------------

elseif( $act == "restore" )
{
	if( ! isset( $_FILES['backup']['tmp_name'] )) return;

	$b_dir = '/tmp/backup';

	$b_name = $_FILES['backup']['tmp_name'];
	exec( "tar -xf $b_name -C /tmp/" );
	unlink( $b_name );

	loadOptions();

	$mods = array();
	$mods = parse_ini_file( "$b_dir/mos_backup.ini", true );

	foreach( $mods as $mod => $item )
	{
		$st  = $item['status'];
		$bc  = $item['backup'];

		// install or just stop
		if( isset( $nav_modules[ $mod ] ))
		{
			$is = true;
			if(( $ast = $nav_modules[ $mod ]['_status'] ) <> 'disable' )
			if( $nav_modules[ $mod ]['role'] <> 'core' )
				doAction( $mod, 'stop', false );
		}
		else
		{
			if( $is = installMod( $mod, false ) )
			$ast = 'disable';
		}
		if( ! $is ) continue;

		// untar backup
		if( $bc <> '' ) exec( "tar -xf $b_dir/$bc -C $mos/" );

		// restore status
		if( $st == 'disable' )
		{
			doAction( $mod, 'disable' );
		}
		else
		{
			if( $ast = 'disable' ) doAction( $mod, 'enable' );

			if( $st <> 'stop' )
			if( $nav_modules[ $mod ]['role'] <> 'core' )
				doAction( $mod, 'start' );
		}
	}

	// Remove not exists
	foreach( $nav_modules as $mod => $item )
	{
		if( ! array_key_exists( $mod, $mods )) doAction( $mod, 'remove' );
	}
	exec( "rm -Rf $b_dir" );
}
// ------------------------------------

elseif( $act == "editexport" )
{
	if( ! isset( $_REQUEST[ 'mod' ] )) return;
	if( ! isset( $_REQUEST[ 'edit_text' ] )) return;

	$mod = $_REQUEST[ 'mod' ];
	$s = $_REQUEST[ 'edit_text' ];

	if( get_magic_quotes_gpc() )
		$s = stripslashes( $s );

	$s = str_replace( "\r", '', $s );

	$m = array ();
	$m = parse_ini_file( $mos.'/etc/pm/installed', true );
	$opts = $m[ $mod ];
	if( isset( $opts['config_edit'] ))
	{
		$conf = "$mos/".$opts['config_edit'];
		file_put_contents( $conf, $s );

		// send file
		header( 'Content-type: application/octet-stream' );
		header( 'Content-Lenght: '.filesize( $conf ) );
		header( 'Content-Disposition: attachment; filename="'.basename( $conf ).'"' );
		readfile( $conf );
		exit;
	}
}

?>