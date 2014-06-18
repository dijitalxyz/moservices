<?php

$trans_dir = dirname( __FILE__ );
require_once( $trans_dir . '/TransmissionRPC.class.php' );

$rpc = new TransmissionRPC('http://localhost:9091/transmission/rpc', 'torrent', '1234');
//$rpc->username = 'torrent';
//$rpc->password = '1234';

if( isset( $_REQUEST['debug']))
{
	$rpc->debug = true;
	header( "Content-type: text/plain" );
}

// ------------------------------------
// Actions
//

try
{
	if( isset( $_REQUEST['act'] ))
	{
		$act = $_REQUEST['act'];
		if( $act == 'resume_all' )
		{
			$ids = array();
			$result = $rpc->start( $ids );
		}
		elseif( $act == 'pause_all' )
		{
			$ids = array();
			$result = $rpc->stop( $ids );
		}
		elseif( $act == 'alt_speed' )
		{
			if( isset( $_REQUEST['disable'] ))
				$sets = array( 'alt-speed-enabled' => 0 );
			else
				$sets = array( 'alt-speed-enabled' => 1 );

			$result = $rpc->sset( $sets );
		}
		else
		if( isset( $_REQUEST['id'] ))
		{
			$id = $_REQUEST['id'];
			$ids = array( intval( $id ) );

			if( $act == 'resume' )
			{
				$result = $rpc->start( $ids );
			}
			elseif( $act == 'pause' )
			{
				$result = $rpc->stop( $ids );
			}
			elseif( $act == 'delete' )
			{
				$result = $rpc->remove( $ids, false );
			}
			elseif( $act == 'remove' )
			{
				$result = $rpc->remove( $ids, true );
			}
			elseif( $act == 'anno' )
			{
				$result = $rpc->reannounce( $ids );
			}
			elseif( $act == 'check' )
			{
				$result = $rpc->verify( $ids );
			}
			elseif( $act == 'high' )
			{
				$result = $rpc->set( $ids, array( 'priority-high' ) );
			}
			elseif( $act == 'normal' )
			{
				$result = $rpc->set( $ids, array( 'priority-normal' ) );
			}
			elseif( $act == 'low' )
			{
				$result = $rpc->set( $ids, array( 'priority-low' ) );
			}
		}
	}
} catch (Exception $e)
{
	die('[ERROR] ' . $e->getMessage() . PHP_EOL);
}

// ------------------------------------
//
function getPeers( $peers_active, $peers_total, $s )
{
	if( ! isset( $peers_total )) return '';
	return ' '.$s.sprintf("%01u",$peers_active).getMsg( 'transOf' ).sprintf("%01u",$peers_total);
}

// ------------------------------------
// List of torrents
//

header( "Content-type: text/plain" );

try
{

  $ids = array();
  $fields = array(
/*
 'activityDate',
 'addedDate',
 'bandwidthPriority',
 'comment',
 'corruptEver',
 'creator',
 'dateCreated',
 'desiredAvailable',
 'doneDate',
 'downloadDir',
 'downloadedEver',
 'downloadLimit',
 'downloadLimited',
 'error',
 'errorString',
 'eta',
 'hashString',
 'haveUnchecked',
 'haveValid',
 'honorsSessionLimits',
 'id',
 'isFinished',
 'isPrivate',
 'leftUntilDone',
 'magnetLink',
 'manualAnnounceTime',
 'maxConnectedPeers',
 'metadataPercentComplete',
 'name',
 'peer',
 'peersConnected',
 'peersGettingFromUs',
 'peersSendingToUs',
 'percentDone',
 'pieces',
 'pieceCount',
 'pieceSize',
 'rateDownload',
 'rateUpload',
 'recheckProgress',
 'secondsDownloading',
 'secondsSeeding',
 'seedIdleLimit',
 'seedIdleMode',
 'seedRatioLimit',
 'seedRatioMode',
 'sizeWhenDone',
 'startDate',
 'status',
 'totalSize',
 'torrentFile',
 'uploadedEver',
 'uploadLimit',
 'uploadLimited',
 'uploadRatio',
 'webseedsSendingToUs',
*/
	'id',
	'name',
	'status',

	'peersConnected',
	'peersGettingFromUs',
	'peersSendingToUs',

	'percentDone',
	'recheckProgress',

	'uploadRatio',
	'uploadedEver',

	'seedRatioMode',
	'seedRatioLimit',
        'isFinished',

	'rateDownload',
	'rateUpload',

	'haveValid',
	'totalSize'
/*
	'id',
	'addedDate',
        'name',
        'totalSize',

        'error',
        'errorString',
        'eta',
        'isFinished',
        'isStalled',
        'leftUntilDone',
        'metadataPercentComplete',
        'peersConnected',
        'peersGettingFromUs',
        'peersSendingToUs',
        'percentDone',
        'queuePosition',
        'rateDownload',
        'rateUpload',
        'recheckProgress',
        'seedRatioMode',
        'seedRatioLimit',
        'sizeWhenDone',
        'status',
        'downloadDir',
        'uploadedEver',
        'uploadRatio',
        'webseedsSendingToUs'
*/
);

  $result = $rpc->get( $ids, $fields );
  
} catch (Exception $e) {
  die('[ERROR] ' . $e->getMessage() . PHP_EOL);
}

