<?php

$fws  = array();
$fmem = 0;


// ------------------------------------
function analizeSkin( $w, $m, $s )
{
global $mos;
global $fws;

		$fws[ $m ]['skins'][ $s ]['status'] = 'install';

		if( file_exists( "$w$m/$m.$s.ini" ))
		{
			$a = array();
			$a = parse_ini_file( "$w$m/$m.$s.ini", false );
			$fws[ $m ]['skins'][ $s ]['title'] = $a['title'];
			$fws[ $m ]['skins'][ $s ]['irev' ] = $a['revision'];
			$fws[ $m ]['skins'][ $s ]['ilen' ] = $a['size']*1024;
		}
		else
		{
			$fws[ $m ]['skins'][ $s ]['title'] = $s;
			$fws[ $m ]['skins'][ $s ]['irev' ] = 1;
			$fws[ $m ]['skins'][ $s ]['ilen' ] = filesize($f);
		}
		if( ! file_exists( "$mos/www/modules/sskin/skins/$m.$s.png"))
		 if( file_exists( "$w$m/$m.$s.png"))
		  exec( "cp -a $w$m/$m.$s.png $mos/www/modules/sskin/skins/" );

		$fws[ $m ]['count'] += 1;
}
// ------------------------------------
function getSkinsList()
{
global $mos;
global $fws;
global $fmem;

	$cpu_id   = file_get_contents( '/sys/realtek_boards/cpu_id' );
	$board_id = file_get_contents( '/sys/realtek_boards/board_id' );

	$s = exec( 'free | grep "Mem:"' );
	$s = preg_replace( '| +|', ' ', $s );
	$a = explode( " ", $s);
	$ram = $a[1];

	$srev = trim( @file_get_contents( '/etc/system_svn_version' ));

	$fws  = array();

	// make list of fws and skins
	$f = $mos.'/www/modules/sskin/skins/skins.ini';
	if( file_exists( $f ))
	{
		// load avaiable skins

		$ss = array();
		$ss = parse_ini_file( $f, true );

		foreach( $ss as $skin => $item )
		{
			if( isset( $item['cpu'] )
			&& strpos( $item['cpu'], $cpu_id ) === false ) continue; 

			if( isset( $item['board'] )
			&& strpos( $item['board'], $board_id ) === false ) continue; 

			if( isset( $item['ram'] )
			&& $item['ram'] > $ram ) continue; 

			if( isset( $item['system'] )
			&& $item['system'] > $srev ) continue; 

			if( $item['role'] == 'fw' )
			{
				$fws[ $skin ]['title'] = $item['title'];
				$fws[ $skin ]['arev']  = $item['revision'];
				$fws[ $skin ]['alen']  = $item['size']*1024;
				$fws[ $skin ]['status']= 'noinstall';
			}
			else
			{
				$fw = $item['fw'];

				if( ! isset( $fws[ $fw ]['status'] )) $fws[ $fw ]['status']= 'disable';

				$a = explode( '_', $skin);
				$fws[ $fw ]['skins'][ $a[1] ] = array(
					'title' => $item['title'],
					'arev'  => $item['revision'],
					'alen'  => $item['size']*1024,
					'status'=> 'noinstall'
				);
			}
		}
	}

	// find installed skins
	$w = '/usr/share/bin/';
	$d = opendir( $w );
	while ( $m = readdir( $d ) )
	{
		if(( $m == '.' )or( $m == '..' )) continue;
		if( ! is_dir( $w.$m )) continue;

		$fws[ $m ]['status'] = 'install';
		$fws[ $m ]['count'] = 0;

		if( file_exists( "$w$m/$m.ini" ))
		{
			$a = array();
			$a = parse_ini_file( "$w$m/$m.ini", false );
			$fws[ $m ]['title' ] = $a['title'];
			$fws[ $m ]['irev'  ] = $a['revision'];
			$fws[ $m ]['ilen'  ] = $a['size']*1024;
		}
		else
		{
			$title = $m;
			if( file_exists( $w.$m.'/fw_desc.txt' ))
			 $title = file_get_contents( $w.$m.'/fw_desc.txt' );

			$fws[ $m ]['title' ] = $title;
			$fws[ $m ]['irev'  ] = 1;

			// calculate size
			$a = exec( "echo $( du -s $w$m ) | cut -d' ' -f1" );
			$a -= exec( "echo $( du -s $w$m/res.* ) | cut -d' ' -f1" );

			$fws[ $m ]['ilen'] = $a*1024;
		}

		$g = glob( $w.$m.'/res.*.squash' );

		if( $g !== false && count( $g ) > 0 )
		foreach( $g as $f )
		{
			$a = explode( '.', basename( $f ));
			$s = $a[1];

			analizeSkin( $w, $m, $s );
		}
		else
		{
			// without resources
			analizeSkin( $w, $m, 'default' );
		}
	}

	// find active skin
	if( file_exists( "$w/boot_fw.conf" ))
	{
		$a = array();
		$a = parse_ini_file( "$w/boot_fw.conf", false );
		$fws[ $a['fw'] ]['status'] = 'active';
		$fws[ $a['fw'] ]['skins'][ $a['skin'] ]['status'] = 'active';
	}

	// calculate avaiable memory
	$a = exec( " echo $( df | grep -E '^/dev/root.*/$' )|cut -d' ' -f4" );
	$fmem = $a * 1024;

	// calculate real length of skin
	foreach( $fws as $fw => $pfw )
	{
		$stf  = $pfw[ 'status' ];

		if( $stf == 'disable' ) continue;

		if( empty( $pfw['skins'] ) || count( $pfw['skins'] ) == 0 )
		{
			// noskin fw
			$fws[ $fw ]['skins']['default'] = array(
				'title' => 'Default',
				'irev'  => $pfw['irev'],
				'ilen'  => 0,
				'status'=> $pfw['status'],
			);
			$pfw = $fws[ $fw ];
		}

		foreach( $pfw['skins'] as $skin => $pskin )
		{
			$sts  = $pskin[ 'status' ];

			if( $stf == 'noinstall' )
			{
				$len = $pfw['alen'] + $pskin['alen'];
			}
			elseif( $sts == 'noinstall' )
			{
				$len = $pskin['alen'];
			}
			elseif( $pfw['count'] > 1 )
			{
				$len = $pskin['ilen'];
			}
			else
			{
				$len = $pfw['ilen'] + $pskin['ilen'];
			}
			$fws[ $fw ]['skins'][ $skin ]['len' ] = $len;
		}
	}
}
//
// ------------------------------------
function sskin_actions( $act, $log )
{
global $mos;

	if( $act == 'getrep' )
	{
		// download
		if( doCommand( "$mos/bin/pm get skins", $log ) == 0 )
		{
			// extract
			doCommand( "tar xjf /tmp/skins.tar.bz2 -C $mos/www/modules/sskin/skins/", $log );
			doCommand( 'rm /tmp/skins.tar.bz2', $log );
		}
		return;
	}

	if( ! isset( $_REQUEST['fw'] )) return;

	$fw = $_REQUEST['fw'];

	if( isset( $_REQUEST['skin'] ))
	{
		// Skin actions
		$skin = $_REQUEST['skin'];

		if( $act == 'select' )
		{
			if( $log )
			{
?>
<script type="text/javascript">
	window.location.href='/';
</script>
<?php
			}
			doCommand( "$mos/bin/sskin change $fw $skin", false );
		}
		if( $act == 'delete' )
		{
			doCommand( "$mos/bin/sskin delete $fw $skin", $log );
		}
		if( $act == 'update' )
		{
			doCommand( "$mos/bin/sskin update $fw $skin", $log );
		}
		if( $act == 'install' )
		{
			doCommand( "$mos/bin/sskin get $fw $skin", $log );
		}
	}
	else
	{
		// FW actions

		if( $act == 'delete' )
		{
			doCommand( "$mos/bin/sskin delete $fw", $log );
		}
		if( $act == 'update' )
		{
			doCommand( "$mos/bin/sskin update $fw", $log );
		}
	}
}

