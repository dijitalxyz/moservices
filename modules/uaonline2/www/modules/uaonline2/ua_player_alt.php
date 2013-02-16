<?php
/*	------------------------------
	Ukraine online services 	
	RSS player module v1.7
	------------------------------
	Created by Sashunya 2012
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
	const timeline_offsetXPC		= "3"; 
	const timeline_offsetYPC		= "86"; 
	const timeline_heightPC			= "8";
	const timeline_widthPC			= "94";
	const timeline_foreground_image	= "ua_timeline_fg.png";
	const timeline_background_image	= "ua_timeline_bg.png";
	
	const prev_offsetXPC			= "0";
	const prev_offsetYPC			= "30";
	const prev_widthPC				= "5"; 
	const prev_heightPC				= "40";

	const next_offsetXPC			= "95";
	const next_offsetYPC			= "30";
	const next_widthPC				= "5"; 
	const next_heightPC				= "40";
	
	// картинка фона
	const info_prev					= "ua_info_prev.png";
	const info_next					= "ua_info_next.png";
	const info_background			= "ua_info_bkgd.png";

	// это текст названия фильма для окна информации
	const title_offsetXPC			= "3";
	const title_offsetYPC			= "0"; 
	const title_widthPC				= "55"; 
	const title_heightPC			= "73"; 
	const title_fontSize			= "22"; 
	const title_backgroundColor		= "-1:-1:-1"; 
	const title_foregroundColor		= "255:255:255";
	const title_lines 				= "3";
	
	//	это параметры картинок статуса (пауза, воспроизведение)
	const status_offsetXPC				= "89";
	const status_offsetYPC				= "52";
	const status_widthPC				= "5"; 
	const status_heightPC				= "30";	
	// настройки прогрессбара для буфера
	const bar_offsetXPC					= "58";
	const bar_offsetYPC					= "40";
	const bar_widthPC					= "35";
	const bar_heightPC					= "8";
	const bar_barColor					= "0:0:0";
	const bar_progressColor				= "200:200:200";
	const bar_bufferColor				= "-1:-1:-1";
	

	// текст для отображения длительности времени
	const time_offsetXPC			= "56";
	const time_offsetYPC			= "2"; 
	const time_widthPC				= "41"; 
	const time_heightPC				= "32"; 
	const time_fontSize				= "30"; 
	const time_backgroundColor		= "-1:-1:-1"; 
	const time_foregroundColor		= "255:255:255";
	
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
					if (input == "<?= $key_play ?>")
					{
						if (cPlayPause == 1) 
						{
							postMessage("<?= $key_pause ?>"); 
							cPlayPause = 0;
							
						} else
						{
							postMessage("<?= $key_play ?>"); 
							cPlayPause = 1;	
						}
						ret = "true";
					}
					
					if (input == "video_stop") {
						stop_play = 1;
						}
					if (input == "<?= $key_left ?>")
					{
							if( pitemCount != 1 )
								{
									if( idx == 0 ) idx = pitemCount - 1;
										else idx -= 1;
									startVideo = 1;
								}
						ret = "true";
					}

					if (input == "<?= $key_right ?>" )
					{
							if( pitemCount != 1 )
								{
									idx -= -1;
									if( idx == pitemCount ) idx = 0;
									startVideo = 1;
								}
						ret = "true";
					}
					
					if (input == "U" || input == "D" )
					{
						print("playElapsed_before",playElapsed);
						if (input == "<?= $key_up ?>" )playElapsed -= -300;
						if (input == "<?= $key_down ?>" )playElapsed -= 300;
						print("playElapsed_after",playElapsed);			
						if (playElapsed != -1) playAtTime(playElapsed); 
						
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
	global $tmp;
	?>
	<onEnter>
		<?php
		if (isset($_GET['idx'])) {
			$idx=$_GET['idx'];
			} else $idx=0;
		?>
		url = "<?=$tmp?>";
		idx="<?=$idx?>";
		param = "<?=$_GET['param']?>";
		site = "<?=$_GET['site']?>";
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
					param = getStringArrayAt(dlok, 0);
					c = 12;
				} else
				{
					name = getStringArrayAt(dlok, 0);
					img = getStringArrayAt(dlok, 10);
					c = 11;
				}
				
				
				pitemCount = getStringArrayAt(dlok, c); c += 2;
				count = 0;
				while( count != pitemCount )
					{
					plinkArray = pushBackStringArray( plinkArray, getStringArrayAt(dlok, c)); c += 2;		
					downameArray = pushBackStringArray( downameArray, getStringArrayAt(dlok, c)); c += 1;
					ptitleArray = pushBackStringArray( ptitleArray, getStringArrayAt(dlok, c)); c += 3;
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
	</onEnter>
	<?php
	}	

	public function onExits()
	{
	?>
		<onExit>
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
		playTotal = getStringArrayAt(vidProgress, 1);
		playStatus = getStringArrayAt(vidProgress, 3);
		if (startVideo == 1)
		{
			info_streamTitle = getStringArrayAt(ptitleArray, idx); 
			info_playURL =  getStringArrayAt(plinkArray, idx)+ " autoReconnect"; 
			playItemURL(-1, 1);
			setRefreshTime(1000);
			showLoading = 1;
			startVideo = 0;
			timeStamp += 1;
			info_timer = 0;
			showInfo = 1;
			playItemURL(info_playURL, 0, "mediaDisplay", "previewWindow");
			<?
			include ("ua_rss_historyfiles.inc.php");
			include ("ua_rss_history_check.inc.php");
			?>
			
			
		} else
		{
			if (playElapsed != 0)
			{
				x = Integer(playElapsed / 60);
				h = Integer(playElapsed / 3600);
				s = playElapsed - (x * 60);
				m = x - (h * 60);
				if(h &lt; 10) 
				elapsedTime = "0" + sprintf("%s:", h);	else	elapsedTime = sprintf("%s:", h);
				if(m &lt; 10)  elapsedTime += "0";
				elapsedTime += sprintf("%s:", m);
				if(s &lt; 10)  elapsedTime += "0";
				elapsedTime += sprintf("%s", s);
							
				x = Integer(playTotal / 60);
				h = Integer(playTotal / 3600);
				s = playTotal - (x * 60);
				m = x - (h * 60);
				if(h &lt; 10) totalTime = "0" + sprintf("%s:", h); else		totalTime = sprintf("%s:", h);
				if(m &lt; 10)  totalTime += "0";
				totalTime += sprintf("%s:", m);
				if(s &lt; 10)  totalTime += "0";
				totalTime += sprintf("%s", s);
				

				timeLine = elapsedTime+"/"+totalTime;
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
				if (stop_play == 1) {postMessage("<?= $key_return ?>");} else
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
		if(showInfo == 1)
		{
			info_timer += 1;
			updatePlaybackProgress(bufProgress, "mediaDisplay", "progressBar");
		} else info_timer = 0;
		if (info_timer == 10) showInfo =0;
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
	?>
	<mediaDisplay name = "threePartsView"
		showDefaultInfo 	= "no" 
		sideLeftWidthPC 	= "0" 
		sideRightWidthPC 	= "0" 
		backgroundColor		= "<?= static::main_backgroundColor ?>"
		idleImageXPC		="88"
		idleImageYPC		="80"
		idleImageWidthPC	="5"
		idleImageHeightPC	="8"
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
		<text redraw="yes" offsetXPC="<?= static::title_offsetXPC ?>" offsetYPC="<?= static::title_offsetYPC ?>" widthPC="<?= static::title_widthPC ?>" heightPC="<?= static::title_heightPC ?>" fontSize="<?= static::title_fontSize ?>" backgroundColor="<?= static::title_backgroundColor ?>" foregroundColor="<?= static::title_foregroundColor ?>" lines="<?= static::title_lines ?>">
			<script>info_streamTitle;</script>
		</text>
		
		<image redraw="yes" offsetXPC="<?= static::next_offsetXPC ?>" offsetYPC="<?= static::next_offsetYPC ?>" widthPC="<?= static::next_widthPC ?>" heightPC="<?= static::next_heightPC ?>">
				<?= $ua_images_path.static::info_next ?>
		</image>
		
		<image redraw="yes" offsetXPC="<?= static::prev_offsetXPC ?>" offsetYPC="<?= static::prev_offsetYPC ?>" widthPC="<?= static::prev_widthPC ?>" heightPC="<?= static::prev_heightPC ?>">
				<?= $ua_images_path.static::info_prev ?>
		</image>

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
				(playElapsed/playTotal)*94;
			</script>
		</widthPC>
		</image>
		
		
		
		<bar offsetXPC="<?= static::bar_offsetXPC ?>" offsetYPC="<?= static::bar_offsetYPC ?>" widthPC="<?= static::bar_widthPC ?>" heightPC="<?= static::bar_heightPC ?>" barColor="<?= static::bar_barColor ?>" progressColor="<?= static::bar_progressColor ?>" bufferColor="<?= static::bar_bufferColor ?>"/>

		
		<text redraw="yes" offsetXPC="<?= static::time_offsetXPC ?>" offsetYPC="<?= static::time_offsetYPC ?>" widthPC="<?= static::time_widthPC ?>" heightPC="<?= static::time_heightPC ?>" fontSize="<?= static::time_fontSize ?>" backgroundColor="<?= static::time_backgroundColor ?>" foregroundColor="<?= static::time_foregroundColor ?>">
			<script>timeLine;</script>
		</text>
		<destructor offsetXPC="0" offsetYPC="0" widthPC="100" heightPC="100" color="-1:-1:-1">
		</destructor>

	</progressBar>
	
	<?php
		$this->showIdle();
	?>
		
	<?php	
		
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