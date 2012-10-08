<?php
require_once("downloady.php");

$namechop = 30;
$refresh = 5;

$d = new downloady;

$sid = (isset($_REQUEST['sid']) ? $_REQUEST['sid']: NULL);
$cmd = strtolower(isset($_REQUEST['action']) ? $_REQUEST['action']: NULL);


function HumanReadableSize($size) {
   $filesizename = array(" Bytes", " KB", " MB", " GB", " TB", " PB", " EB", " ZB", " YB");
   return round($size/pow(1024, ($i = floor(log($size, 1024)))), 2) . $filesizename[$i];
}


function ShortenName($name, $maxlength) { // needs improvements
	if (strlen($name) <= $maxlength) return($name);

	$extension = substr($name,-10,10); // last 10 chars.
	if (($pointer = strrpos($extension,".")) !== FALSE) {
		$extension = substr($extension,$pointer);
		}
	else {
		$extension = "";
		}
	return (substr($name,0,$maxlength)."...".$extension);
}


if (!empty($sid)) {
	$running = $d->GetStats('is_running', $sid);

	switch ($cmd) {
		case 'done': 
			print ((int)(!$running && $d->RemoveJob($sid)));
			exit(0);
			break;

		case 'pause':
			if (!$running) {
				print(0); exit(0);
				}
			$d->PauseJob($sid);
			$running = $d->GetStats('is_running', $sid, false);
			break;

		case 'resume':
			if ($running) {
				print(0); exit(0);
				}
			$d->Resume($sid);
			$running = $d->GetStats('is_running', $sid, false);
			break;

		case "trash":
			if ($running) {
				print(0); exit(0);
				}
			$d->RemoveFile($sid);
			break;

		case "log":
			readfile($d->GetStatfile($sid));
			die();
		}

	$details = $d->GetDetails($sid);
	$size = safe_number_format($details['size']);
	$exists = file_exists($details['savefile']);

	switch ($cmd) {
		case 'get':
			if ($details && $details['done']) {

				if (!function_exists('mime_content_type')) {
				   function mime_content_type($f) {
				       $f = escapeshellarg($f);
				       return trim( `file -bi $f` );
					   }
					}

				if ($exists) {
					header('Content-type: '.mime_content_type($details['savefile']));
					header('Content-disposition: attachment; filename="'.basename($details['savefile']).'"');
					header('Content-length: '.sprintf('%u',filesize($details['savefile'])));
					readfile($details['savefile']);
					}
				}
			die();
		case 'info':
			$pid = $d->GetStats('pid', $sid);
			print("{$sid}\t{$details['url']}\t{$details['savefile']}\t{$size}\t{$pid}");
			die();
		}

	$name = ShortenName(basename($details['savefile']), $namechop);

	// print stats
	print("{$sid}\t{$name}\t{$size}\t{$details['speed']}\t{$details['percent']}\t");
	print("{$details['percent']}% ({$details['fetched']})\t");
	print("{$details['done']}\t{$running}\t{$exists}");
	}
else {
	if( $cmd == 'add' )
	{
		$url = (isset($_REQUEST['url'])? $_REQUEST['url'] : NULL);
		if($url) {
			$url_parts = parse_url($url);

			switch($url_parts['scheme']) { // Make sure the URL is valid
				case "http":
				case "https":
				case "ftp":
					require_once("downloady.php");
					$d = new downloady;
					$d->AddURL($url);
			}
		}
		header ( 'Location: dstatus.htm' );
		exit;
	}

	$list = $d->GetJobList();
	$any_running = false;

	foreach($list as $sid) {
		$running = $d->GetStats('is_running', $sid);
		$any_running = $any_running || $running;
		$details = $d->GetDetails($sid);
		$name = ShortenName(basename($details['savefile']), $namechop);
		$size = safe_number_format($details['size']);
		$exists = file_exists($details['savefile']);
//		$eta = $details['eta'];

		print("{$sid}\t{$name}\t{$size}\t{$details['speed']}\t{$details['percent']}\t");
		print("{$details['percent']}% ({$details['fetched']})\t");
		print("{$details['done']}\t{$running}\t{$exists}\n");
	}

	$usage = $d->GetDiskUsage();
	if (!$any_running) $refresh *= 4;
	print("{$refresh}\t{$usage['percent']}\t{$usage['percent']}% (".HumanReadableSize($usage['free'])." free)");
	}

?>