function xml_sskin_actions( $act, $log )
{
	sskin_actions( $act, $log );
}

// ------------------------------------
function sskin_head()
{

?>
<link rel="stylesheet" href="modules/sskin/sskin.css" type="text/css" media="screen" charset="utf-8">
<link rel="stylesheet" href="modules/core/css/toolbar.css" type="text/css" media="screen" charset="utf-8">
<?php

}
// ------------------------------------

function showHtmlSkinsList()
{
global $fws;
global $fmem;

?>
<div id="container">

<table class="ssk_top" border="0" cellspacing="0" cellpadding="0">
<tr><td><h3><?= getMsg('sskinTitle') ?></h3></td>
<td width="20">&nbsp;</td>
<td>
<div class="mod_toolbar">
<a class="mod_button" href="?page=sskin&act=getrep" title="<?= getMsg( 'coreCmUpdList') ?>">
<img src="modules/core/images/btn_page_refresh.png" /></a>
<a class="mod_button" href="?page=sskin_sets" title="<?= getMsg( 'coreSettings') ?>">
<img src="modules/core/images/btn_settings.png" /></a>
</div></td>
</tr></table>

<div class="memory">
<b><?= getMsg('coreFreeMem').getHumanValue( $fmem ) ?></b>
</div>
<?php

if( isset( $_REQUEST['debug'] ))
{
	echo "<pre>";
	print_r( $fws );
	echo "</pre>";
}

	ksort( $fws );

	foreach( $fws as $fw => $pfw )
	{
		$stf  = $pfw[ 'status' ];

		if( $stf == 'disable' ) continue;

?>
<div class="ssk_fw">
<div class="ssk_fw_topic"><?= $pfw['title'] ?></div>
<div class="ssk_fw_list<?= ' ssk_st_'.$stf ?>">
<?php

		ksort( $pfw['skins'] );

		foreach( $pfw['skins'] as $skin => $pskin )
		{
			$sts  = $pskin[ 'status' ];

			$len = $pskin['len'];

			$menu = array();
			$menu["$fw.$skin"] = array (
				'type'	=> 'node',
				'title'	=> $pskin['title'],
				'items'	=> array()
			);

			if( $sts == 'noinstall' )
			{
				if( $len < $fmem )
				 $menu["$fw.$skin"]['items']['install'] = array (
					'type'	=> 'item',
					'title' => getMsg( 'coreCm_install' ),
					'url'	=> "?page=sskin&fw=$fw&skin=$skin&act=install"
				);
			}
			else
			{
				if( $sts != 'active' )
				{
					$menu["$fw.$skin"]['items']['select'] = array (
						'type'	=> 'item',
						'title' => getMsg( 'sskinSelect' ),
						'url'	=> "?page=sskin&fw=$fw&skin=$skin&act=select"
					);

					$url = "?page=sskin&act=delete&fw=$fw";
					if( $pfw['count'] > 1 )
					 $url .= "&skin=$skin";

					$menu["$fw.$skin"]['items']['delete'] = array (
						'type'	=> 'item',
						'title' => getMsg( 'coreCm_delete' ),
						'url'	=> $url
					);
				}

				if( ( isset( $pskin['arev'] ))
				  &&( $pskin['arev'] <> $pskin['irev'] )
				  &&( ( $pskin['alen'] - $pskin['ilen'] ) < $fmem )
				)
				 $menu["$fw.$skin"]['items']['update'] = array (
					'type'	=> 'item',
					'title' => getMsg( 'coreCm_update' ),
					'url'	=> "?page=sskin&fw=$fw&skin=$skin&act=update"
				);
			}

?>
<div class="ssk_card<?php

	echo  ' ssk_st_'.$sts;
	if(( count( $pfw['skins'] ) == 1 )||( $sts == 'active' )) echo  ' ssk_card_alone';

?>">
<table border="0" cellspacing="0" cellpadding="0" width="100%">
<tr valign="center">
<td colspan="2"><img src="modules/sskin/skins/<?= $fw.'.'.$skin ?>.png" /></td></tr>
<tr><td>
<?php

			drawRootMenu( $menu );

?>
</td>
<td align="right"><?php

			echo getHumanValue( $len );

?></td>
</tr></table>
<?php
			if( ( isset( $pskin['arev'] ))
			  &&( isset( $pskin['irev'] ))
			  &&( $pskin['arev'] <> $pskin['irev'] )
			  &&( ( $pskin['alen'] - $pskin['ilen'] ) < $fmem )
			)
			{
				echo "<div class=\"ssk_update\">\n";

//				if( $sts <> 'active' )
//				{
					echo "<a href=\"?page=sskin&fw=$fw&skin=$skin&act=update\" title=\"".getMsg( 'coreCm_update' )."\">".$pskin['arev']."</a>\n";
//				}
//				else echo $pskin['arev']."\n";
				echo "</div>\n";
			}

?>
</div>
<?php
		}

?>
</div>
<?php
		if( ( isset( $pfw['arev'] ))
		  &&( isset( $pfw['irev'] ))
		  &&( $pfw['arev'] <> $pfw['irev'] )
		  &&( ( $pfw['alen'] - $pfw['ilen'] ) < $fmem )
		)
		{
			echo "<div class=\"ssk_update\">\n";

//			if( $stf <> 'active' )
//			{
				echo "<a href=\"?page=sskin&fw=$fw&act=update\" title=\"".getMsg( 'coreCm_update' )."\">".$pfw['arev']."</a>\n";
//			}
//			else echo $pfw['arev']."\n";
			echo "</div>\n";
		}

?>
</div>
<?php
	}

?>
</div>
<?php

}

