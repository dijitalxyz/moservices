<?php
/*	------------------------------
	Ukraine online services 	
	history check routine V1.2
	------------------------------
	Created by Sashunya 2014	
	wall9e@gmail.com			
	Some code was used from 
	Farvoice  & others 
	------------------------------ */

global $confs_path;
global $ua_history_main_filename;
global $history_length;
global $ua_favorites_filename;
?>
				count = 0;
				found = 0;
				f_cnt = 0;
				curr = getStringArrayAt(downameArray,idx);
				if (checkElapsed == 0 &amp;&amp; playElapsed &gt; 0) 
				{
					currElapsed = playElapsed; 
				}
				else currElapsed =0;
				
				while( count != histCount )
					{
						hst=getStringArrayAt(historyFilesArray, count);
						if (hst == curr) 
						{
							found = 1;
							if (checkElapsed == 1) currElapsed = getStringArrayAt(historyElapsedArray,count);
							f_cnt = count;
							break;
						}
						count += 1;
					}
				if (checkElapsed == 0)
				{
					if (found == 1)
					{
						saveBookArray = null;
						saveBookArray = pushBackStringArray(saveBookArray, histCount);
						count = 0;
						while( count != histCount )
						{				
							saveBookArray = pushBackStringArray(saveBookArray, getStringArrayAt(historyFilesArray,count));
							if (count == f_cnt) 
							{
								saveBookArray = pushBackStringArray(saveBookArray, currElapsed);
							}
							else
							{
								saveBookArray = pushBackStringArray(saveBookArray, getStringArrayAt(historyElapsedArray,count));
							}
							count += 1;
						}
						
						writeStringToFile("<?=$confs_path."ua_history/"?>" + currentHistFile, saveBookArray);
					}
					
					if (currentHistFile != "" &amp;&amp; found == 0 )
					{
						
						saveBookArray = null;
						hc = (-histCount-1)*-1;
						saveBookArray = pushBackStringArray(saveBookArray, hc);
						count = 0;
						while( count != histCount )
						{				
							saveBookArray = pushBackStringArray(saveBookArray, getStringArrayAt(historyFilesArray,count));
							saveBookArray = pushBackStringArray(saveBookArray, getStringArrayAt(historyElapsedArray,count));
							count += 1;
						}
						saveBookArray = pushBackStringArray(saveBookArray, curr);
						saveBookArray = pushBackStringArray(saveBookArray, currElapsed);
						writeStringToFile("<?=$confs_path."ua_history/"?>" + currentHistFile, saveBookArray);
					}
					
					if (currentHistFile == "")
					{
						saveBookArray = null;
						hc = (-histFilmsCount-1)*-1;
						cHistFile = "";
						if ( hc &gt; <?=$history_length?>)
						{
							bookCount = 0;
							cnt = 0;
							dlok = readStringFromFile( "<?=$ua_favorites_filename?>" );
							if (dlok != null)
								{
									while ( cnt != hc)
									{
										fnd = 0;
										hl = getStringArrayAt(historyFilmsArray,cnt);
										print("hl==============================",hl);
										c = 0;
										bookCount = getStringArrayAt(dlok, c); c += 2;
										count = 0;
										while( count != bookCount )
											{
												bl = getStringArrayAt(dlok, c); 
												print("bl==============================",bl);
												c += 5;
												if (hl == bl) 
												{
													fnd = 1;
													break;
												}
												count += 1;
											}
										if (fnd == 0) break;
										cnt += 1;
									}
								}	
												
							
								cHistFile = getStringArrayAt(historyFilesListArray,cnt);
								historyFilesListArray = deleteStringArrayAt(historyFilesListArray, cnt);
								historyFilmsArray = deleteStringArrayAt(historyFilmsArray, cnt);
								historyTitleArray = deleteStringArrayAt(historyTitleArray, cnt);
								historyPoster = deleteStringArrayAt(historyPoster, cnt);
								historySite = deleteStringArrayAt(historySite, cnt);
								
								hc = histFilmsCount;
								histFilmsCount -=1;
						}
						
						
						saveBookArray = pushBackStringArray(saveBookArray, hc);
						count = 0;
						while( count != histFilmsCount )
						{
							saveBookArray = pushBackStringArray(saveBookArray, getStringArrayAt(historyFilmsArray,count));
							saveBookArray = pushBackStringArray(saveBookArray, getStringArrayAt(historyFilesListArray,count));
							saveBookArray = pushBackStringArray(saveBookArray, getStringArrayAt(historyTitleArray,count));
							saveBookArray = pushBackStringArray(saveBookArray, getStringArrayAt(historyPoster,count));
							saveBookArray = pushBackStringArray(saveBookArray, getStringArrayAt(historySite,count));
							count += 1;
						}
						
						saveBookArray = pushBackStringArray(saveBookArray, param);
						if (cHistFile !="")	saveBookArray = pushBackStringArray(saveBookArray, cHistFile);
						else saveBookArray = pushBackStringArray(saveBookArray, count);
						
						saveBookArray = pushBackStringArray(saveBookArray, name);
						saveBookArray = pushBackStringArray(saveBookArray, poster);
						saveBookArray = pushBackStringArray(saveBookArray, site);
											
						
					
						writeStringToFile("<?=$ua_history_main_filename?>", saveBookArray);
						saveBookArray = null;
						saveBookArray = pushBackStringArray(saveBookArray, 1);
						saveBookArray = pushBackStringArray(saveBookArray, curr);
						saveBookArray = pushBackStringArray(saveBookArray, currElapsed);
						if (cHistFile !="") count=cHistFile;
						writeStringToFile("<?=$confs_path."ua_history/"?>" +count+".conf", saveBookArray);
					}
				}
<?php
?>