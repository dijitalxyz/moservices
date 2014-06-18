<?php
/*	------------------------------
	Ukraine online services 	
	RSS player module v2.4
	------------------------------
	Created by Sashunya 2014
	wall9e@gmail.com			
	Some code was used from 
	Farvoice , sayler project 
	------------------------------ */

include ("ua_paths.inc.php");

class ua_player_const
{

	// цвета и настройки окна воспроизведения
	const main_backgroundColor		= "0:0:0";
	const preview_windowColor		= "0:0:0";
	const progress_backgroundColor	= "-1:-1:-1";
	const progress_offsetXPC		= "10"; 
	const progress_offsetYPC		= "70"; 
	const progress_widthPC			= "80"; 
	const progress_heightPC			= "23";
	// это фон верхней части окна информации
	const top_offsetXPC				= "0";
	const top_offsetYPC				= "0";
	const top_widthPC				= "100"; 
	const top_heightPC				= "100";
	// это константы линии времени
	const timeline_offsetXPC		= "15"; 
	const timeline_offsetYPC		= "84"; 
	const timeline_heightPC			= "8";
	const timeline_widthPC			= "50";
	const timeline_foreground_image	= "ua_timeline_fg.png";
	const timeline_background_image	= "ua_timeline_bg.png";
	const seek_image	= "ua_seek.png";
	
	// картинка фона
	const info_prev					= "ua_info_prev.png";
	const info_next					= "ua_info_next.png";
	const info_background			= "ua_info_bkgd.png";

	// это текст названия файла для окна информации
	const title_offsetXPC			= "0";
	//const title_offsetXPC			= "11";
	const title_offsetYPC			= "28"; 
	const title_widthPC				= "98"; 
	//const title_widthPC				= "88"; 
	const title_heightPC			= "40"; 
	const title_fontSize			= "18"; 
	const title_backgroundColor		= "-1:-1:-1"; 
	const title_foregroundColor		= "255:235:8";
	const title_lines 				= "2";
	
	// это текст названия фильма для окна информации
	const name_offsetXPC			= "2.5";
	//const name_offsetXPC			= "13.5";
	const name_offsetYPC			= "0"; 
	const name_widthPC				= "88"; 
	//const name_widthPC				= "75"; 
	const name_heightPC				= "30"; 
	const name_fontSize				= "20"; 
	const name_backgroundColor		= "-1:-1:-1"; 
	const name_foregroundColor		= "255:255:255";
	const name_lines 				= "1";
	
	//	это параметры картинок статуса (пауза, воспроизведение)
	const status_offsetXPC				= "93";
	const status_offsetYPC				= "50";
	const status_widthPC				= "5"; 
	const status_heightPC				= "30";	
	
	//	это параметры логотипа
	const logo_offsetXPC				= "93";
	const logo_offsetYPC				= "10";
	const logo_widthPC					= "5"; 
	const logo_heightPC					= "30";	
	// настройки прогрессбара для буфера
	const bar_offsetXPC					= "80";
	const bar_offsetYPC					= "84";
	const bar_widthPC					= "17";
	const bar_heightPC					= "8";
	const bar_barColor					= "0:0:0";
	const bar_progressColor				= "84:255:8";
	const bar_bufferColor				= "-1:-1:-1";
	

	// текст для отображения текущего времени
	const time_elapsed_offsetXPC			= "1.5";
	const time_elapsed_offsetYPC			= "71"; 
	const time_elapsed_widthPC				= "41"; 
	const time_elapsed_heightPC				= "32"; 
	const time_elapsed_fontSize				= "19"; 
	const time_elapsed_backgroundColor		= "-1:-1:-1"; 
	const time_elapsed_foregroundColor		= "255:255:255";
	// текст для отображения длительности времени
	
	const time_offsetXPC			= "66.5";
	const time_offsetYPC			= "71"; 
	const time_widthPC				= "41"; 
	const time_heightPC				= "32"; 
	const time_fontSize				= "19"; 
	const time_backgroundColor		= "-1:-1:-1"; 
	const time_foregroundColor		= "255:255:255";
	