if( isset( $_REQUEST['debug']))
{
	print "result\n";
	print_r( $result );
}

$sput = count( $result->arguments->torrents ) . PHP_EOL;


foreach( $result->arguments->torrents as $item )
{
	// status
	$id = $item->id;
	$st = 0;
	if( isset( $item->status )) $st = $item->status;

	$progress_bg = $trans_dir . '/images/progress_white.png';
	$progress_ratio = 0;

	if( $st == 1 )		// check_wait
	{
		$item_left = getMsg( 'transWaitCheck' );
		$progress_bar = $trans_dir . '/images/progress_grey.png';
	}
	elseif( $st == 2 )		// check
	{
		$item_left = getMsg( 'transChecking' );
		if( isset( $item->recheckProgress ))
		$item_left .= ' - '.round( $item->recheckProgress *100, 1 ).'%';

		$progress_bg  = $trans_dir . '/images/progress_red.png';
		$progress_bar = $trans_dir . '/images/progress_deep_green.png';
		$progress_ratio = round( $item->recheckProgress*100, 2 );
	}
	elseif( $st == 3 )	// download_wait
	{
		$item_left = getMsg( 'transWaitDownload' );
		$progress_bar = $trans_dir . '/images/progress_grey.png';
	}
	elseif( $st == 5 )	// seed_wait
	{
		$item_left = getMsg( 'transWaitSeed' );
		$progress_bar = $trans_dir . '/images/progress_grey.png';
	}
	elseif( $st == 4 )		// download
	{
		$item_left = getMsg('transDownload') . getPeers( $item->peersSendingToUs, $item->peersConnected, getMsg( 'transFrom' ) );
		$progress_bar = $trans_dir . '/images/progress_blue.png';
	}
	elseif( $st == 6 )			// seeding
	{
		$item_left = getMsg( 'transSeeding' ) . getPeers( $item->peersGettingFromUs, $item->peersConnected, getMsg( 'transTo' ) );
		$progress_bg = $trans_dir . '/images/progress_green.png';
		$progress_bar = $trans_dir . '/images/progress_deep_green.png';
	}
	elseif( $st == 0 )				// paused
	{
		if( $item->isFinished )
			$item_left = getMsg( 'transFinished' );
		else
			$item_left = getMsg( 'transPaused' );
		$progress_bar = $trans_dir . '/images/progress_grey.png';
	}
	else						// stopped
	{
		$item_left = getMsg( 'transUnknown' );
		$progress_bar = $trans_dir . '/images/progress_grey.png';
	}
	// DL & UL
	$s = '';
	if( isset( $item->rateDownload ))
		$s .= getMsg( 'transDL' ) . getHumanValue( $item->rateDownload ) . getMsg( 'transPSec' );
	if( isset( $item->rateUpload ))
		$s .= getMsg( 'transUL' ) . getHumanValue( $item->rateUpload ) . getMsg( 'transPSec' );
	if( $s <> '' ) $item_left .= ' -'.$s;


	$item_right = '';

	if( isset( $item->totalSize ))
	{
		$ts = $item->totalSize;
		$vs = 0;
		if( isset( $item->haveValid )) $vs = $item->haveValid;

		if( $ts !== $vs )
		{
			// incomplete
			$item_right .= getHumanValue( $vs ).getMsg( 'transOf' ).getHumanValue( $ts );
			if( isset( $item->percentDone ))
			{
				$item_right .= ' ('.sprintf( '%01.2f', $item->percentDone*100).'%)';
				$progress_ratio = round( $item->percentDone*100, 4 );
			}
			if( isset( $item->rateDownload ))
				$item_right .= ' - '. getHumanPeriod( ($ts-$vs)/$item->rateDownload ).getMsg( 'transRemain' );
		}
		else
		{
			// complete
			$progress_ratio =100;
			$item_right .= getHumanValue( $ts );
			if( isset( $item->uploadedEver ))
				$item_right .= getMsg( 'transUploaded' ).getHumanValue( $item->uploadedEver );
			if( isset( $item->uploadRatio )) {
				$item_right .= getMsg( 'transRatio' ).sprintf( '%01.2f', $item->uploadRatio ).')';
				// seeding ratio
				if( isset( $item->seedRatioLimit ) && $item->seedRatioMode && $st == 6 ) {
					$srl = $item->seedRatioLimit;
					if( $srl > 0 ) {
						$progress_ratio = round( $item->uploadRatio * 100 / $srl, 2 );
						// $item_right .= " SRL=$srl"; // debug
					}
				}
			}
			else
				$progress_ratio =0;
		}
	}

	$url = getMosUrl() ."?page=rss_trans_actions&id=$id";

	$sput .= $item->name . PHP_EOL;
	$sput .= $item_left . PHP_EOL;
	$sput .= $item_right . PHP_EOL;
	$sput .= $progress_bg . PHP_EOL;
	$sput .= $progress_bar . PHP_EOL;
	$sput .= $progress_ratio . PHP_EOL;
	$sput .= $url. PHP_EOL;
}

if( isset( $_REQUEST['debug']))
{
	echo $sput;
}
else
{
	file_put_contents( '/tmp/put.dat', $sput );
	echo "/tmp/put.dat";
}

exit;

?>