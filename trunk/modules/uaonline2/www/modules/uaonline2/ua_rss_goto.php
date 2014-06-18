<?php
/*	------------------------------
	Ukraine online services 	
	GOTO module v0.1
	------------------------------
	Created by Sashunya 2013
	wall9e@gmail.com			
	----------------------------- */

include ("ua_paths.inc.php");

class ua_rss_goto_const 
{

	const gotoFocusUp 			= 	'ua_goto_focus_up.png';
	const gotoFocusDown 		= 	'ua_goto_focus_down.png';
	const gotoUnFocusUp 		= 	'ua_goto_unfocus_up.png';
	const gotoUnFocusDown 		= 	'ua_goto_unfocus_down.png';
	const background_image		=   'ua_update_bkgd.png';
	const focusColor 			=	 "255:230:0";
	const unFocusColor 			=	 "120:120:120";
}

class ua_goto_rss_photo extends ua_rss_goto_const
{
	public function showDisplay()
	{
	?>
	<mediaDisplay name="onePartView"
		
	 viewAreaXPC=31
	 viewAreaYPC=45
	 viewAreaWidthPC=40
	 viewAreaHeightPC=20
	 sideColorRight=0:0:0
	 sideColorLeft=0:0:0
	 sideColorTop=0:0:0
	 sideColorBottom=0:0:0 
	 backgroundColor=0:0:0
	 focusBorderColor=0:0:0
	 unFocusBorderColor=0:0:0
	 itemBackgroundColor=0:0:0
	 showHeader="no"
	 showDefaultInfo="no"
	 itemPerPage=2
	 itemWidthPC=15
	 itemXPC=41
	 itemHeightPC=7
	 itemImageWidthPC=0
	 itemImageHeightPC=0
	 itemYPC=89
	 itemImageXPC=0
	 itemImageYPC=30
	 idleImageXPC		="88"
	 idleImageYPC		="80"
	 idleImageWidthPC	="5"
	 idleImageHeightPC	="8"
	 >
	<?
	}
	
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
	public function TextGraphicsElements()
	{
	global $ua_images_path;
	?>
	<backgroundDisplay>
		<image redraw="no" offsetXPC=0 offsetYPC=0 widthPC=100 heightPC=100>
				<?=$ua_images_path.static::background_image?>
		</image>
	</backgroundDisplay>
<!-- 						TEXT ELEMENTS							 -->
<!-- CURRENT HOURS -->
		<text  align="center" redraw="yes" lines="1" offsetXPC="5" offsetYPC="35" widthPC="18" heightPC="30" fontSize="26" backgroundColor="-1:-1:-1">
		<foregroundColor>
				<script>
					if (h_c_a==1)
						{
							h_c_c="<?=static::focusColor?>";
						}else
						{
							h_c_c="<?=static::unFocusColor?>";
						}
						h_c_c;
				</script>
		</foregroundColor>
		<script> 
			if (h_c &lt; 10 ) res = "0"+h_c; else res=h_c;
			res;
		</script>
		</text>
<!-- DIVIDER -->
		<text  align="center" redraw="yes" lines="1" offsetXPC="12" offsetYPC="35" widthPC="18" heightPC="30" fontSize="26" backgroundColor="-1:-1:-1" foregroundColor="255:255:255">
			:
		</text>
<!-- CURRENT MINUTES -->
		<text  align="center" redraw="yes" lines="1" offsetXPC="19" offsetYPC="35" widthPC="18" heightPC="30" fontSize="26" backgroundColor="-1:-1:-1">
		<foregroundColor>
				<script>
					if (m_c_a==1)
						{
							m_c_c="<?=static::focusColor?>";
						}else
						{
							m_c_c="<?=static::unFocusColor?>";
						}
						m_c_c;
				</script>
		</foregroundColor>
			<script> 
			if (m_c &lt; 10 ) res = "0"+m_c; else res=m_c;
			res; 
			</script>
		</text>
<!-- DIVIDER -->
		<text  align="center" redraw="yes" lines="1" offsetXPC="26" offsetYPC="35" widthPC="18" heightPC="30" fontSize="26" backgroundColor="-1:-1:-1" foregroundColor="255:255:255">
			:
		</text>
<!-- CURRENT SECONDS -->
		<text  align="center" redraw="yes" lines="1" offsetXPC="34" offsetYPC="35" widthPC="18" heightPC="30" fontSize="26" backgroundColor="-1:-1:-1">
		<foregroundColor>
				<script>
					if (s_c_a==1)
						{
							s_c_c="<?=static::focusColor?>";
						}else
						{
							s_c_c="<?=static::unFocusColor?>";
						}
						s_c_c;
				</script>
		</foregroundColor>	
			<script> 
			if (s_c &lt; 10 ) res = "0"+s_c; else res=s_c;
				res;
			</script>
		</text>
<!-- MIDDLE DIVIDER -->
		<text  align="center" redraw="yes" lines="1" offsetXPC="43" offsetYPC="35" widthPC="18" heightPC="30" fontSize="26" backgroundColor="-1:-1:-1" foregroundColor="255:255:255">
			/
		</text>
<!-- TOTAL TIME -->
		<text  align="center" redraw="yes" lines="1" offsetXPC="49" offsetYPC="35" widthPC="50" heightPC="30" fontSize="26" backgroundColor="-1:-1:-1" foregroundColor="255:255:255">
			<script> 
				if (h_t &lt; 10 ) r_h = "0"+h_t; else r_h=h_t;
				if (m_t &lt; 10 ) r_m = "0"+m_t; else r_m=m_t;
				if (s_t &lt; 10 ) r_s = "0"+s_t; else r_s=s_t;
				t_t=r_h+":"+r_m+":"+r_s;
				t_t;
			</script>
		</text>
<!-- 						GRAPHIC CONTROLS							 -->		
<!-- HOURS UP -->		
		<image redraw="yes" offsetXPC="11" offsetYPC="15" widthPC="5" heightPC="15">
			<script>
				h_u;
			</script>
		</image>
<!-- MINUTES UP -->		
		<image redraw="yes" offsetXPC="26" offsetYPC="15" widthPC="5" heightPC="15">
			<script>
				m_u;
			</script>
		</image>		
<!-- SECONDS UP -->		
		<image redraw="yes" offsetXPC="40" offsetYPC="15" widthPC="5" heightPC="15">
			<script>
				s_u;
			</script>
		</image>		
<!-- HOURS DOWN -->		
		<image redraw="yes" offsetXPC="11" offsetYPC="70" widthPC="5" heightPC="15">
			<script>
				h_d;
			</script>
		</image>		
<!-- MINUTES DOWN -->		
		<image redraw="yes" offsetXPC="26" offsetYPC="70" widthPC="5" heightPC="15">
			<script>
				m_d;
			</script>
		</image>		
<!-- SECONDS DOWN -->		
		<image redraw="yes" offsetXPC="40" offsetYPC="70" widthPC="5" heightPC="15">
			<script>
				s_d;
			</script>
		</image>
	<?
	}
	
