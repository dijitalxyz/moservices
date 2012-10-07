<?php

function info_head()
{

?>
<link rel="stylesheet" href="modules/core/css/info.css" type="text/css" media="screen" charset="utf-8">
<script type="text/javascript">
function look(list, topic)
{
	plist=document.getElementById(list);
	ptopic=document.getElementById(topic);
	if(plist.style.display == "none")
	{
		plist.style.display = "block";
		ptopic.style.background = "#ccc no-repeat right top url(modules/core/images/btn_hide.png)";
	}
	else {
		plist.style.display = "none";
		ptopic.style.background = "#ccc no-repeat right top url(modules/core/images/btn_show.png)";
	}
}
</script>
<?php

}

function info_body()
{
	$id = 1;
	function showInfo ( $cmd )
	{
	global $id;

?>
<div class="info_frame">
<a href="#" onclick="look('l_<?= $id ?>','t_<?= $id ?>')"><div id="t_<?= $id ?>" class="info_topic"><?= $cmd ?></div></a>
<div id="l_<?= $id ?>" class="info_list"><pre>
<?php

		exec( $cmd, $lines );
		foreach( $lines as $s )	echo "$s\n";
		$id += 1;

?>
</pre></div></div><br />
<?php
	}
?>
<div id="container">
<h3><?= getMsg( 'coreInfo' ) ?></h3>
<?php

	showInfo ( "/bin/uname -a"	);
	showInfo ( "cat /proc/cpuinfo"	);
	showInfo ( "cat /proc/meminfo"	);
	showInfo ( "/sbin/lsmod"	);
	showInfo ( "/bin/ps -w"		);
	showInfo ( "/bin/dmesg"		);

	showInfo ( "/bin/busybox"	);

	showInfo ( "cat /proc/mounts"	);
	showInfo ( "/bin/df -h"		);
	showInfo ( "/sbin/fdisk -l"	);

	showInfo ( "/sbin/ifconfig"	);
	showInfo ( "/sbin/route"	);
	showInfo ( "cat /etc/resolv.conf" );

	showInfo ( "set" );

	echo "</div>\n";
}
?>