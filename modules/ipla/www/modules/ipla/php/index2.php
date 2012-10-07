<?php
/*  xIpla - index2.php
 *  Copyright (C) 2010 ToM/UD
 *
 *  This Program is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2, or (at your option)
 *  any later version.
 *
 *  This Program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with flvstreamer; see the file COPYING.  If not, write to
 *  the Free Software Foundation, 675 Mass Ave, Cambridge, MA 02139, USA.
 *  http://www.gnu.org/copyleft/gpl.html
 *
 */
   
	include_once "config.php";

	include_once "IplaInterface.php";

	include_once "MainTemplate.php";
	include_once "CategoriesTemplate.php";
	include_once "VodsTemplate.php";
	include_once "ChannelsTemplate.php";
	include_once "EpgTemplate.php";

	
	$action = $_GET['action'];
	
	$iplaInterface = new IplaInterface();
	
	if ($action == "search")
	{

		$keywords = $_GET['keywords'];
		
		if (strlen($keywords) > 0)
		{
			
			$iplaInterface->GetVodsSearch($keywords);
			
			if (count($iplaInterface->vods) > 0 )
			{

			$title = "Szukaj: " . $keywords;
		
				$template = new VodsTemplate();
				$template->CreateTemplate($iplaInterface->vods, $title, 0, 0, 0);
				$template->ShowTemplate();
				
				$iplaInterface->Close();
				return;
				
			}
			
			$action = "";
			
		}
		else $action = ""; 

	}

	if ($action == "searchsql")
	{

		$keywords = $_GET['keywords'];
		$id = $_GET['id'];
		$pt = base64_decode($_GET['pt']);
		$sort = $_GET['sort'];

		$iplaInterface->GetVodsSearchSQL($keywords,$id, $sort);
		
		$template = new VodsTemplate();
		$template->CreateTemplate($iplaInterface->vods, $pt, $id, $sort, 2);
		$template->ShowTemplate();

	}
	
	
	if ($action == "rec")
	{
		
		$iplaInterface->GetVodsRecommendations();
			
		if (count($iplaInterface->vods) > 0 )
		{

			$template = new VodsTemplate();
			$template->CreateTemplate($iplaInterface->vods, "Polecane", 0, 0, 0);
			$template->ShowTemplate();
			
			$iplaInterface->Close();
			return;
		
		}
		
		$action = "";
		
	}
	
	if ($action == "epg")
	{
		
		$id = $_GET['id'];
		$title = base64_decode($_GET['title']);
		
		$iplaInterface->GetVodsEpg($id);

		if (count($iplaInterface->vods) > 0 )
		{
			
			$template = new EpgTemplate();
			$template->CreateTemplate($iplaInterface->vods, $title);
			$template->ShowTemplate();
			
			$iplaInterface->Close();
			return;
			
		}
		
		$action = "cha";
	
	}

	if ($action == "cha")
	{

		$iplaInterface->GetChannelsList();

		if (count($iplaInterface->channels) > 0 )
		{
		
			$template = new ChannelsTemplate();
			$template->CreateTemplate($iplaInterface->channels, $iplaInterface);
			$template->ShowTemplate();
			
			$iplaInterface->Close();
			return;
		
		}
		
		$action = "";
		
	}
	
	if ($action == "cat")
	{

		$id = $_GET['id'];
		$pt = base64_decode($_GET['pt']);

		$iplaInterface->GetCategoriesList($id);
		
		if (count($iplaInterface->categories) > 0 )
		{

			$template = new CategoriesTemplate();
			$template->CreateTemplate($iplaInterface->categories, $pt, $id);
			$template->ShowTemplate();
			
		}
		else 
		{

			$iplaInterface->GetVodsList($id, 4);

			if (count($iplaInterface->vods) >= VODS_SEARCH_ENABLE) $se = 2;
			else $se = 1;
			
			$template = new VodsTemplate();
			$template->CreateTemplate($iplaInterface->vods, $pt, $id, 4, $se);
			$template->ShowTemplate();

		}
	
	}
	elseif ($action == "mov")
	{

		$id = $_GET['id'];
		$pt = base64_decode($_GET['pt']);
		$sort = $_GET['sort'];
		
		$iplaInterface->GetVodsList($id, $sort);

		if (count($iplaInterface->vods) >= VODS_SEARCH_ENABLE) $se = 2;
		else $se = 1;
		
		$template = new VodsTemplate();
		$template->CreateTemplate($iplaInterface->vods, $pt, $id, $sort, $se);
		$template->ShowTemplate();
	
	}
	else
	{
		
		$iplaInterface->GetCategoriesList(0);
		
		$template = new MainTemplate();
		$template->CreateTemplate($iplaInterface->categories);
		$template->ShowTemplate();

	}
	
	$iplaInterface->Close();
	
?>