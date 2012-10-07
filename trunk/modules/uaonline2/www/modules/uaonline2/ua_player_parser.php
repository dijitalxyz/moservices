<?php
include ("ua_paths.inc.php");

if(isset($_GET["player_style"])){
	if ($_GET["player_style"]=='req'){
	echo $player_style;
	}
	if ($_GET["player_style"]=='send')	{
		$ini = new TIniFileEx($confs_path.'ua_settings.conf');
		$ini->write('player','style',$_GET["player_style_val"]);
		$ini->updateFile();
		unset($ini);
	}

}



//ini_set("memory_limit","300M"); 
//set_time_limit(0);
//set_time_limit (0);
if (isset($_GET['file'])){
$file=$_GET['file'];
readfile($file);
}




//$file = "http://127.0.0.1/media/sda1/scripts/uaonline/test2.sh";
//$file = "/tmp/usbmounts/sda1/scripts/uaonline/test2.sh";
//$file = "exec /tmp/usbmounts/sda1/msdl -p http -o - -l \"/tmp/usbmounts/sda1/temp.log\" \"http://www.ex.ua/get/12182025\"";
//$file = "exec wget -O - \"http://www.ex.ua/get/12182025\"";


//readfile($file);
//passthru($file,$result);

//$header=get_headers ($file,1);
//$len = $header["Content-Length"];
	//set_time_limit(-1);
//header("Content-type: application/octet-stream\n");
//header("Content-Length: ".$len);
//readfile($file);

//        header('Cache-Control: max-age=86400'); // Cache for 24 hours
//       header("Content-type: application/octet-stream");
//header('Content-Disposition: inline; filename="'.$file.'"');
//      header('Content-transfer-encoding: binary');
//header("Connection: close");
//header('HTTP/1.1 200 OK');
//header('Date:  Sat, 2 Jul 2011 19:03:54 GMT');
//header('Server: filmy/0.1');
//header('Last-Modified: Fri, 1 Jul 2011 09:00:15 GMT');
//header('Accept-Range: bytes');
//header('Content-Length: 1467934720');
//header('Content-Type: video/x-msvideo');
//header("Connection: close");		
//$fp = fopen($file, 'rb');
//fpassthru($fp);
//readfile($file);
//passthru($file,$result);












	
/*$chain_size=1024*64;
$fd = fopen ($file, "rb");
$workFileSize = $len;
$full_part=(int)($workFileSize/$chain_size);
$patch_part=$workFileSize-$full_part*$chain_size;
while($full_part>=0)
{
    $contents = fread ($fd, $chain_size);
    echo $contents;
    $full_part=$full_part-1;
}
$contents = fread ($fd, $patch_part);
echo $contents;
        
fclose ($fd); 
*/	
/*
function download_file($file = NULL, $speed_limit = 1024, $resume = true, $send_errors = false){
//return:: 0 - ok \ 1 - $file is_null \ 2 - forbidden \ 3 - 404 Error
  if(is_null($file)){ 
    return 1;
  }else{
    $file_name = basename($file); 
    $speed_limit = intval($speed_limit); 
    if($speed_limit<0) $speed_limit = 1024; 

    $running_time = 0; 
    $begin_time = time(); 

    set_time_limit(300); 

    if(file_exists($file))
    { 
      if( false !== ($file_hand = fopen($file, "rb")) )
      { 
        $file_size = filesize($file); 
        $file_date = date("D, d M Y H:i:s T",filemtime($file));
        if(preg_match("/bytes=(\d+)-/", $_SERVER["HTTP_RANGE"],$range) && $resume == true)
        { 
          header("HTTP/1.1 206 Partial Content"); 
          $offset = $file_size - intval($range[1]); 
        }else{ 
          header("HTTP/1.1 200 OK"); 
          $offset = 0; 
        } 

        $data_start = $offset; 
        $data_end = $file_size - 1; 
        $etag = md5($file.$file_size.$file_date); 

        fseek($file_hand, $data_start); 

        header("Content-Disposition: attachment; filename=".$file_name); 
        header("Last-Modified: ".$file_date); 
        header("ETag: \"".$etag."\""); 
        if($resume == true) header("Accept-Ranges: bytes"); 
        header("Content-Length: ".($file_size-$data_start)); 
        header("Content-Range: bytes ".$data_start."-".$data_end."/".$file_size); 
        header("Content-type: application/octet-stream"); 

        while(!feof($file_hand) && (connection_status()==0))
        { 
          print fread($file_hand,$speed_limit); 
          flush(); 
          sleep(1); 
          $running_time = time() - $begin_time; 
          if($running_time>240)
          { 
            set_time_limit(300); 
            $begin_time = time(); 
          } 
        }
        fclose ($file_hand); 
        return 0; 
      }
      else
      {
        if($send_errors == true) header ("HTTP/1.0 403 Forbidden"); 
        return 2; 
      }
    }else{
      if($send_errors == true) header("HTTP/1.0 404 Not Found"); 
      return 3; 
    } 
  }
}  
download_file($file);
*/

?>