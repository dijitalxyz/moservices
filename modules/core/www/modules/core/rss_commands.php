<?php
$rss_commands = array(
	'sdk' => array(
		'info'	=> array(
			'command' => 'display',
			'prompt'  => 'DISPLAY'
		),
		'rewind' => array(
			'command' => 'video_frwd',
			'prompt'  => '&lt;&lt;'
		),
		'forward' => array(
			'command' => 'video_ffwd',
			'prompt'  => '&gt;&gt;'
		),
		'pageup' => array(
			'command' => 'pageup',
			'prompt'  => '|&lt;'
		),
		'pagedown' => array(
			'command' => 'pagedown',
			'prompt'  => '&gt;|'
		),
		'stop' => array(
			'command' => 'video_stop',
			'prompt'  => 'STOP'
		),
		'pause' => array(
			'command' => 'video_pause',
			'prompt'  => 'PAUSE'
		),
		'play' => array(
			'command' => 'video_play',
			'prompt'  => 'PLAY'
		),
		'up'	=> array(
			'command' => 'up',
			'prompt'  => 'UP'
		),
		'down'	=> array(
			'command' => 'down',
			'prompt'  => 'DOWN'
		),
		'left'	=> array(
			'command' => 'left',
			'prompt'  => 'LEFT'
		),
		'right'	=> array(
			'command' => 'right',
			'prompt'  => 'RIGHT'
		),
		'return' => array(
			'command' => 'return',
			'prompt'  => 'RETURN'
		),
		'repeat' => array(
			'command' => 'video_repeat',
			'prompt'  => 'REPEAT'
		),
		'enter' => array(
			'command' => 'enter',
			'prompt'  => 'OK'
		),
		'menu' => array(
			'command' => 'edit',
			'prompt'  => 'FILE'
		),
		'zoom' => array(
			'command' => 'zoom',
			'prompt'  => 'ZOOM'
		),
		'home' => array(
			'command' => 'guide',
			'prompt'  => 'HOME'
		),
		'one' => array(
			'command' => 'one',
			'prompt'  => '1'
		),
		'two' => array(
			'command' => 'two',
			'prompt'  => '2'
		),
		'three' => array(
			'command' => 'three',
			'prompt'  => '3'
		),
		'four' => array(
			'command' => 'four',
			'prompt'  => '4'
		),
		'five' => array(
			'command' => 'five',
			'prompt'  => '5'
		),
		'six' => array(
			'command' => 'six',
			'prompt'  => '6'
		),
		'seven' => array(
			'command' => 'seven',
			'prompt'  => '7'
		),
		'eight' => array(
			'command' => 'eight',
			'prompt'  => '8'
		),
		'nine' => array(
			'command' => 'nine',
			'prompt'  => '9'
		),
		'zero' => array(
			'command' => 'zero',
			'prompt'  => '0'
		)
	),
	'xtreamer' => array(
		'info'	=> array(
			'command' => 'DISPLAY',
			'prompt'  => 'DISPLAY'
		),
		'rewind' => array(
			'command' => 'video_frwd',
			'prompt'  => '&lt;&lt;'
		),
		'forward' => array(
			'command' => 'video_ffwd',
			'prompt'  => '&gt;&gt;'
		),
		'pageup' => array(
			'command' => 'PG',
			'prompt'  => '|&lt;'
		),
		'pagedown' => array(
			'command' => 'PD',
			'prompt'  => '&gt;|'
		),
		'stop' => array(
			'command' => 'video_stop',
			'prompt'  => 'STOP'
		),
		'play' => array(
			'command' => 'video_play',
			'prompt'  => 'PLAY'
		),
		'repeat' => array(
			'command' => 'video_repeat',
			'prompt'  => 'REPEAT'
		),
		'menu' => array(
			'command' => 'video_abrepeat',
			'prompt'  => 'A-B'
		),
		'pause' => array(
			'command' => 'video_pause',
			'prompt'  => 'PAUSE'
		),
		'up'	=> array(
			'command' => 'U',
			'prompt'  => 'UP'
		),
		'down'	=> array(
			'command' => 'D',
			'prompt'  => 'DOWN'
		),
		'left'	=> array(
			'command' => 'L',
			'prompt'  => 'LEFT'
		),
		'right'	=> array(
			'command' => 'R',
			'prompt'  => 'RIGHT'
		),
		'return' => array(
			'command' => 'RET',
			'prompt'  => 'RETURN'
		),
		'enter' => array(
			'command' => 'ENTR',
			'prompt'  => 'OK'
		),
		'one' => array(
			'command' => '1',
			'prompt'  => '1'
		),
		'two' => array(
			'command' => '2',
			'prompt'  => '2'
		),
		'three' => array(
			'command' => '3',
			'prompt'  => '3'
		),
		'four' => array(
			'command' => '4',
			'prompt'  => '4'
		),
		'five' => array(
			'command' => 'video_search',
			'prompt'  => '5'
		),
		'seven' => array(
			'command' => 'setup',
			'prompt'  => '7'
		),
		'eight' => array(
			'command' => '8',
			'prompt'  => '8'
		),
		'zero' => array(
			'command' => '0',
			'prompt'  => '0'
		)
	)
);

function getRssCmdSet()
{
global $nav_cmdset;

	return $nav_cmdset;
}

function getRssCommand( $cmd )
{
global $nav_cmdset;
global $rss_commands;

	if( isset( $rss_commands[ $nav_cmdset ][ $cmd ] )) return $rss_commands[ $nav_cmdset ][ $cmd ]['command'];
	return $cmd;
}

function getRssCommandPrompt( $cmd )
{
global $nav_cmdset;
global $rss_commands;

	if( isset( $rss_commands[ $nav_cmdset ][ $cmd ] )) return $rss_commands[ $nav_cmdset ][ $cmd ]['prompt'];
	return $cmd;
}

?>
