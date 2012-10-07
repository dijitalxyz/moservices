#!/tmp/www/cgi-bin/php
﻿<?php echo "<?xml version='1.0' encoding='UTF8' ?>"; ?>
<rss version="2.0" xmlns:dc="http://purl.org/dc/elements/1.1/">
<mediaDisplay name="onePartsView"
	itemBackgroundColor="0:0:0"
	backgroundColor="0:0:0"
	sideLeftWidthPC="0"
	itemImageXPC="5"
	itemImageYPC="24"
	itemImageWidthPC="5"
	itemImageHeightPC="5"
	itemXPC="10"
	itemYPC="20"
	itemWidthPC="75"
	unFocusFontColor="101:101:101"
	focusFontColor="255:255:255"
>
	<idleImage idleImageYPC="45" idleImageHeightPC="10">/tmp/www/cgi-bin/scripts/exua/image/POPUP_LOADING_01.png<idleImageWidthPC><script>10 * screenYp / screenXp;</script></idleImageWidthPC><idleImageXPC><script>45 + 10 * (1 - screenYp / screenXp) / 2;</script></idleImageXPC></idleImage>
	<idleImage idleImageYPC="45" idleImageHeightPC="10">/tmp/www/cgi-bin/scripts/exua/image/POPUP_LOADING_02.png<idleImageWidthPC><script>10 * screenYp / screenXp;</script></idleImageWidthPC><idleImageXPC><script>45 + 10 * (1 - screenYp / screenXp) / 2;</script></idleImageXPC></idleImage>
	<idleImage idleImageYPC="45" idleImageHeightPC="10">/tmp/www/cgi-bin/scripts/exua/image/POPUP_LOADING_03.png<idleImageWidthPC><script>10 * screenYp / screenXp;</script></idleImageWidthPC><idleImageXPC><script>45 + 10 * (1 - screenYp / screenXp) / 2;</script></idleImageXPC></idleImage>
	<idleImage idleImageYPC="45" idleImageHeightPC="10">/tmp/www/cgi-bin/scripts/exua/image/POPUP_LOADING_04.png<idleImageWidthPC><script>10 * screenYp / screenXp;</script></idleImageWidthPC><idleImageXPC><script>45 + 10 * (1 - screenYp / screenXp) / 2;</script></idleImageXPC></idleImage>
	<idleImage idleImageYPC="45" idleImageHeightPC="10">/tmp/www/cgi-bin/scripts/exua/image/POPUP_LOADING_05.png<idleImageWidthPC><script>10 * screenYp / screenXp;</script></idleImageWidthPC><idleImageXPC><script>45 + 10 * (1 - screenYp / screenXp) / 2;</script></idleImageXPC></idleImage>
	<idleImage idleImageYPC="45" idleImageHeightPC="10">/tmp/www/cgi-bin/scripts/exua/image/POPUP_LOADING_06.png<idleImageWidthPC><script>10 * screenYp / screenXp;</script></idleImageWidthPC><idleImageXPC><script>45 + 10 * (1 - screenYp / screenXp) / 2;</script></idleImageXPC></idleImage>
	<idleImage idleImageYPC="45" idleImageHeightPC="10">/tmp/www/cgi-bin/scripts/exua/image/POPUP_LOADING_07.png<idleImageWidthPC><script>10 * screenYp / screenXp;</script></idleImageWidthPC><idleImageXPC><script>45 + 10 * (1 - screenYp / screenXp) / 2;</script></idleImageXPC></idleImage>
	<idleImage idleImageYPC="45" idleImageHeightPC="10">/tmp/www/cgi-bin/scripts/exua/image/POPUP_LOADING_08.png<idleImageWidthPC><script>10 * screenYp / screenXp;</script></idleImageWidthPC><idleImageXPC><script>45 + 10 * (1 - screenYp / screenXp) / 2;</script></idleImageXPC></idleImage>
<onUserInput>
	userInput = currentUserInput();

	if( userInput == "video_stop")
	{
		url="http://127.0.0.1/cgi-bin/scripts/exua/php/manag.cgi?name=" + getItemInfo(getFocusItemIndex(),"name") + ";go=delete";
		jumpToLink("destination");
	}

</onUserInput>


		<backgroundDisplay>
			<image  offsetXPC=0 offsetYPC=0 widthPC=100 heightPC=100>
			/tmp/www/cgi-bin/scripts/exua/image/backgd.jpg
			</image>
		</backgroundDisplay>
		<image  offsetXPC=0 offsetYPC=2.8 widthPC=100 heightPC=15.6>
		/tmp/www/cgi-bin/scripts/exua/image/rss_title.jpg
		</image>
		<text  offsetXPC=40 offsetYPC=8 widthPC=35 heightPC=10 fontSize=20 backgroundColor=-1:-1:-1 foregroundColor=255:255:255>
		Завантаження
		</text>
 <text align="center" redraw="yes" lines="4" offsetXPC=10 offsetYPC=90 widthPC=75 heightPC=15 fontSize=15 backgroundColor=0:0:0 foregroundColor=120:120:120>Для star/stop - вибираємо і ОК. Для видалення із списку - клавіша 2
                </text>
</mediaDisplay>
<destination>
	<link>
	<script>url;</script>
	</link>
</destination>
<channel>
	<title>Загрузки</title>
	<menu>main menu</menu>
    <item>
    <title>Остановить все</title>
	<link>http://127.0.0.1/cgi-bin/scripts/util/stop_exua.rss</link>
	<media:thumbnail url="/tmp/www/cgi-bin/scripts/exua/image/fon.jpg" />
    </item>
<?php
$file_list = glob("/tmp/hdd/root/log/*.log");
for ($i=0; $i< count($file_list); $i++) {
$log_file = file($file_list[$i]);
$t1 = explode('/log/', $file_list[$i]);
$t1 = explode('.log', $t1[1]);
$log = $log_file[count($log_file) -4];
$t3 = explode("K", $log);
$t4 = substr($log, -25);
$t5 = explode("%", $log);
$end = substr($t5[0], -3);
$t0 = $i+1;
//pid
$pd = "/tmp/".$t1[0].".pid";
$pid_file = file($pd);
$pid = explode('pid ', $pid_file[0]);
$pid = explode('.', $pid[1]);
//url
$log_url =  $log_file[0];
$url = explode('http://', $log_url);
$link = str_replace("\r","",$url[1]);
$link = str_replace("\n","",$link);
$link = 'http://'.$link;
//title
$title = $t0.'. '. $t1[0].' -  '.$t3[0].'kБайт'.$t4;
   echo '
    <item>
    <title>'.$title.'</title>';
	echo '<name>'.$t1[0].'</name>';
	if ($end != "100") {
	if (!$pid_file)  echo '
	<link>http://127.0.0.1/cgi-bin/scripts/exua/php/manag.cgi?link='.$link.';name='.$t1[0].';go=start</link>
	<media:thumbnail url="/tmp/www/cgi-bin/scripts/exua/image/off.jpg" />';
	else
	echo '
		<link>http://127.0.0.1/cgi-bin/scripts/exua/php/manag.cgi?pid='.$pid[0].';name='.$t1[0].';go=stop</link>
	<media:thumbnail url="/tmp/www/cgi-bin/scripts/exua/image/on.jpg" />';
	} else
	 echo '
	<link>http://127.0.0.1/cgi-bin/scripts/exua/php/manag.cgi?name='.$t1[0].';go=delete</link>
	<media:thumbnail url="/tmp/www/cgi-bin/scripts/exua/image/end.jpg" />';
	echo '
    </item>
    ';
}

?>

</channel>
</rss>