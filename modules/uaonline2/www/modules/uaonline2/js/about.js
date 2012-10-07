function openAbout(url)
{
	if (window.XMLHttpRequest) {
		xmlHttp = new XMLHttpRequest();
        }
	else
	{// code for IE6, IE5
		xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
	}
	xmlHttp.open("GET", url, false);
	xmlHttp.send();

	df = document.getElementById('about_list');
	df.innerHTML = xmlHttp.responseText;

	dd = document.getElementById('about_container');
	dd.style.visibility = 'visible';
}

function closeAbout()
{
	dd = document.getElementById('about_container');
	dd.style.visibility = 'hidden';
}
