// Copyright 2006-2007 javascript-array.com
// http://javascript-array.com/scripts/simple_drop_down_menu
var timeout	= 500;
var closetimer	= 0;
var ddmenuitem	= 0;

// cancel close timer
function mcancelclosetime()
{
	if(closetimer)
	{
		window.clearTimeout(closetimer);
		closetimer = null;
	}
}

function mopenlayer(id)
{
	dd = document.getElementById(id);
	dd.style.visibility = 'visible';
}

function mcloselayer(id)
{
	dd = document.getElementById(id);
	dd.style.visibility = 'hidden';
}

// open hidden layer
function mopen(id)
{
	// cancel close timer
	mcancelclosetime();

	// close old layer
	if(ddmenuitem) ddmenuitem.style.visibility = 'hidden';

	// get new layer and show it
	ddmenuitem = document.getElementById(id);
	ddmenuitem.style.visibility = 'visible';

}
// close showed layer
function mclose()
{
	if(ddmenuitem)
	{
		ddmenuitem.style.visibility = 'hidden';
		ddmenuitem = null;
	}
}

// go close timer
function mclosetime()
{
	closetimer = window.setTimeout(mclose, timeout);
}

// close layer when click-out
//document.onclick = mclose;