	const fsua_logo								= 'ua_fsua.png';
	const exua_logo								= 'ua_exua_ukr.png';
	const uakino_logo							= 'ua_uakinonet.png';
}
class ua_player extends ua_player_const
{	
	// функция анимации ожидания
	public function showIdle()
	{
	global $ua_images_path;
	?>
		<idleImage><?= $ua_images_path ?>POPUP_LOADING_01.png</idleImage>
		<idleImage><?= $ua_images_path ?>POPUP_LOADING_02.png</idleImage>
		<idleImage><?= $ua_images_path ?>POPUP_LOADING_03.png</idleImage>
		<idleImage><?= $ua_images_path ?>POPUP_LOADING_04.png</idleImage>
		<idleImage><?= $ua_images_path ?>POPUP_LOADING_05.png</idleImage>
		<idleImage><?= $ua_images_path ?>POPUP_LOADING_06.png</idleImage>
		<idleImage><?= $ua_images_path ?>POPUP_LOADING_07.png</idleImage>
		<idleImage><?= $ua_images_path ?>POPUP_LOADING_08.png</idleImage>
	<?php
	}
	
	public function onUserInput_script()	
	{
		global $key_display;
		global $key_play;
		global $key_pause;
		global $key_left;
		global $key_right;
		global $key_up;
		global $key_down;
		global $key_frwd;
		global $key_ffwd;
		global $key_return;
		global $key_enter;
		global $goto_time;
		global $screensaver;

	?>
		<onUserInput>
				<script>
					input = currentUserInput();
					print("*player_tv input***",input);
					ret = "false";

					if (input == "<?= $key_display ?>")
					{
						if(showInfo == 0) showInfo = 1;	else showInfo = 0;
						ret = "true";
					}	
					else if (input == "<?= $key_play ?>")
					{
						if (cPlayPause == 1) 
						{
							postMessage("<?= $key_pause ?>"); 
							cPlayPause = 0;
							<?
							 if ($screensaver == "1")
								{
							?>
								SetScreenSaverStatus("yes");
							<?	
								}
							?>
							
							
						} else
						{
							postMessage("<?= $key_play ?>"); 
							cPlayPause = 1;	
							<?
							 if ($screensaver == "1")
								{
							?>
								SetScreenSaverStatus("no");
							<?	
								}
							?>
							
						}
						ret = "true";
					}
					
					else if (input == "video_stop") {
						
						executeScript("historyCheck");
						stop_play = 1;
						ret = "true";
						}
					else if (input == "<?= $key_down ?>")
					{
						executeScript("historyCheck");
							if( pitemCount != 1 )
								{
									if( idx == 0 ) idx = pitemCount - 1;
										else idx -= 1;
											
									startVideo = 1;
								}
						ret = "true";
					}

					else if (input == "<?= $key_up ?>" )
					{
							executeScript("historyCheck");
							
							if( pitemCount != 1 )
								{
									idx -= -1;
									if( idx == pitemCount ) idx = 0;
									startVideo = 1;
								}
						ret = "true";
					}
					
					else if (input == "<?=$key_left?>" || input == "<?=$key_right?>" )
					{
						showInfo = 1;
						seek = "true";
						info_timer = 0;
						if (input == "<?= $key_right ?>" )p_seek -= -60;
						if (input == "<?= $key_left ?>" )p_seek -= 30;
						if (p_seek &lt; 0) p_seek = 0;
						if (p_seek &gt; playTotal) p_seek = playTotal;
						ret = "true";
					}
					else if (input == "<?=$key_enter?>")
					{
						if (seek == "true")
						{
							
							playAtTime(p_seek); 
							seek = "false";
						} else
						{
							gotoTimeArr = null;
							gotoTimeArr = pushBackStringArray( gotoTimeArr, h_e );
							gotoTimeArr = pushBackStringArray( gotoTimeArr, m_e );
							gotoTimeArr = pushBackStringArray( gotoTimeArr, s_e );
							gotoTimeArr = pushBackStringArray( gotoTimeArr, h_t );
							gotoTimeArr = pushBackStringArray( gotoTimeArr, m_t );
							gotoTimeArr = pushBackStringArray( gotoTimeArr, s_t );
							writeStringToFile("<?=$goto_time?>", gotoTimeArr);
							jumpToLink("gotoMenu");
						}
						ret = "true";
					}
					ret;
				</script>
		</onUserInput>
	<?php
	}

//-------------------------------
	public function onEnters()
	{
	global $ua_path_link;
	global $ua_images_path;
	global $tmp;
	global $goto_time;
	global $position;
	global $screensaver;
	?>
	<onEnter>
		<?
			if ($screensaver == "1")
			{
		?>
			SetScreenSaverStatus("no");
		<?	
			}
		?>
		
		
		env_goto = readStringFromFile("/tmp/env_goto");
		env_ret_goto = readStringFromFile("/tmp/env_ret_goto");
		if (env_goto == "1" )
		{
			gotoTime = readStringFromFile("<?=$goto_time?>");
			env_goto = "0";
			writeStringToFile("/tmp/env_goto", "0");
			writeStringToFile("/tmp/env_ret_goto", "0");
			p_seek=gotoTime;
			playAtTime(gotoTime);
		}
		else
		{
			if (env_ret_goto == "1")
			{
				writeStringToFile("/tmp/env_ret_goto", "0");	
			} else
			{
				
				<?php
				if (isset($_GET['idx'])) {
					$idx=$_GET['idx'];
					} else $idx=0;
					
				?>
				url = "<?=$tmp?>";
				idx="<?=$idx?>";
				param = "<?=$_GET['param']?>";
				site = "<?=$_GET['site']?>";
				pos = "<?=$position?>";
				go_elapsed = 0;
				stop_play = 0;
				ptitleArray = null;
				plinkArray = null;
				downameArray = null;
				pitemCount = 0;
				dlok = readStringFromFile(url);
				if (dlok != null)
					{
						if (site == "2")
						{
							name = getStringArrayAt(dlok, 1);
							img = getStringArrayAt(dlok, 11);
							poster = getStringArrayAt(dlok, 12);
							param = getStringArrayAt(dlok, 0);
							c = 13;
							logo = "<?= $ua_images_path.static::fsua_logo ?>";
						} else
						if (site == "3")
						{
							name = getStringArrayAt(dlok, 0);
							img = getStringArrayAt(dlok, 10);
							poster = getStringArrayAt(dlok, 11);
							c = 12;
							logo = "<?= $ua_images_path.static::uakino_logo ?>";
						}
						else
						{
							name = getStringArrayAt(dlok, 0);
							img = getStringArrayAt(dlok, 10);
							poster = getStringArrayAt(dlok, 11);
							c = 12;
							logo = "<?= $ua_images_path.static::exua_logo ?>";
							
						}
						
						
						pitemCount = getStringArrayAt(dlok, c); c += 1;
						count = 0;
						while( count != pitemCount )
							{
							ptitleArray = pushBackStringArray( ptitleArray, getStringArrayAt(dlok, c)); c += 1;
							plinkArray = pushBackStringArray( plinkArray, getStringArrayAt(dlok, c)); c += 2;		
							downameArray = pushBackStringArray( downameArray, getStringArrayAt(dlok, c)); c += 3;
							count += 1;
							}
					}	
				
				
				
				setRefreshTime(100);
				startVideo = 1;
				cPlayPause = 1;
				elapsedTime = "";
				totalTime = "";
				showLoading = 0;
				showInfo = 0;
			}
		}
	</onEnter>
	<?php
	}	

