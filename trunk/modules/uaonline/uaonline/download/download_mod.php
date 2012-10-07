#!/tmp/www/cgi-bin/php
<?php
// ------------------------------
//	Ukraine online services 	
//	Download module 0.3 (PHP)	
//	Created by Sashunya 2011	
//	wall9e@gmail.com			
// ------------------------------


define("settings","/usr/local/etc/mos/uaonline/settings.conf");

if (file_exists(settings)) {
	$path=trim(file_get_contents(settings));
	if (substr($path, -1)<>"/") {
		$path.= "/";
		}
} else {$path="/tmp/usbmounts/sda1/";} 


define("logfile",$path."downlist");

// Starting download --------------------------------------------
if(isset($_GET["downloadlink"]) && isset($_GET["title"])  ) {
		$outputfile = "'".$path . $_GET["title"]."'";
		$name = $_GET["title"];
		$downloadfile = $_GET["downloadlink"];				
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
		// waiting for 3 seconds to close connection
                sleep(3);
		// starting download using wget with low priority
		$command ="nice -n 10 wget -c -O ".$outputfile." ".$downloadfile." > /dev/null 2>&1 & echo $!";
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
		    	

}
//--------------------------------------------------------


//Display downloads --------------------------------------
if (isset($_GET["display"]))  {
	if (file_exists(logfile)) {
		$temp= file_get_contents(logfile);
		$log = explode("\n",$temp);
		unset($log[sizeof($log)-1]);
		$pidcount = 0;
		// start preparing output data in XML format
		header( "Content-type: text/plain" );
		echo "<?xml version=\"1.0\" encoding=\"UTF8\" ?>\n";
		echo "<downloads>\n";
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
				if (file_exists($path.$pidfile)) {
				unset($op);
				exec('ls -l -1 '.$path, $op);
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
                                // outputing data in XML format
				?>
                
		                <file>
				<curlen><?=$str1 ?></curlen>
				<percent><?=$percent ?></percent>	 
                                <state><?=$pidstate ?></state>
				<name><?=$pidfile ?></name>
				<pid><?=$pid ?></pid>
				<filelink><?=$pidlink ?></filelink>
				<len><?=$pidlen ?></len>
				</file>
				<?php
		                
			$pidcount++;
		}
		echo "</downloads>\n";
	}

}

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
?>