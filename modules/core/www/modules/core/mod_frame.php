<?php
function frame_session()
{
global $mos;
global $nav_title;

	if ( isset( $_GET['mod'] ) )
	{
		$mod = $_GET['mod'];

		$mods = array ();
		$mods = parse_ini_file( $mos.'/etc/pm/installed', true );

		$nav_title = $mods[ $mod ]['navy_title'];
	}
}

function frame_body()
{
global $mos_url;
global $nav_modules;

	if ( isset( $_GET['mod'] ) )
	{
		$mod = $_GET['mod'];
		if( isset( $nav_modules[$mod]['navy_frame'] ))
		{
			$src = $nav_modules[$mod]['navy_frame'];
			$src = str_replace("%addr%", $_SERVER["SERVER_ADDR"], $src);
			$src = str_replace("%host%", 'http://'.$_SERVER["HTTP_HOST"].'/', $src);
			$src = str_replace("%mos%", $mos_url, $src);

?>
<iframe class="cont_frame" src="<?php echo $src; ?>" width="100%" height="100%" scrolling="auto" frameborder="0">
</iframe>
<?php
		}
	}
}

?>