<?php

require("modules/status/sajax.php");

function get_process_info()
{
global $mos;

	if( file_exists( "$mos/bin/top" ))
	{
		exec("$mos/bin/top -b", $result);
	}
	else exec("top -b", $result);
	return implode("\n", $result);
}

sajax_init();
$sajax_remote_uri = '?page=statgetprocs';
sajax_export("get_process_info");

// ------------------------------------
function statgetprocs_content()
{
	sajax_handle_client_request();
}

// ------------------------------------
function processes_head()
{

?>
<link rel="stylesheet" href="/modules/status/status.css" type="text/css" media="screen" charset="utf-8">
<script type="text/javascript" src="/modules/status/javascript/processes.js"></script>
<?php

}

// ------------------------------------
function processes_body()
{
global $mos;
/*
require("modules/status/sajax.php");

function get_process_info() {
	exec("top -b", $result);
	return implode("\n", $result);
}

sajax_init();
sajax_export("get_process_info");
sajax_handle_client_request();
*/
?>
<script type="text/javascript">
<?php sajax_show_javascript();?>
</script>

<div id="container">
<h3><?= getMsg( 'statProcesses' ) ?></h3>
<pre><textarea id="procinfo" name="procinfo" class="listcontent" cols="80" rows="24" readonly="readonly"><?=htmlspecialchars(get_process_info());?></textarea></pre>
</div>
<?php

}

?>
