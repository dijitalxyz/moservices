<?php

// define skin

class rssSkin extends rssView
{
	const screenWidth	= 1360;
	const screenHeight	= 768;

	const skinWidth		= 1360;	//1280;
	const skinHeight	= 768;	//720;

	const viewAreaX		= 0;
	const viewAreaY		= 0;
	const viewAreaWidth	= 1360;	//1280;
	const viewAreaHeight	= 768;	//720;

	const background	= '';

// top
	const topBackground	= 'focus_left.png';
	const topX		= 0;
	const topY		= 24;	//0;
	const topWidth		= 1360;	//1280;
	const topHeight		= 40;
// top title
	const topOffsetX	= 50;	//0;
	const topOffsetY	= 0;
	const topFontColor	= '0:0:0';
	const topBackgroundColor= '-1:-1:-1';
	const topFontSize	= 16;
	const topAlign		= 'left';

// bottom
	const bottomBackground	= 'focus_left.png';
	const bottomX		= 0;
	const bottomY		= 704;	//680;
	const bottomWidth	= 1360;	//1280;
	const bottomHeight	= 40;
// bottom title
	const bottomOffsetX	= 50;	//0;
	const bottomOffsetY	= 0;
	const bottomFontColor	= '0:0:0';
	const bottomBackgroundColor = '-1:-1:-1';
	const bottomFontSize	= 14;
	const bottomAlign	= 'left';

// cursors
	const imageFocus	= 'focus.png';
	const imageUnFocus	= 'unfocus.png';
	const imageParentFocus	= 'unfocus.png';

// colors
	const sideColorLeft	= '0:0:0';
	const sideColorRight	= '0:0:0';
	const sideColorTop	= '0:0:0';
	const sideColorBottom	= '0:0:0';
	const backgroundColor	= '0:0:0';
// item
	const itemBackgroundColor = '0:0:0';

	const itemUnFocusBgColor = '80:80:80';
	const itemFocusBgColor   = '255:255:255';

	const focusBorderColor	  = '0:0:0';
	const unFocusBorderColor  = '0:0:0';

	const focusFontColor	   = '0:0:0';
	const unFocusFontColor	   = '255:255:255';
	const parentFocusFontColor = '255:255:255';

	const itemTextLines	= 1;
	const itemTextFontSize	= 14;
	const itemTextAlign	= 'left';
	const itemTextBackgroundColor = '-1:-1:-1';

// idle image
	const idleImageX	= 620;	//580;
	const idleImageY	= 324;	//300;
	const idleImageWidth	= 120;
	const idleImageHeight	= 120;
}

class rssSkinHTile extends rssSkin
{
// item
	const imageFocus	= 'focus_h_tile.png';

	const itemWidth		= 300;
	const itemHeight	= 120;

	const itemImageX	= 10;
	const itemImageY	= 10;
	const itemImageWidth	= 100;
	const itemImageHeight	= 100;

	const itemTextX		= 120;
	const itemTextY		= 24;
	const itemTextWidth	= 170;
	const itemTextHeight	= 50;

	const itemTextLines	= 2;
}

class rssSkinVTile extends rssSkin
{
// item
	const imageFocus	= 'focus_v_tile.png';

	const itemWidth		= 240;
	const itemHeight	= 160;

	const itemImageX	= 20;
	const itemImageY	= 10;
	const itemImageWidth	= 200;
	const itemImageHeight	= 112;

	const itemTextX		= 20;
	const itemTextY		= 122;
	const itemTextWidth	= 200;
	const itemTextHeight	= 30;

	const itemTextFontSize	= 10;
	const itemTextAlign	= 'center';
}

class rssSkinList extends rssSkin
{
// item
	const itemHeight	= 120;
	const itemWidth		= 1200;

	const itemImageX	= 10;
	const itemImageY	= 10;
	const itemImageWidth	= 100;
	const itemImageHeight	= 100;

	const itemTextX		= 120;
	const itemTextY		= 24;
	const itemTextWidth	= 1080;
	const itemTextHeight	= 50;

	const itemTextLines	= 2;
}

class rssSkinLeft extends rssSkin
{
	const background	= 'bg_left.png';

	const viewAreaX		= 0;
	const viewAreaY		= 84;	//60;
	const viewAreaHeight	= 600;	//600;
	const viewAreaWidth	= 360;	//320;
// item
	const imageFocus	= 'focus_left.png';

	const itemX		= 0;	//0;
	const itemY		= 3;
	const itemHeight	= 40;
	const itemWidth		= 356;	//316;

	const itemTextX		= 80;
	const itemTextY		= 0;
	const itemTextWidth	= 240;
	const itemTextHeight	= 40;
}

class rssSkinPopup extends rssSkin
{
	const background	= 'bg_popup.png';

	const viewAreaWidth	= 600;
	const offsetTop		= 64;	//40;
	const offsetBottom	= 64;	//40;

// top title
	const topX		= 2;
	const topY		= 2;
	const topWidth		= 596;
	const topHeight		= 38;

	const topOffsetX	= 2;
	const topOffsetY	= 2;

// bottom
	const bottomHeight	= 20;
// item
	const itemHeight	= 40;
	const itemWidth		= 592;

	const itemTextX		= 20;
	const itemTextY		= 0;
	const itemTextWidth	= 560;
	const itemTextHeight	= 40;
}

class rssSkinPlayer extends rssSkin
{
	const progressBackgroundColor	= '0:0:0';
	const progressX			= 80;
	const progressY			= 500;
	const progressWidth		= 1200;
	const progressHeight		= 180;
	const progressBackground	= 'bg_info.png';

	// текст названия потока
	const titleX			= 148;
	const titleY			= 0;
	const titleWidth		= 712;
	const titleHeight		= 150;
	const titleFontSize		= 22;
	const titleBackgroundColor	= '-1:-1:-1';
	const titleForegroundColor	= '255:255:255';
	const titleLines 		= 3;
	const titleAlign		= 'left';

	// иконка потока
	const iconX		= 40;
	const iconY		= 10;
	const iconWidth		= 100;
	const iconHeight	= 75;

	// картинки предыдущий, следующий
	const prevX	= 0;
	const prevY	= 66;
	const prevWidth	= 32;
	const prevHeight= 48;
	const prevIcon	= 'player_prev.png';

	const nextX	= 1168;
	const nextY	= 66;
	const nextWidth	= 32;
	const nextHeight= 48;
	const nextIcon	= 'player_next.png';

	// картинки статуса (пауза, воспроизведение)
	const statusX		= 1100;
	const statusY		= 105;
	const statusWidth	= 60;
	const statusHeight	= 60;

	const statusIconPlay	= 'player_play.png';
	const statusIconPause	= 'player_pause.png';
	const statusIconStop	= 'player_stop.png';

	// настройки прогрессбара для буфера
	const barX		= 874;
	const barY		= 70;
	const barWidth		= 280;
	const barHeight		= 15;
	const barBarColor	= '0:0:0';
	const barProgressColor	= '200:200:200';
	const barBufferColor	= '200:0:0';

	// текст для отображения длительности времени
	const timeX			= 874;
	const timeY			= 14;
	const timeWidth			= 280;
	const timeHeight		= 48;
	const timeFontSize		= 18;
	const timeBackgroundColor	= '-1:-1:-1';
	const timeForegroundColor	= '255:255:255';
}

?>
