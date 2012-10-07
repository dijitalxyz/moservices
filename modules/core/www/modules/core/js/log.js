function log_look()
{
	var pLog  = document.getElementById('log_container');
	var pList = document.getElementById('log_list');
	var pTop  = document.getElementById('log_topic');
	if(pList.style.display == "none")
	{
		pList.style.display = "block";
		pTop.style.background = "#D0D0D0 no-repeat right top url(modules/core/images/btn_hide.png)";
		pLog.style.position = "absolute";
		pLog.style.left     = "0px";
		pLog.style.right    = "0px";
		pLog.style.width    = "auto";
	}
	else {
		pList.style.display = "none";
		pTop.style.background = "#D0D0D0 no-repeat right top url(modules/core/images/btn_show.png)";
		pLog.style.position = "absolute";
		pLog.style.left     = "auto";
		pLog.style.right    = "0px";
		pLog.style.width    = "100px";
	}
}
