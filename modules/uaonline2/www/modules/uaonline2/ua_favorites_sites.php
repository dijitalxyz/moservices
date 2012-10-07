<?php
function get_fav_site($site);
{
	switch ($sit)
	{
		case 0: {$fav_site=$ua_path_link.$exua_rss_list_filename;break;}
		case 1: {$fav_site=$ua_path_link.$exua_rss_link_filename;break;}
		case 2: {$fav_site=$ua_path_link.$filmy_rss_link_filename;break;}
		case 3: {$fav_site=$ua_path_link.$fsua_parser_filename;break;}
		case 4: {$fav_site=$ua_path_link2.$uaix_rss_list_filename;break;}
		case 5: $fav_site=$ua_path_link2.$uaix_rss_link_filename;
	}
	return $fav_site;
}
?>