	public function onExits()
	{
	global $screensaver;
	?>
		<onExit>
				<?
			if ($screensaver == "1")
			{
		?>
			SetScreenSaverStatus("yes");
		<?	
			}
		?>
		
			if (stop_play == 0)
			{
						executeScript("historyCheck");
			}
			playItemURL(-1, 1);
			setRefreshTime(-1);
			writeStringToFile("/tmp/env_idx_play_message", idx);
			writeStringToFile("/tmp/env_use_alt_player_message", "1");
		</onExit>
	
	<?php
	}
	
	
	public function onRefresh()
	{
	global $key_return;
	global $ua_images_path;
	global $xtreamer;
	
	?>
	<onRefresh>
		img_fg_timeline = "<?= $ua_images_path.static::timeline_foreground_image ?>";
		img_bg_timeline = "<?= $ua_images_path.static::timeline_background_image ?>";
		vidProgress = getPlaybackStatus();
		bufProgress = getCachedStreamDataSize(0, 262144);
		playElapsed = getStringArrayAt(vidProgress, 0);
		if (seek == "false") p_seek=playElapsed;
		playTotal = getStringArrayAt(vidProgress, 1);
		playStatus = getStringArrayAt(vidProgress, 3);
		if (go_elapsed == 1 &amp;&amp; bufProgress == 100 &amp;&amp; playStatus !=0) 
			{
				delay +=1;
				if (delay &gt; 3)
				{
					if (currElapsed &gt; 0 &amp;&amp; pos == "1") 
						{
							p_seek=currElapsed;
							playAtTime(currElapsed);
						}
					go_elapsed = 0;
					delay = 0;
				}
			}
		if (startVideo == 1)
		{
			elapsedTime = "00:00:00";
			totalTime = elapsedTime;
			info_streamTitle = getStringArrayAt(ptitleArray, idx); 
			info_playURL =  getStringArrayAt(plinkArray, idx)+ " autoReconnect"; 
			playItemURL(-1, 1);
			setRefreshTime(500);
			showLoading = 1;
			startVideo = 0;
			timeStamp += 1;
			info_timer = 0;
			showInfo = 1;
			delay = 0;
			playItemURL(info_playURL, 0, "mediaDisplay", "previewWindow");
			checkElapsed = 1;
			executeScript("historyFiles");
			executeScript("historyCheck");
			checkElapsed = 0;
			go_elapsed = 1;
			
		} else
		{
			
			if (playElapsed != 0)
			{
				x = Integer(playElapsed / 60);
				h_e = Integer(playElapsed / 3600);
				s_e = playElapsed - (x * 60);
				m_e= x - (h_e * 60);
				if(h_e &lt; 10) 
				elapsedTime = "0" + sprintf("%s:", h_e);	else	elapsedTime = sprintf("%s:", h_e);
				if(m_e &lt; 10)  elapsedTime += "0";
				elapsedTime += sprintf("%s:", m_e);
				if(s_e &lt; 10)  elapsedTime += "0";
				elapsedTime += sprintf("%s", s_e);
							
				x = Integer(playTotal / 60);
				h_t = Integer(playTotal / 3600);
				s_t = playTotal - (x * 60);
				m_t = x - (h_t * 60);
				if(h_t &lt; 10) totalTime = "0" + sprintf("%s:", h_t); else		totalTime = sprintf("%s:", h_t);
				if(m_t &lt; 10)  totalTime += "0";
				totalTime += sprintf("%s:", m_t);
				if(s_t &lt; 10)  totalTime += "0";
				totalTime += sprintf("%s", s_t);
								
				if (startVideo == 0)
				{
					startVideo = 2;
					statusCounter = 0;
					statusTimeout = 60;
					updatePlaybackProgress("delete", "mediaDisplay", "progressBar");
				}
				else if(startVideo == 2)
				{
					statusCounter += 1;
					
					if (statusCounter &gt;= statusTimeout) statusCounter = 0;
					
				}
				else
				{
					
					updatePlaybackProgress(bufProgress, "mediaDisplay", "progressBar");
					
				}
			}
			else if (playStatus == 0)
			{
				print ("stop_play------",stop_play);
				executeScript("historyCheck");
				if( pitemCount != 1 )
								{
									idx -= -1;
									if( idx == pitemCount ) 
									{
										idx -=1;
										postMessage("<?= $key_return ?>");
									}
									
									startVideo = 1;
								} else	postMessage("<?= $key_return ?>");
						
			}
			else
			{
				
				updatePlaybackProgress(bufProgress, "mediaDisplay", "progressBar");
			}
		}
	
		if (stop_play == 1 &amp;&amp; playStatus != 0) 
		{
			executeScript("historyCheck");
			postMessage("<?= $key_return ?>");
		}

		if(showInfo == 1)
		{
			info_timer += 1;
			updatePlaybackProgress(bufProgress, "mediaDisplay", "progressBar");
		} else
		{	
			info_timer = 0;
			seek = "false";
		}
		if (info_timer == 20) 
		{
			showInfo =0;
			seek = "false";
		}
	</onRefresh>
	<?php
	}
	
