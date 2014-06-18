<?php

function addFavorite( $path )
{
#	exec( 'echo path='.$path.' >> /tmp/a.txt' );
	// get drive
	exec( 'cat /proc/mounts', $a );
	$a = implode( "\n", $a );
	$path = preg_quote( $path );
	$path = preg_replace( '/([\s\'])/', '\\\\${1}', $path );

if( isset( $_REQUEST['debug'] )) echo "path=$path\n";

	if( ! ($s = realpath( $path )) ) {
	    $s = trim( `/usr/local/etc/mos/bin/realpath $path` );
	}
	$realpath=$s;

if( isset( $_REQUEST['debug'] )) echo "realpath=$realpath\n";

	if( $s == '' ) return false;
	do {
		$pathDev = dirname( $s );
		if( strpos( $a, ' '. $pathDev .' ' ) > 0 ) break;
		$s = $pathDev;
	} while( $s != '/' );

	if( $pathDev == '/' ) return false;

if( isset( $_REQUEST['debug'] )) echo "pathDev=$pathDev\n";

	# get type of drive
	$s = exec( "cat /proc/mounts | grep ' $pathDev ' | cut -d' ' -f1" );
	if( $s == '' ) return false;
	$s = str_replace( '/dev/', '', $s );
	$s = exec( "echo \$( ls -l /dev | grep $s ) | cut -d' ' -f5-6" );
	if( $s == '' ) return false;
	$a = preg_replace( '/\,\s+/', ':', $s );

if( isset( $_REQUEST['debug'] )) echo "s=$s a=$a\n";

	if( ! file_exists( '/sys/dev/block/'. $a .'/uevent' )) return false;

	$s = trim( file_get_contents( '/sys/dev/block/'. $a .'/uevent' ));
	if( strpos( $s, '/usb' ) )
	    $typeDev = 1;
	elseif( strpos( $s, '/sata' ) )
	    $typeDev = 2;
	else
	    return false;

if( isset( $_REQUEST['debug'] )) echo "typeDev=$typeDev\n";

	# get URL file
	$favUrl = str_replace( $pathDev, '', $realpath );

if( isset( $_REQUEST['debug'] )) echo "favUrl =$favUrl\n";

	# get type file
	if( substr( $path, -1, 1 ) == '/' )
	{
		$favUrl .= '/';
		$favType = 4;
	}
	else
	{
		$a = pathinfo( $path );
		if( in_array( $a['extension'], array( 'jpg', 'jpeg', 'bmp', 'png', 'gif', 'tif', 'tiff'))) $favType = 1;
		elseif( in_array( $a['extension'], array( 'trp', 'mp4', 'mov', 'avi', 'asf', 'wmv', 'mkv', 'flv', 'ts', 'mts', 'm2ts', 'dat', 'mpg', 'vob', 'iso'))) $favType = 2;
		elseif( in_array( $a['extension'], array( 'mp3', 'wav', 'aac', 'ogg', 'flac', 'aiff'))) $favType = 3;
		else return false;
	}

if( isset( $_REQUEST['debug'] )) echo "favType=$favType\n";

	# find FavLink file
	$favName = $pathDev .'/FavLink_';
	if( $typeDev == 1 ) $favName .= 'USB';
	else $favName .= 'HDD';

	if( file_exists( $favName ))
	{
		$s = file_get_contents ( $favName );

		if( strpos( $s, '<Link />' ) )
		{
			# empty link
			$s = str_replace( '    <Link />', '    <Link>
    </Link>', $s );
		}
		else
		{
			# remove previous trans items
			$s = preg_replace( '/\s*<Item module="trans">.*?<\/Item>/s', '', $s );
		}

		# add new item
		$s = str_replace( '    </Link>', '        <Item module="trans">
            <Hash>'. md5( $favUrl) .'</Hash>
            <Type>'. $favType .'</Type>
            <Timestamp>'. time() .'</Timestamp>
            <URL>'. $favUrl .'</URL>
        </Item>
    </Link>' , $s );

	}
	else
	{
		# make new favorites file
		$s = '<Storage>
    <Device>'. $typeDev .'</Device>
    <Link>
        <Item>
            <Hash>'. md5( $favUrl) .'</Hash>
            <Type>'. $favType .'</Type>
            <Timestamp>'. time() .'</Timestamp>
            <URL>'. $favUrl .'</URL>
        </Item>
    </Link>
</Storage>
';

	}

if( isset( $_REQUEST['debug'] )) echo "favName =$favName\n$s";

	file_put_contents( $favName, $s );

	return true;
}

function rss_trans_actions_content()
{
	if( ! isset( $_REQUEST['id'] )) return;
	$id = $_REQUEST['id'];

	$trans_dir = dirname( __FILE__ );
	require_once( $trans_dir . '/TransmissionRPC.class.php' );

	$rpc = new TransmissionRPC('http://localhost:9091/transmission/rpc', 'torrent', '1234');
	//$rpc->username = 'torrent';
	//$rpc->password = '1234';


if( isset( $_REQUEST['debug'] ))
{
	$rpc->debug = true;
	header( "Content-type: text/plain" );
}
	try
	{
		$ids = array( intval( $id ) );
		$fields = array(
			'id',
			'status',
			'name',

			'downloadDir',
			'percentDone',
			'isFinished',
			'files',
		);

		$result = $rpc->get( $ids, $fields );
  
	} catch (Exception $e)
	{
		die('[ERROR] ' . $e->getMessage() . PHP_EOL);
	}

if( isset( $_REQUEST['debug'] ))
{
	print "GET\n";
	print_r( $result );
}
	if( count( $result->arguments->torrents ) == 0 ) return;

	if( isset ( $result->arguments->torrents[0]->status ))
	{ $st = $result->arguments->torrents[0]->status; }
	else $st = 0;

	// --------------------------
	// send RSS
	//
	include( 'modules/core/rss_view_popup.php' );


	class rssTransPopup extends  rssSkinPopupView
	{
		function showOnUserInput()
		{

?>
    <onUserInput>
	ret = "false";
	userInput = currentUserInput();
	if (userInput == "enter"  || userInput == "right")
	{
		idx = getFocusItemIndex();
		url = getItemInfo( idx, "link" );
		act = getItemInfo( idx, "action" );
		if( act == "rss" )
		{
			url = doModalRss(url);
		}
		setReturnString( url );
		postMessage( "return" );
		ret = "true";
	}
	else
	if (userInput == "left" || userInput == "right")
	{
		ret = "true";
	}
	ret;
    </onUserInput>
<?php
		}
	}

	$view = new rssTransPopup;

	$view->topTitle = $result->arguments->torrents[0]->name;


	if( $result->arguments->torrents[0]->percentDone == 1 )
	if( file_exists( '/usr/local/bin/scripts/myfavorites_list.rss' ))
	{
		$l = $result->arguments->torrents[0]->downloadDir .'/';
		if( count( $result->arguments->torrents[0]->files ) > 1 )
		 // folder
		 $l .= $result->arguments->torrents[0]->name .'/';
		else
		 // file
		 $l .= $result->arguments->torrents[0]->files[0]->name;

		if( addFavorite( $l ) )
		 $view->items[] = array(
				'title'	=> getMsg( 'transOpen' ),
				'action'=> 'ret',
				'link'	=> 'open'
		 );
	}

	if( $st == 0 )
	{
		$view->items[] = array(
			'title'	=> getMsg( 'transResume' ),
			'action'=> 'ret',
			'link'	=> getMosUrl()."?page=xml_trans&amp;id=$id&amp;act=resume"
		);
	}
	else
	{
		$view->items[] = array(
			'title'	=> getMsg( 'transPause' ),
			'action'=> 'ret',
			'link'	=> getMosUrl()."?page=xml_trans&amp;id=$id&amp;act=pause"
		);
		
		$view->items[] = array(
			'title'	=> getMsg( 'transHigh' ),
			'action'=> 'ret',
			'link'	=> getMosUrl()."?page=xml_trans&amp;id=$id&amp;act=high"
		);
		
		$view->items[] = array(
			'title'	=> getMsg( 'transNormal' ),
			'action'=> 'ret',
			'link'	=> getMosUrl()."?page=xml_trans&amp;id=$id&amp;act=normal"
		);
		
		$view->items[] = array(
			'title'	=> getMsg( 'transLow' ),
			'action'=> 'ret',
			'link'	=> getMosUrl()."?page=xml_trans&amp;id=$id&amp;act=low"
		);
	}

	$view->items[] = array(
		'title'	=> getMsg( 'transDelete' ),
		'action'=> 'ret',
		'link'	=> getMosUrl()."?page=xml_trans&amp;id=$id&amp;act=delete"
	);

	$view->items[] = array(
		'title'	=> getMsg( 'transRemove' ),
		'action'=> 'ret',
		'link'	=> getMosUrl()."?page=xml_trans&amp;id=$id&amp;act=remove"
	);

	$view->items[] = array(
		'title'	=> getMsg( 'transAnno' ),
		'action'=> 'ret',
		'link'	=> getMosUrl()."?page=xml_trans&amp;id=$id&amp;act=anno"
	);

	$view->items[] = array(
		'title'	=> getMsg( 'transCheck' ),
		'action'=> 'ret',
		'link'	=> getMosUrl()."?page=xml_trans&amp;id=$id&amp;act=check"
	);

	$view->items[] = array(
		'title'	=> getMsg( 'coreCmCancel' ),
		'action'=> 'ret',
		'link'	=> ''
	);

	$view->showRss();
}

?>