	public function onUserInput()
	{
	global $key_left;
	global $key_right;
	global $key_up;
	global $key_down;
	global $key_enter;
	global $key_return;
	global $goto_time;
	global $ua_images_path;
	?>
		
			<onUserInput>
				<script>
					timerCnt = 0;
					input = currentUserInput();
					itm_index = getFocusItemIndex();
					majorContext = getPageInfo("majorContext");	
					ret = "false";
					if (input == "<?= $key_up ?>" )
						{
							if (currentDigits == "hours" )
								{
									h_u = "<?=$ua_images_path.static::gotoFocusUp?>";									
									h_c -=- 1;
									if (h_c ==  h_t &amp;&amp; m_c &gt; m_t) m_c = m_t; 
									if (h_c &gt; h_t) 
									{
										h_c = 0;
									}
								} else 
							if (currentDigits == "minutes" )
								{
									m_u = "<?=$ua_images_path.static::gotoFocusUp?>";									
									m_c -=-1;
									if (m_c &gt; 59 ||  (h_c == h_t &amp;&amp; m_c &gt; m_t)) 
										{
											m_c = 0;
											h_c -=- 1;
											if (h_c &gt; h_t) 
												{
													h_c = 0;
												}
										}	

								} else 
							if (currentDigits == "seconds" )
								{
									s_u = "<?=$ua_images_path.static::gotoFocusUp?>";									
									s_c -=-1;
									if (s_c &gt; 59 || (h_c == h_t &amp;&amp; m_c == m_t &amp;&amp; s_c &gt; s_t))
										{
											s_c=0;
											m_c -=-1;
											if (m_c &gt; 59 ||  (h_c == h_t &amp;&amp; m_c &gt; m_t)) 
												{
													m_c = 0;
													h_c -=- 1;
													if (h_c &gt; h_t) 
													{
														h_c = 0;
													}
												}
										}
								} 
						ret="true";
						}
					if (input == "<?= $key_down ?>" )
						{
							if (currentDigits == "hours" )
								{
									h_d = "<?=$ua_images_path.static::gotoFocusDown?>";									
									h_c -= 1;
									if (h_c &lt; 0 ) 
									{
										h_c = h_t;
										m_c = m_t;
										s_c = s_t;
									}
								} else 
							if (currentDigits == "minutes" )
								{
									m_d = "<?=$ua_images_path.static::gotoFocusDown?>";									
									m_c -=1;
									if (m_c &lt; 0) 
										{
											m_c = 59;
											h_c -= 1;
											if (h_c &lt; 0) 
												{
													h_c = h_t;
													m_c = m_t;
													s_c = s_t;
												}
										}	

								} else 
							if (currentDigits == "seconds" )
								{
									s_d = "<?=$ua_images_path.static::gotoFocusDown?>";									
									s_c -=1;
									if (s_c &lt; 0)
										{
											s_c = 59;
											m_c -=1;
											if (m_c &lt; 0) 
												{
													m_c = 59;
													h_c -= 1;
													if (h_c &lt; 0) 
													{
														h_c = h_t;
														m_c = m_t;
														s_c = s_t;
													}
												}
										}
								} 
						ret="true";
						}
					if (input == "<?= $key_left ?>" )
						{
							if (currentDigits == "hours" )
								{
									currentDigits = "seconds";
									s_c_a = 1;
									m_c_a = 0;
									h_c_a = 0;
								} else 
							if (currentDigits == "minutes" )
								{
									currentDigits = "hours";
									s_c_a = 0;
									m_c_a = 0;
									h_c_a = 1;
								} else 
							if (currentDigits == "seconds" )
								{
									currentDigits = "minutes";
									s_c_a = 0;
									m_c_a = 1;
									h_c_a = 0;
								} 
						ret = "true";
						}
					if (input == "<?= $key_right ?>" )
						{
							if (currentDigits == "hours" )
								{
									currentDigits = "minutes";
									s_c_a = 0;
									m_c_a = 1;
									h_c_a = 0;
								} else 
							if (currentDigits == "minutes" )
								{
									currentDigits = "seconds";
									s_c_a = 1;
									m_c_a = 0;
									h_c_a = 0;
									
								} else 
							if (currentDigits == "seconds" )
								{
									currentDigits = "hours";
									s_c_a = 0;
									m_c_a = 0;
									h_c_a = 1;
								} 
						ret="true";
						}
					if (input == "<?= $key_enter ?>" )
						{
							out = (-h_c*3600-m_c*60-s_c)*-1;
							writeStringToFile("<?=$goto_time?>", out);
							jump = "1";
							postMessage("<?= $key_return ?>");	
							ret="true";
						}
					redrawDisplay();
					ret;
			</script>
			</onUserInput>
			</mediaDisplay>
		<?
	}
	
