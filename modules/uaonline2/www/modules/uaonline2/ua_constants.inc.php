<?php
/*	------------------------------
	Ukraine online services 	
	RSS constants for threePartsView style 
	module v1.1
	------------------------------
	Created by Sashunya 2011	
	wall9e@gmail.com			
	Some code was used from 
	Farvoice  & others 
	------------------------------ */

// Константы, размеров элементов окон, шрифта и т.п.
// это константы для окна с линками (там где кыно запускаем)

// глобальные константы, испрользуемые во всех окнах
class global_const extends ua_rss_view
{
// main
	const backgroundColor 		=	'0:0:0';
	const focusFontColor		=	'255:255:255';
	const unFocusFontColor		=	'101:101:101';
// это картинки баров
	const imageFocus 			= 	'ua_focus_category.bmp';
	const imageParentFocus 		= 	'ua_parent_focus_category.bmp';
	const imageUnFocus 			= 	'ua_unfocus_category.bmp';
	const parentFocusFontColor	=	'101:101:101';
	
	

	// текст заголовка
	const text_header_align		=	'left'; // далее идут константы для текста заголовка
	const text_header_redraw	=	'no';
	const text_header_lines		=	'1';
	const text_header_offsetXPC	=	'27';
	const text_header_offsetYPC	=	'2';
	const text_header_widthPC	=	'70';
	const text_header_heightPC	=	'10';
	const text_header_fontSize	=	'20'; // размер шрифта заголовка
	const text_header_backgroundColor	=	'-1:-1:-1';// фон 
	const text_header_foregroundColor	=	'255:255:255'; //цвет шрыфта
	
	// текст подписи 
	const text_footer_align		=	'left';
	const text_footer_redraw	=	'no';
	const text_footer_lines		=	'1';
	const text_footer_offsetXPC	=	'8';
	const text_footer_offsetYPC	=	'88';
	const text_footer_widthPC	=	'95';
	const text_footer_heightPC	=	'10';
	const text_footer_fontSize	=	'20'; 
	const text_footer_backgroundColor	=	'-1:-1:-1';
	const text_footer_foregroundColor	=	'255:255:255'; 
    
	// название сайта (которое справа внизу)	
	const image_site_footer_display_offsetXPC 	= '85';
	const image_site_footer_display_offsetYPC 	= '90.3';
	const image_site_footer_display_widthPC 	= '4';
	const image_site_footer_display_heightPC	= '6';
	const fsua_logo								= 'ua_fsua.png';
	const exua_logo								= 'ua_exua_ukr.png';
	const uakino_logo							= 'ua_uakinonet.png';
}
class ua_rss_link_const extends global_const
{
	
	// items т.е отображение списка файлов фильма 
	const itemBackgroundColor	=	'0:0:0';
	const itemXPC				=	'27';
	const itemYPC				=	'13'; 
	const itemImageXPC			=	'27';
	const itemImageYPC 			= 	'13' ;
	const itemImageWidthPC		=	'0';
	const itemImageHeightPC		=	'0';
	const itemPerPage			=	'5';
	const itemWidthPC			=	'62';
	const itemHeightPC 			= 	'10';
	const itemGap				=	'0';
	
	// menu т.е. меню слева
	const menuXPC				=	'8';
	const menuYPC				=	'13';
	const menuWidthPC			=	'15';
	const menuHeightPC			=	'6';
		
	// description. Картинка бордюра и текст для описания фильма
	const text_descr_align		=	'justify';
	const text_descr_redraw		=	'no';
	const text_descr_lines		=	'1';
	const text_descr_offsetXPC	=	'28';
	const text_descr_offsetYPC	=	'51.8';
	const text_descr_widthPC	=	'67';
	const text_descr_heightPC	=	'26';
	const text_descr_fontSize	=	'12'; 
	const text_descr_backgroundColor	=	'-1:-1:-1';
	const text_descr_foregroundColor	=	'255:255:255'; 
	
	
	const image_poster_offsetXPC	=	'7';
	const image_poster_offsetYPC	=	'55';
	const image_poster_widthPC		=	'15';
	const image_poster_heightPC		=	'34';
	
	
	// itemdisplay. Константы для отображения списка фильмов
	const unfocus_color			=	'255:255:255';
	const focus_color			=	'255:255:255';
	const itemdisplay_offsetXPC	=	'0';
	const itemdisplay_offsetYPC	=	'0';
	const itemdisplay_widthPC	=	'95';
	const itemdisplay_heightPC	=	'90';
	const itemdisplay_fontSize	=	'16'; // размер шрыфта
	const itemdisplay_lines		=	'2'; // кол-во линий в 1 строке фильма
	
	
}

