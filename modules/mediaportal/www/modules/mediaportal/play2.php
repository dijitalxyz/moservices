<?
function get_youtube($id,$quality = 'hi' ){
$opts=array( 'http'=>array( 'method'=>'GET', 'user-agent'=>'Mozilla/5.0 (Windows NT 5.1; rv:7.0) Gecko/20100101 Firefox/7.0'));
$context=stream_context_create($opts);
$s=file_get_contents("http://www.youtube.com/watch?v=$id",false,$context);
if(preg_match('/url_encoded_fmt_stream_map=(.*?)\&/s',$s,$ss)>0){
$ss=urldecode($ss[1]);
$ss=urldecode($ss).';';
$streams=array();
if(preg_match_all('/url=(.*?\&itag=(\d*).*?)[\;\,]/',$ss,$ss)>0)
foreach($ss[2]as$i=>$itag)
$streams[$itag]=$ss[1][$i];
//22-HD720
//18-MEDIUM(default)
if($quality=='hi' && isset($streams[22])) return $streams[22];
if(isset($streams[18])) return $streams[18];}
return false;
}
$parser=isset($_GET['parser']) ? $_GET['parser']:"2";
$parser=urldecode($parser);
$pos1=strpos($parser,"/v/",0);
$pos2=strpos($parser,"?",$pos1);
$parser=substr ($parser,$pos1+3,$pos2-$pos1-3);
if(($url=get_youtube($parser))!==false) header ("Location: ".$url);
?>