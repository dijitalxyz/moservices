<?php
//
// ------------------------------------
function update_all_actions( $act, $log )
{
	if( $act == 'prepare' )
	{
		core_actions( 'getrep', $log );
	}
	elseif( $act == 'update' )
	{

		$updates = getUpdates();
		foreach( $updates as $mod => $item )
		{
			doAction( $mod, 'update', $log );
		}
	}
}
//
// ------------------------------------
function update_all_body()
{

?>
<script type="text/javascript">
<?php

	$act = $_REQUEST['act'];
	$ret = $_REQUEST['ret'];

	$url = '/';
	if( isset( $ret )) $url = '?page='.$ret;


	if( $act == 'prepare' )
	{
		$updates = getUpdates();

		if( count( $updates ) == 0 )
		{

?>
	alert("<?= getMsg( 'coreUpdNothing' ) ?>");
	window.location.href='<?= $url ?>';
<?php
		}
		else
		{
			$msg = getMsg( 'coreUpdMods' ).'\n\n';
			foreach( $updates as $mod => $item )
			{
				$msg .= "'". $item['title'] ."' rev ". $item['revision']. ' -> rev '. $item['_arev'].'\n';
			}

			$urlYes = '?page=update_all&act=update';
			if( isset( $ret )) $urlYes .= '&ret='.$ret;

?>
	isConfirmed = confirm("<?= $msg ?>");
	if(isConfirmed)
	{
		window.location.href = '<?= $urlYes ?>';
	}
	else
	{
		window.location.href='<?= $url ?>';
	}
<?php
		}
	}
	else
	{

?>
	window.location.href = '<?= $url ?>'; 
<?php
	}

?>
</script>
<?php

}

?>