	public function channel()
	{
	global $ua_path_link;
	?>
	<channel>
		<title>ua_player</title>
		<link><?= $ua_path_link."ua_player.php"?></link>	
	</channel>
		
	<?php
	}
	
	// функция подготовки параметров выходного RSS
	public function showDisplay()
	{
	global $ua_images_path;
	global $ua_path_link;
	?>
	<historyFiles>
			<?
				include ("ua_rss_historyfiles.inc.php");
			?>
	</historyFiles>
	
	<historyCheck>
			<?
				include ("ua_rss_history_check.inc.php");
			?>
	</historyCheck>
	
	
	<gotoMenu>
	    <link>
		<script>
			link="<?=$ua_path_link?>ua_rss_goto.php";
			link;
		</script>
		</link>
	</gotoMenu>
	
	<mediaDisplay name = "threePartsView"
		showDefaultInfo 	= "no" 
		sideLeftWidthPC 	= "0" 
		sideRightWidthPC 	= "0" 
		backgroundColor		= "<?= static::main_backgroundColor ?>"
		idleImageXPC		="0"
		idleImageYPC		="0"
		idleImageWidthPC	="0"
		idleImageHeightPC	="0"
	>
	
	<previewWindow 
		windowColor			= "<?= static::preview_windowColor ?>" 
		offsetXPC			= "0" 
		offsetYPC			= "0" 
		widthPC				= "100"
		heightPC			= "100"
	>
	</previewWindow>	
	
	<progressBar 
		backgroundColor		= "<?= static::progress_backgroundColor ?>" 
		offsetXPC			= "<?= static::progress_offsetXPC ?>" 
		offsetYPC			= "<?= static::progress_offsetYPC ?>" 
		widthPC				= "<?= static::progress_widthPC ?>" 
		heightPC			= "<?= static::progress_heightPC ?>"
	>
	
		
		<image redraw="yes" offsetXPC="<?= static::top_offsetXPC ?>" offsetYPC="<?= static::top_offsetYPC ?>" widthPC="<?= static::top_widthPC ?>" heightPC="<?= static::top_heightPC ?>">
		<?= $ua_images_path.static::info_background ?>
		</image>

		<image redraw="yes" offsetXPC="<?= static::top_offsetXPC ?>" offsetYPC="<?= static::top_offsetYPC ?>" widthPC="<?= static::top_widthPC ?>" heightPC="<?= static::top_heightPC ?>">
				<?= $ua_images_path.static::info_background ?>
		</image>
		
		<text redraw="yes" offsetXPC="<?= static::name_offsetXPC ?>" offsetYPC="<?= static::name_offsetYPC ?>" widthPC="<?= static::name_widthPC ?>" heightPC="<?= static::name_heightPC ?>" fontSize="<?= static::name_fontSize ?>" backgroundColor="<?= static::name_backgroundColor ?>" foregroundColor="<?= static::name_foregroundColor ?>" lines="<?= static::name_lines ?>">
			<script>name;</script>
		</text>
		
		<text redraw="yes" offsetXPC="<?= static::title_offsetXPC ?>" offsetYPC="<?= static::title_offsetYPC ?>" widthPC="<?= static::title_widthPC ?>" heightPC="<?= static::title_heightPC ?>" fontSize="<?= static::title_fontSize ?>" backgroundColor="<?= static::title_backgroundColor ?>" foregroundColor="<?= static::title_foregroundColor ?>" lines="<?= static::title_lines ?>">
			<script>info_streamTitle;</script>
		</text>


		<image redraw="yes" offsetXPC="<?= static::status_offsetXPC ?>" offsetYPC="<?= static::status_offsetYPC ?>" widthPC="<?= static::status_widthPC ?>" heightPC="<?= static::status_heightPC ?>">
				<script>
						if (playStatus == 2)
						{
							if (cPlayPause == 1)
								showstr = "<?= $ua_images_path."ua_play.png"?>"; 
							else
								showstr = "<?= $ua_images_path."ua_pause.png"?>"; 
						}
						else
						{
							showstr = "<?= $ua_images_path."ua_stop.png"?>"; 
						}				
				showstr;
			</script>
					
		</image>
		
		
		<image redraw="yes" offsetXPC="<?= static::logo_offsetXPC ?>" offsetYPC="<?= static::logo_offsetYPC ?>" widthPC="<?= static::logo_widthPC ?>" heightPC="<?= static::logo_heightPC ?>">
			<script>
					logo;
			</script>
					
		</image>
		
<!--	
		<image redraw="no" offsetXPC="2.3" offsetYPC="4" widthPC="9.5" heightPC="72">
			<script>
					img;
			</script>
			
		</image>
-->	
		
		<image redraw="no" offsetXPC="<?= static::timeline_offsetXPC ?>" offsetYPC="<?= static::timeline_offsetYPC ?>" heightPC="<?= static::timeline_heightPC ?>" widthPC="<?= static::timeline_widthPC ?>">
			<script>
				img_bg_timeline;
			</script>
		</image>
		
		<image redraw="yes" offsetXPC="<?= static::timeline_offsetXPC ?>" offsetYPC="<?= static::timeline_offsetYPC ?>" heightPC="<?= static::timeline_heightPC ?>">
			<script>
				img_fg_timeline;
			</script>
		<widthPC>
			<script>
				(playElapsed/playTotal)*50;
			</script>
		</widthPC>
		</image>
		<image redraw="yes" offsetYPC="82" heightPC="12" widthPC="2"> 
			<script>
				"<?= $ua_images_path.static::seek_image?>";
			</script>
		<offsetXPC>
			<script>
				if (playStatus == 2)
				{
					 off=((p_seek/playTotal)+0.28)*50;
				} else off=14;
				off;
			</script>
		</offsetXPC>
				
				
			
		</image>
		
		<bar offsetXPC="<?= static::bar_offsetXPC ?>" offsetYPC="<?= static::bar_offsetYPC ?>" widthPC="<?= static::bar_widthPC ?>" heightPC="<?= static::bar_heightPC ?>" barColor="<?= static::bar_barColor ?>" progressColor="<?= static::bar_progressColor ?>" bufferColor="<?= static::bar_bufferColor ?>"/>

		
		<text redraw="yes" offsetXPC="<?= static::time_elapsed_offsetXPC ?>" offsetYPC="<?= static::time_elapsed_offsetYPC ?>" widthPC="<?= static::time_elapsed_widthPC ?>" heightPC="<?= static::time_elapsed_heightPC ?>" fontSize="<?= static::time_elapsed_fontSize ?>" backgroundColor="<?= static::time_elapsed_backgroundColor ?>" foregroundColor="<?= static::time_elapsed_foregroundColor ?>">
			<script>elapsedTime;</script>
		</text>
		
		<text redraw="yes" offsetXPC="<?= static::time_offsetXPC ?>" offsetYPC="<?= static::time_offsetYPC ?>" widthPC="<?= static::time_widthPC ?>" heightPC="<?= static::time_heightPC ?>" fontSize="<?= static::time_fontSize ?>" backgroundColor="<?= static::time_backgroundColor ?>" foregroundColor="<?= static::time_foregroundColor ?>">
			<script>totalTime;</script>
		</text>
		
		<destructor offsetXPC="0" offsetYPC="0" widthPC="100" heightPC="100" color="-1:-1:-1">
		</destructor>

	</progressBar>
	
	<?php	
		
		$this->showIdle();
		$this->onUserInput_script();
	?>
	</mediaDisplay>
	<?php
		$this->onRefresh();
		$this->onEnters();
		$this->onExits();
		$this->channel();
	}
	
	
	
	public function showRss()
	{
		header( "Content-type: text/plain" );
		echo "<?xml version=\"1.0\" encoding=\"UTF8\" ?>\n";
		echo '<rss version="2.0" xmlns:dc="http://purl.org/dc/elements/1.1/">'.PHP_EOL;

		$this->showDisplay();
		
		echo '</rss>'.PHP_EOL;
	}


}
	
$view = new ua_player;
$view->showRss();
exit;
?>