	function unFocusArrows()
	{
	global $ua_images_path;
	?>
			h_u = "<?=$ua_images_path.static::gotoUnFocusUp?>";
			h_d = "<?=$ua_images_path.static::gotoUnFocusDown?>";
			m_u = "<?=$ua_images_path.static::gotoUnFocusUp?>";
			m_d = "<?=$ua_images_path.static::gotoUnFocusDown?>";
			s_u = "<?=$ua_images_path.static::gotoUnFocusUp?>";
			s_d = "<?=$ua_images_path.static::gotoUnFocusDown?>";
	<?
	}
	
	public function restFunctions()
	{
	global $goto_time;
	global $key_return;
	?>
		
		<onRefresh>
			<?=$this->unFocusArrows()?>	
		timerCnt += 1;
		if (timerCnt == 10)
		{
			postMessage("<?= $key_return ?>");
		}

			redrawDisplay();
		</onRefresh>
		
		<onEnter>
		writeStringToFile("/tmp/env_goto", "0");
		timerCnt = 0;
		jump = "0";
		dlok = readStringFromFile("<?=$goto_time?>");
		h_c = getStringArrayAt(dlok, 0);
		m_c = getStringArrayAt(dlok, 1);
		s_c = getStringArrayAt(dlok, 2);
		h_t = getStringArrayAt(dlok, 3);
		m_t = getStringArrayAt(dlok, 4);
		s_t = getStringArrayAt(dlok, 5);
		
		h_c -=-1; 
		h_c -=1; 
		m_c -=-1; 
		m_c -=1; 
		s_c -=-1;
		s_c -=1;
		h_t -=-1; 
		h_t -=1; 
		m_t -=-1; 
		m_t -=1; 
		s_t -=-1; 
		s_t -=1; 
	
		currentDigits = "minutes";
		s_c_a = 0;
		m_c_a = 1;
		h_c_a = 0;
		<?=$this->unFocusArrows()?>
		cancelIdle();
		redrawDisplay();
		setRefreshTime(500); 
	</onEnter>
	
	<onExit>
		writeStringToFile("/tmp/env_goto", jump);
		writeStringToFile("/tmp/env_ret_goto", "1");		
	</onExit>
		
		
	<item_template>
		<link>
			
		</link>
	</item_template>
	<channel>
	<title>goto</title>
	<link><?=ua_images_path?>ua_rss_goto.php</link>

	<itemSize>
		<script>
			0;
		</script>
    </itemSize>
			
	</channel>
	</rss>
<?
	}
	public function showRss()
	{
		header( "Content-type: text/plain" );
		echo "<?xml version=\"1.0\" encoding=\"UTF8\" ?>\n";
		echo '<rss version="2.0" xmlns:dc="http://purl.org/dc/elements/1.1/">'.PHP_EOL;

		$this->showDisplay();
		$this->showIdle();
		$this->TextGraphicsElements();
		$this->onUserInput();
		$this->restFunctions();
		
		echo '</rss>'.PHP_EOL;
	}
}

$view = new ua_goto_rss_photo();
$view->showRss();

?>