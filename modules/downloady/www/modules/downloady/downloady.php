<?php

function my_file_put_contents($filename, $data, $append = false) {
	$fp = fopen($filename, $append ? 'ab' : 'wb');
	chmod($filename, 0600); // This file may contain passwords
	fwrite($fp, $data."\n");
	fclose($fp);
	}

function safe_number_format($val, $precision = 0) {
  if(is_numeric($val)) {
    return(number_format($val, $precision, '.', ' '));
  }
  else return($val);
}

class downloady {
	// Configuration
	
	// This is where the downloads and status files go. Make sure this directory exists and is WRITABLE by the webserver process!
	var $destdir = '/tmp/usbmounts/sda1/download';
	var $tmpdir = '/tmp/usbmounts/sda1/download/.tmp';
	var $logfile = '/tmp/usbmounts/sda1/download/.tmp/downloady.log';
                       
	// Path and name of your server's Wget-compatible binary
	var $wget = '/usr/local/etc/mos/bin/wget';
	
	// Extra options to Wget go here. man wget for details.
	var $wgetoptions = '--continue --user-agent="MyBrowse/1.1 (GS/OS 6.0.5; AppleIIgs)" --tries="10" --random-wait --waitretry="10"'; //--limit-rate="25k" 

	var $stats_cache = Array();

	function downloady() {
		if (!file_exists($this->tmpdir)) @mkdir($this->tmpdir, 0700); // attempt to create; it may fail...
		if (!file_exists($this->destdir)) @mkdir($this->destdir, 0700); // attempt to create; it may fail...

/*		$stat = stat($this->tmpdir);
		if ($stat['mode'] & 0007) {
			print("WARNING: {$this->tmpdir} is publicly accessible! This is a security risk, as temporary files may contain passwords.<br>");
		}
*/	}


	function GetDiskUsage() {
		$res = Array();
		$res['total'] = disk_total_space($this->destdir);
		$res['free'] = disk_free_space($this->destdir);
		$res['used'] = $res['total'] - $res['free'];
		$res['percent'] = safe_number_format(100 * $res['used'] / $res['total'], 2);
		return($res);
	}


	function GetJobList() {
		$res = Array();
		foreach(glob("{$this->tmpdir}/*.stat") as $filename)
			if($filename) $res[] = basename($filename, ".stat");
		return($res);
	}


	function GetStats($what, $sid = '', $cache = true) {
		if ($cache)
			if(!empty($this->stats_cache[$what.$sid])) return($this->stats_cache[$what.$sid]);

		switch($what) {
			case 'pid':
				$pidfile = "{$this->tmpdir}/{$sid}.pid";
				$res = file_exists($pidfile) ? (int)file_get_contents($pidfile) : -1;
				break;
			case 'is_running':
				$pid = $this->GetStats('pid', $sid, $cache);
				$res = intval(`ps axopid,command |grep -v grep |grep {$pid} |grep -c wget`);
				break;
			}
		return($this->stats_cache[$what.$sid] = $res);
	}


	function AddURL($url) {
		$url = trim($url);
		$sid = md5($url);

		$urlfile = "{$this->tmpdir}/{$sid}.url";
		$statfile = "{$this->tmpdir}/{$sid}.stat";
		$pidfile = "{$this->tmpdir}/{$sid}.pid";
		
		my_file_put_contents($urlfile, $url);
		my_file_put_contents($this->logfile, $url, true);

		$safe_urlfile = escapeshellarg($urlfile);
		$safe_url = escapeshellarg($url);
		$safe_destdir = escapeshellarg($this->destdir);
		$safe_statfile = escapeshellarg($statfile);

		exec("{$this->wget} {$this->wgetoptions} --referer={$safe_url} --background --input-file={$safe_urlfile} --progress=dot --directory-prefix={$safe_destdir} --output-file={$safe_statfile}", $output);
		preg_match('/[0-9]+/', $output[0], $output);

		my_file_put_contents($pidfile, $output[0]);
		return(true);
	}


	function RemoveFile($sid) {
		if($this->GetStats('is_running', $sid)) return(false);
		$details = $this->GetDetails($sid);
		if (file_exists($details['savefile']))
			unlink($details['savefile']);
		return(true);
	}


	function PauseJob($sid) {
		if(!@is_file("{$this->tmpdir}/{$sid}.stat")) return(false);
		return ($this->GetStats('is_running', $sid) && exec('kill -15 '.$this->GetStats('pid', $sid)));
	}


	function Resume($sid) {
		if($this->GetStats('is_running', $sid)) return(false);

		$details = $this->GetDetails($sid);
		if($details['done'] && file_exists($details['savefile'])) return(false);
		return($this->AddURL($details['url']));
	}


	function RemoveJob($sid) {
		@unlink("{$this->tmpdir}/{$sid}.url");
		@unlink("{$this->tmpdir}/{$sid}.stat");
		@unlink("{$this->tmpdir}/{$sid}.pid");
		return(true);
	}

	function GetStatfile($sid) {
		return("{$this->tmpdir}/{$sid}.stat");
	}

	function GetDetails($sid, $verbose = false) {
		$statfile = "{$this->tmpdir}/{$sid}.stat";
		if(!@is_file($statfile)) return(false);

		$fp = fopen($statfile, 'rb');
		$res = Array(
						'done' => 0,
						'url' => '',
						'savefile' => '',
						'size' => '- Unknown -',
						'percent' => '- Unknown -',
						'fetched' => 0,
						'speed' => 0,
						'eta' => 'n/a'
					);

		$log = Array();
		$count = 0;

		while(!feof($fp)) {
			$count++;
			$line = fgets($fp, 2048); // read a line

			if($count == 1) { // URL
				if(preg_match('/^--[0-9\-\s:]+?-- (.+)$/i', $line, $regs)){ // url
					$res['url'] = trim($regs[1]);
					}
				} 
			elseif(preg_match("/^\S+: ([0-9,\s]+)\(/", $line, $regs)){ // Length
				$res['size'] = str_replace( array(' ', ','), '', $regs[1]);
				}
			elseif(preg_match("/^\s*=> [`'\"](.*?)[`'\"]$/i", $line, $regs)){ // Destination file
				$res['savefile'] = $regs[1];
				}
			elseif(preg_match("/^[^:]+: [`'\"](.*?)[`'\"]$/i", $line, $regs)){ // Destination file on newer wget
				$res['savefile'] = $regs[1];
				}
			elseif(preg_match("/^\s*([0-9]+[kmgte]) [,. ]{54}\s*([0-9]{1,3}%)?\s+([0-9.,]+\s*[kmgte](?:\/s)?)\s*([0-9dhms]*)/i", $line, $regs)){
				$res['fetched'] = $regs[1];
				$res['percent'] = floatval($regs[2]);
				$res['speed'] = $regs[3];
				$res['eta'] = $regs[4];
				}
			elseif(preg_match("/^.*?\(([^)]+)[^']+ saved \[([^\]]+)]$/i", $line, $regs)){
				$res['fetched'] = $regs[2];
				$res['percent'] = 100;
				$res['speed'] = $regs[1];
				}
			elseif(preg_match("/ --[0-9:]+--/i", $line, $regs)){
				$res['done'] = 1;
			}
		}
		fclose($fp);

		//$res['exists'] = $res['savefile'] && file_exists($res['savefile']);
		
		return $res;
	}
}
?>
