<?php
/*	------------------------------
	Ukraine online services 	
	history files loading module 1.0
	------------------------------
	Created by Sashunya 2012	
	wall9e@gmail.com			
	Some code was used from 
	Farvoice  & others 
	------------------------------ */
	global $ua_history_main_filename;
	global $confs_path;
	?>
		historyFilmsArray = null;
		historyFilesListArray = null;
		historyTitleArray = null;
		historySite = null;
		historyPoster = null;
		histFilmsCount = 0;
		currentHistFile = "";
		dlok = readStringFromFile( "<?=$ua_history_main_filename?>" );
		if (dlok != null)
			{
				c = 0;
				histFilmsCount = getStringArrayAt(dlok, c); c += 1;
				count = 0;
				while( count != histFilmsCount )
					{
						hFilms = getStringArrayAt(dlok, c);
						historyFilmsArray = pushBackStringArray(historyFilmsArray, hFilms);  c += 1;
						hFilesList = getStringArrayAt(dlok, c);
						historyFilesListArray = pushBackStringArray(historyFilesListArray, hFilesList);  c += 1;
						historyTitleArray = pushBackStringArray(historyTitleArray, getStringArrayAt(dlok, c));  c += 1;
						historyPoster = pushBackStringArray(historyPoster, getStringArrayAt(dlok, c));  c += 1;
						historySite = pushBackStringArray(historySite, getStringArrayAt(dlok, c));  c += 1;
						count += 1;
						
						if ( hFilms == param )
						{
							currentHistFile = hFilesList+".conf";
						}
					}

			}
		histCount = 0;
		historyFilesArray = null;
		historyElapsedArray = null;
		if (currentHistFile != "")
		{
			dlok = readStringFromFile( "<?=$confs_path."ua_history/"?>" + currentHistFile);	
			if (dlok != null)
				{
					c = 0;
					histCount = getStringArrayAt(dlok, c); c += 1;
					count = 0;
					while( count != histCount )
						{
							historyFilesArray = pushBackStringArray(historyFilesArray, getStringArrayAt(dlok, c)); c += 1; 
							historyElapsedArray = pushBackStringArray(historyElapsedArray, getStringArrayAt(dlok, c)); c += 1; 
							count += 1;
						}
				}
		}
		
	<?
?>