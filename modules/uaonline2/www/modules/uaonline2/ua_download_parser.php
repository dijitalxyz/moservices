<?php
/*	------------------------------
	Ukraine online services 	
	Download manager parser part module v1.0
	------------------------------
	Created by Sashunya 2011	
	wall9e@gmail.com			
	Some code was used from 
	Farvoice & others 
	------------------------------ */

include ("ua_paths.inc.php");
	
if (substr($download_path, -1)<>"/") {
		$download_path.= "/";
		}

define("logfile",$download_path.$download_log_filename);

function display()
{
global $tmp_down;
global $download_path;
if (file_exists(logfile)) {
		$temp= file_get_contents(logfile);
		$log = explode("\n",$temp);
		unset($log[sizeof($log)-1]);
		$pidcount = 0;
		$temps = '';
		// checking each item of logfile
        	foreach ($log as $value){
			$vrem = explode("#",$value);
			// making variables from log
			$pid = $vrem[0];
			$pidfile = $vrem[1];
			$pidlink = $vrem[2];
			$pidlen = $vrem[3];
			// cheking if download is going
	                unset($op);
			exec('ps | grep '.$pid, $op);	
			$n = false;
			foreach ($op as $value){
				if (stripos($value,$pid)>=0 and stripos($value,"wget")){
				$n = true;
					}
				}
				// getting size current downloads -----------------
				if (file_exists($download_path.$pidfile)) {
				unset($op);
				exec('ls -l -1 '.$download_path, $op);
				foreach ($op as $value){
					if (stripos($value,$pidfile)){$need=$value;}					
				}	
				//file_put_contents("/tmp/usbmounts/sda1/test2", $op); 
				$str1=substr($need,16);
				$i=0;
				while ($str1[$i]==' '){$i++;}
				$str1=substr($str1,$i);
				$str1=substr($str1,0,strpos($str1,' '));
				$percent = ceil((int)$str1*100/(int)$pidlen);
				
				// making state variable
				if ($n and (int)$str1<(int)$pidlen) {
					$pidstate = 'loading';
					
					}
				elseif (!$n and (int)$str1<(int)$pidlen) {
					$pidstate = 'stopped';
					
					}	
				elseif (!$n and (int)$str1=(int)$pidlen) {
					$pidstate = 'done';
					
					}		
				
				} else {
				$pidstate = 'none';
				$percent = '0';
				$str1 = '0';
				}
//-------------------------------------------------------------------------			
                // outputing data in txt format
				
				// $str1 - current length
				// $percent - %
				// $pidstate - loading, stopped, done, none
				// $pidfile - filename
				// $pid - PID
				// $pidlink - filelink 
				// $pidlen - lenght of downloading file

//				$temps .= $str1."\n".$percent."\n".$pidstate."\n".$pidfile."\n".$pid."\n".$pidlink."\n".$pidlen."\n";
				$temps .= $percent."\n".$pidstate."\n".$pidfile."\n".$pidlink."\n".$pid."\n";
				
			$pidcount++;
			
		}
    $temps = $pidcount."\n".$temps;
    file_put_contents($tmp_down, $temps );
	return $tmp_down;
	}
}

//Display downloads --------------------------------------
if (isset($_GET["display"]))  {
		echo display();
		exit;
}


// Starting download --------------------------------------------
if(isset($_GET["downloadlink"]) && isset($_GET["title"])  ) {
		$outputfile = "'".$download_path . $_GET["title"]."'";
		$name = $_GET["title"];
		$downloadfile = $_GET["downloadlink"];				
		//file_put_contents("/tmp/usbmounts/sda1/scripts/uaonline/temp.txt", $name."\n".$downloadfile."\n", FILE_APPEND | LOCK_EX);
		// checking is the current download is started if yes - exit
		$temp= file_get_contents(logfile);
		$log = explode("\n",$temp);
		$n=false;
		foreach ($log as $i=>$value){
			$vrem = explode("#",$value);
			$pid = $vrem[0];
			if ($downloadfile == $vrem[2]){
			unset($op);
			exec('ps | grep '.$pid, $op);	
			$n = false;
			foreach ($op as $value){
				if (stripos($value,$pid)>=0 and stripos($value,"wget")){
				$n = true;
				break;
					}
				}

			}
		}

		if (!$n) {
		// getting size of the download
		$header=get_headers ($downloadfile,1);
		$len = $header["Content-Length"];
		// waiting for 5 seconds to close connection
                sleep(5);
		// starting download using wget with low priority
		$command ="nice -n 10 ".$ua_wget_path."wget -c -O ".$outputfile." ".$downloadfile." > /dev/null 2>&1 & echo $!";
                unset($op);
		exec($command, $op);
        	// making logfile
		$pids=$op[0]."#".$name."#".$downloadfile."#".$len;
		$resume = false;
		if (file_exists( logfile )){
			$temp= file_get_contents(logfile);
			$log = explode("\n",$temp);
			foreach ($log as $i=>$value){
				$vrem = explode("#",$value);
				if ($name == $vrem[1] and $downloadfile == $vrem[2]){
					$log[$i]=$pids;
					$resume = true;	
				}
			}
			$log = implode ("\n",$log);
			file_put_contents(logfile, $log);
			}
		if (!$resume){file_put_contents(logfile, $pids."\n", FILE_APPEND | LOCK_EX);}	
        	}	
		    	
		echo display();
}
//--------------------------------------------------------




// stopping selected download ----------------------------------------------
if (isset($_GET["kill"])){
	unset($op);
	$killpid = $_GET["kill"];
	exec("kill ".$killpid, $op); 
}
// delete and stop selected download ---------------------------------------
if (isset($_GET["delete"])){
	$deletefile = $_GET["delete"];
	$temp= file_get_contents(logfile);
	$log = explode("\n",$temp);
	foreach ($log as $i=>$value){
		$vrem = explode("#",$value);
		if ($deletefile == $vrem[2]){
		unset($op);
		exec("kill ".$vrem[0], $op);
		unset ($log[$i]);
			}
		}
		$log = implode ("\n",$log);
		file_put_contents(logfile, $log);


}

// clear download list and stop all downloads -------------------------------
if (isset($_GET["clear"])){
if (file_exists(logfile)) {
file_put_contents(logfile, ''); 
unset($op);
exec("killall wget", $op);
}
}
if (isset($_GET["start_buffer"]))
{
	$downloadfile=$_GET["start_buffer"];
	file_put_contents("/tmp/usbmounts/sda1/temp.txt", $downloadfile, FILE_APPEND | LOCK_EX);
	$command ="nice -n 10 wget -O /tmp/usbmounts/sda1/temp.tmp '".$downloadfile."' > /dev/null 2>&1 & echo $!";
    unset($op);
	exec($command, $op);
	exec("ln -s /tmp/usbmounts/sda1/temp.tmp /tmp/www/temp.tmp", $op);
	echo $op[0];
	sleep(10);
}
if (isset($_GET["stop_buffer"]))
{
	exec("kill".$_GET["stop_buffer"], $op);
}
?>