// ------------------------------------
function sskin_body()
{
	getSkinsList();
	showHtmlSkinsList();
}
//
// ====================================
function xml_sskin_content()
{
global $mos;
global $fws;
global $fmem;

	header( "Content-type: text/plain; charset=utf-8" );

	getSkinsList();

	$skins = array();

	foreach( $fws as $fw => $pfw )
	{
		if( $pfw['status'] == 'disable' ) continue;

		foreach( $pfw['skins'] as $skin => $pskin )
		{
			$skins["$fw.$skin"] = array(

				'skin'  => $skin,
				'fw'    => $fw,
				'title' => $pfw['title'],
			);
		}
	}

	ksort( $skins );

	$s = getMsg('sskinFreeMem') . getHumanValue( $fmem ) . PHP_EOL;
	$s .= count( $skins ) . PHP_EOL;

	foreach( $skins as $skin => $item )
	{
		$s .= $item['title'] . PHP_EOL;
		$s .= "$mos/www/modules/sskin/skins/$skin.png\n";
		$s .= getMosUrl().'?page=rss_sskin_actions&fw='.$item['fw'].'&skin='.$item['skin']. PHP_EOL;
	}

if( isset( $_REQUEST['debug'] ))
{
	echo "$s";
}
else
{
	file_put_contents( '/tmp/put.dat', $s );
	echo "/tmp/put.dat";
}
}
//
// ====================================
function rss_sskin_menu_content()
{
	include( 'modules/core/rss_view_left.php' );

	$view = new rssSkinLeftView;

	$view->items = array(
		0 => array(
			'title'	=> getMsg( 'coreCmUList' ),
			'action'=> 'ret',
			'link'	=> getMosUrl().'?page=xml_sskin&amp;act=getrep'
		),
	);

	$view->showRss();
}
//
// ====================================
function rss_sskin_actions_content()
{
global $mos;
global $fws;
global $fmem;

	if( ! isset( $_REQUEST['fw'] )) return;
	$fw = $_REQUEST['fw'];

	if( ! isset( $_REQUEST['skin'] )) return;
	$skin = $_REQUEST['skin'];

	getSkinsList();
	if( ! isset( $fws[ $fw ] )) return;
	if( ! isset( $fws[ $fw ]['skins'][ $skin ] )) return;

	$pfw = $fws[ $fw ];
	$pskin = $fws[ $fw ]['skins'][ $skin ];

	include( 'modules/core/rss_view_popup.php' );

	$view = new rssSkinPopupView;

	$view->topTitle = $pfw['title'].' '.$pskin['title'];

	$stf  = $pfw[ 'status' ];
	$sts  = $pskin[ 'status' ];
	$len = $pskin['len'];

	if( $sts == 'noinstall' )
	{
		if( $len < $fmem )
		 $view->items[] = array(
			'title'	=> getMsg( 'coreCm_install' ),
			'action'=> 'ret',
			'link'	=> getMosUrl()."?page=xml_sskin&amp;fw=$fw&amp;skin=$skin&amp;act=install"
		);
	}
	elseif( $sts != 'active' )
	{
		$view->items[] = array(
			'title'	=> getMsg( 'sskinSelect' ),
			'action'=> 'ret',
			'link'	=> getMosUrl()."?page=xml_sskin&amp;fw=$fw&amp;skin=$skin&amp;act=select"
		);

		$url = getMosUrl()."?page=xml_sskin&amp;act=delete&amp;fw=$fw";
		if( $pfw['count'] > 1 ) $url .= "&amp;skin=$skin";

		$view->items[] = array(
			'title'	=> getMsg( 'coreCm_delete' ),
			'action'=> 'ret',
			'link'	=> $url
		);

		if( ( isset( $pskin['arev'] ))
		  &&( $pskin['arev'] <> $pskin['irev'] )
		  &&( ( $pskin['alen'] - $pskin['ilen'] ) < $fmem )
		)
		 $view->items[] = array(
			'title'	=> getMsg( 'coreCm_update' ),
			'action'=> 'ret',
			'link'	=> getMosUrl()."?page=xml_sskin&amp;fw=$fw&amp;skin=$skin&amp;act=update"
		);
	}

	$view->items[] = array(
		'title'	=> getMsg( 'coreCmCancel' ),
		'action'=> 'ret',
		'link'	=> ''
	);

	$view->showRss();
}

?>