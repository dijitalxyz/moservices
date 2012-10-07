<?
header('Content-type: video/mp4');
$parser = isset($_GET['parser']) ? $_GET['parser'] : "2";
$parser=urldecode ($parser);
$parser = str_replace ('&amp;','&',$parser);
$s=file_get_contents( $parser );

$pos1 = strpos($s, "var video_host = '",0);
$pos2 = strpos($s, "';", $pos1);
$reply1 = substr ($s,$pos1+18,$pos2-$pos1-18);

$pos3 = strpos($s, "var video_uid = '",$pos2);
$pos4 = strpos($s, "';", $pos3);
$reply2 = substr ($s,$pos3+17,$pos4-$pos3-17);

$pos5 = strpos($s, "var video_vtag = '",$pos4);
$pos6 = strpos($s, "';", $pos5);
$reply3 = substr ($s,$pos5+18,$pos6-$pos5-18);

$pos7 = strpos($s, "var video_max_hd = '",$pos6);
$pos8 = strpos($s, "';", $pos7);
$reply4 = substr ($s,$pos7+20,$pos8-$pos7-20);

$res = '240';
			if     ($reply4 >= 3) { $res = '720'; }
			elseif ($reply4 >= 2) { $res = '480'; }
			elseif ($reply4 >= 1) { $res = '360'; }



			$url = $reply1 .'u'. $reply2 .'/video/'. $reply3 .'.'. $res .'.mp4';


header ("Location: ".$url);

?>
