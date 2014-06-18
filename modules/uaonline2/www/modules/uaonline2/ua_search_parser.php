<?php
/*	------------------------------
	Ukraine online services 	
	Global search module v1.0
	------------------------------
	Created by Sashunya 2013	
	wall9e@gmail.com			
	------------------------------ */
include ("ua_exua_parser.php");
include ("ua_fsua_parser.php");
include ("ua_uakino_parser.php");
if (isset($_GET['search_global']))
{
	$search = $_GET['search_global'];
	$head= $search;
	$search=urlencode($search);
	if (isset($_GET['page_global']))
	{
		$page = $_GET['page_global'];
	} else $page=1;
	$ex_res=exuaSearch(urldecode($search),0,$page,true);
	$fs_res=fsua_search($search,$page);
	$uakino_res=uakino_search($search,$page,true);
	$uakino_res=$uakino_res["links"];
	$count_ex=count($ex_res);
	$count_fs=count($fs_res);
	$count_uakino=count($uakino_res);
	$max_count=max($count_ex,$count_fs,$count_uakino);
	$temps = '';
	$videocount=0;
	for ($i=0; $i<=$max_count-1; $i++)
	{
		if ($i<=$count_ex-1)
		{
			if ($ex_res[$i]["type"]=='folder') {$link =$ua_path_link.$exua_rss_list_filename."?view=".$ex_res[$i]["link"]; $type="list";}
			else {$link = $ua_path_link.$exua_rss_link_filename."?file=".$ex_res[$i]["link"]."&image=".$ex_res[$i]["image"]; $type="link";}
			$temps.= $ex_res[$i]["title"]."\n".$link."\n".$ex_res[$i]["image"]."\n".$ex_res[$i]["link"]."\n".$type."\n0\n";
			$videocount++;
		}
		if ($i<=$count_fs-1)
		{
			$fav=$fs_res[$i]["link"]; 
			$link=$ua_path_link.$fsua_parser_filename."?file=".urlencode($fs_res[$i]["link"]."?ajax&folder")."&img=".$fs_res[$i]["image"];
			$temps.= $fs_res[$i]["title"]."\n".$link."\n".$fs_res[$i]["image"]."\n".$fav."?ajax&folder"."\nlist\n2\n";		
			$videocount++;
		}
		if ($i<=$count_uakino-1)
		{
			if ($uakino_res[$i]["type"]=='list') 
			{
				$link =$ua_path_link.$uakino_rss_list_filename."?view=".$uakino_res[$i]["link"];
				$preview='http://uakino.net'.$uakino_res[$i]["image"];
				$type="list";
			}
			else
			{
				$link = $ua_path_link.$uakino_rss_link_filename."?file=".$uakino_res[$i]["link"]."&image=".$uakino_res[$i]["image"];
				$type="link";
			}
			$temps.= $uakino_res[$i]["title"]."\n".$link."\n".$uakino_res[$i]["image"]."\n".$uakino_res[$i]["link"]."\n".$type."\n3\n";
			$videocount++;
		}
		
	}
	$temps = "ПОИСК:".$head."\n".$videocount."\n".$temps;
	file_put_contents( $tmp, $temps );
	echo $tmp;
}
?>