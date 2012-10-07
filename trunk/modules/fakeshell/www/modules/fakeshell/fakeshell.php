<?
class fakeShell {

	var $version;
	var $uname;
	var $path;
	var $menu;
	var $area;

	function fakeShell() {

		$this->version = "1.0.5.2";

		$this->uname = $this->getLinux();
		$this->path = $this->getPath();

		$this->menu['clear'] = "";
//		$this->menu['telnetd'] = "./busybox telnetd -l /bin/sh &";
		$this->menu['processes'] = "ps aux";

		$this->area = $_GET['area'];

		$this->pasteHeader();
		$this->pasteTabs();

		switch ($this->area) {
			case 'diskinfo':
				$this->pasteDiskInfo();
				break;
			default:
				$this->pasteShell();
				break;
		}

		$this->pasteFooter();
	}

	function getLinux() {
		@exec("uname -a", $uname);
		return $uname[0];
	}

	function getPath() {
		@exec("pwd", $path);
		return $path[0];
	}

	function pasteHeader() {
		print '<html>';
		print '<head>';
		print '<title>mavvy\'s fakeshell v'.$this->version.'</title>';
		print '<link rel="stylesheet" type="text/css" href="fakeshell.css">';
		print '</head>';
		print '<body>';
	}

	function pasteFooter() {
		print '</body>';
		print '</html>';
	}

	function pasteTabs() {
		print '<div id="tabs">';
		print '<ul>';

		switch ($this->area) {
			case 'diskinfo':
				print '<a href="'.$PHP_SELF.'?area=shell" style="padding-left:5px;">shell</a>';
				break;
			default:
				foreach ($this->menu as $k => $v) {
					print '<li><a href="#" onclick="document.shell.cmd.value=\''.$v.'\';document.shell.cmd.focus();">'.$k.'</a></li>';
				}
				print '<li><a href="#" onclick="document.shell.cmd.value=\'reboot\';document.shell.cmd.focus();">REBOOT</a></li>';
				print ' <a href="'.$PHP_SELF.'?area=diskinfo">diskinfo</a>';
				break;
		}

		print '</ul>';
		print '</div>';
	}

	function pasteShell() {
		print '<div id="shell">';
		print '<form name=shell method=post action="'.$PHP_SELF.'">';

		print '# <input type=text name=cmd size=80 value="';
		if(isset($_POST[cmd])) {
			print $_POST[cmd];
		}

		print '">';
		print '<input name="Submit" value="Send" type="submit">';
		print '<script> document.shell.cmd.focus(); </script></pre>';
		print '<pre>';

		if (!empty($_POST[cmd])) {
			system($_POST[cmd]);
		}

		print '</form>';
		print '</div>';
	}

	function stripSpaces($string) {
		while (strstr($string, "  ")) {
			$string = str_replace("  ", " ", $string);
		}
		return $string;
	}

	function pasteDiskInfo() {

		print '<div id="diskinfo">';

		@exec("df -h", $df);

		foreach ($df AS $k => $v) {
			if (strstr($v, "/tmp/usbmounts/")) {
				$line[$k] = $this->stripSpaces($v);
			}
		}

		foreach ($line AS $k => $v) {
			$token = explode(" ", $v);
			$disk_name[$k] = str_replace("/tmp/usbmounts/", "", $token[5]);
			$disk_total[$k] = $token[1];
			$disk_used[$k] = $token[4];
			$disk_free[$k] = $token[3];
		}

		print '<table width="300" cellspacing="0" cellpadding="0" border="0">';
		print '<tr>';
		print '<td>DISK</td>';
		print '<td>TOTAL</td>';
		print '<td>FREE</td>';
		print '</tr>';
		print '<tr>';
		print '<td colspan="3" height="10"></td>';
		print '</tr>';

		foreach ($disk_name AS $k => $v) {
			print '<tr>';
			print '<td width="100">'.$disk_name[$k].'</td>';
			print '<td width="100">'.$disk_total[$k].'</td>';
			print '<td>'.$disk_free[$k].'</td>';
			print '</tr>';
			print '<tr>';
			print '<td colspan="3" height="10" style="background-color:#800000;"><div style="background-color:#008000;width:'.((int)$disk_used[$k]*3).'px;height:10px;"></div></td>';
			print '</tr>';
			print '<tr>';
			print '<td colspan="3" height="10"></td>';
			print '</tr>';
		}
		print '</table>';

		print '</div>';
	}
}

$fakeshell = new fakeShell();
?>