// это константы для окна с списком фильмов в разделе
class ua_rss_list_const extends global_const
{
	
	// items т.е отображение списка файлов фильма 
	const itemBackgroundColor	=	'0:0:0';
	const itemXPC				=	'25';
	const itemYPC				=	'13'; 
	const itemImageXPC			=	'25';
	const itemImageYPC 			= 	'13' ;
	const itemImageWidthPC		=	'0';
	const itemImageHeightPC		=	'0';
	const itemPerPage			=	'5';
	const itemWidthPC			=	'70';
	const itemHeightPC 			= 	'15';
	const itemGap				=	'0';
	
	// menu т.е. меню слева
	const menuXPC				=	'7';
	const menuYPC				=	'13';
	const menuWidthPC			=	'15';
	const menuHeightPC			=	'8';
	
		
	// itemdisplay. Константы для отображения списка фильмов
	const unfocus_color			=	'255:255:255';
	const focus_color			=	'255:255:255';
	const itemdisplay_offsetXPC	=	'11';
	const itemdisplay_offsetYPC	=	'2';
	const itemdisplay_widthPC	=	'80';
	const itemdisplay_heightPC	=	'90';
	const itemdisplay_fontSize	=	'16'; // размер шрыфта
	const itemdisplay_lines		=	'3'; // кол-во линий в 1 строке фильма
	// itemdisplay. Константы для постеров списка фильмов
	
	const item_image_display_offsetXPC	=	'1';
	const item_image_display_offsetYPC	=	'9';
	const item_image_display_widthPC	=	'9';
	const item_image_display_heightPC	=	'85';

	
}

class ua_rss_download_const extends ua_rss_list_const
{
	const itemYPC				=	'14'; 
	const itemImageYPC 			= 	'14' ;
	
	const down_item_image_display_offsetXPC	=	'1';
	const down_item_image_display_offsetYPC	=	'9';
	const down_item_image_display_widthPC	=	'9';
	const down_item_image_display_heightPC	=	'85';
	
	const down_itemdisplay_offsetXPC	=	'11';
	const down_itemdisplay_offsetYPC	=	'7';
	const down_itemdisplay_widthPC	=	'80';
	const down_itemdisplay_heightPC	=	'90';
	const down_itemdisplay_fontSize	=	'15'; // размер шрыфта
	const down_itemdisplay_lines		=	'2'; // кол-во линий в 1 строке фильма
	
	const down_item_percent_display_offsetXPC	=	'92';
	const down_item_percent_display_offsetYPC	=	'6';
	const down_item_percent_display_widthPC	=	'10';
	const down_item_percent_display_heightPC	=	'90';
	const down_item_percent_display_fontSize	=	'15'; // размер шрыфта
	const down_item_percent_display_lines		=	'1'; // кол-во линий в 1 строке фильма
		
	//  надписи статус, имя файла, выполнено
	const status_header_offsetXPC		=	'24';
	const status_header_offsetYPC		=	'9.5';
	const status_header_widthPC			=	'10';
	const status_header_heightPC		=	'5';
	const status_header_fontSize		=	'14'; // размер шрыфта
	const status_header_backgroundColor	=	'-1:-1:-1';
	const status_header_foregroundColor	=	'200:200:100'; 
	
	const name_header_offsetXPC			=	'50';
	const name_header_widthPC			=	'15';
	
	const done_header_offsetXPC			=	'81';
	const done_header_widthPC			=	'15';
	
}


?>