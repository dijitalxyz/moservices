var timer;

function ClearTimer() {
	if (timer) clearTimeout(timer);
}

function SetRefresh(interval) {
	ClearTimer();
	timer = setTimeout(LoadAll, interval);
}

function LoadAll() {
	RPC('list', {sid: ''},  
		function() { 
			if (this.readyState != 4) return;
			var i, arr = this.responseText.split('\n');

			for (i = 0; i < arr.length - 1; i++) {
				UpdateRow(arr[i]);
				}
			var usage = arr[arr.length - 1].split("\t")
			SetDiskUsage(usage[1], usage[2]);
			SetRefresh(usage[0] * 1000);
			}
		);
}

function SetPercent(bar, pct, msg) {
	var divs = bar.getElementsByTagName('div');
	var leftDiv = divs[0];
	var rightDiv = divs[1];
	var msgDiv = divs[2];

	if (isNaN(pct)) pct = 0;

	leftDiv.style.width = parseInt(pct) + '%';
	rightDiv.style.width = 100 - parseInt(pct) + '%';
	msgDiv.innerHTML = msg;
	msgDiv.style.color = (pct > 50 ? "#000000" : "#ffffff");
}

function SetDiskUsage(pct, txt) {
	SetPercent(document.getElementById('usage'), pct, txt);

	var i, odd = true; 
	var TABLEnode = document.getElementById('files');

	for (i = 2; i < TABLEnode.rows.length; i++) {
		TABLEnode.rows[i].className = odd ? 'rowa' : 'rowb';
		if (TABLEnode.rows[i].id.substring(0, 1) != '_') odd = !odd;
		}
	document.getElementById("content").style.visibility = '';
}

function AddRow() {
	var TABLEnode = document.getElementById('files');
	var newrow = TABLEnode.rows[0].cloneNode("true");
	TABLEnode.getElementsByTagName("tbody")[0].appendChild(newrow);
	newrow.style.display = '';
	return(newrow);
}

function UpdateRow(data) {
	var v = data.split('\t');
	if (v.length != 9) {
		alert('Wrong data format\n' + data);
		return;
		}

	var sid = v[0];
	var name = v[1];
	var size = v[2];
	var speed = v[3];
	var pct = v[4];
	var msg = v[5];
	var done = v[6];
	var running = v[7];
	var fexists = v[8];

	var row = document.getElementById(sid);
	var buttons;

	if (!row) {
		row = AddRow();
		buttons = row.cells[4].getElementsByTagName('div');
		buttons[0].innerHTML = '<a href="#" onClick="Log(this)" title="info"><img src="images/dl-info.png"/></a>';
		}
	else {
		buttons = row.cells[4].getElementsByTagName('div');
		}
	row.id = sid;
	row.filename = name;
	row.cells[0].innerHTML = done ? '<a href="#" onClick="Get(this)">' + name + '</a>' : name;
	row.cells[1].innerHTML = size;
	SetPercent(row.cells[2].getElementsByTagName('div')[0], pct, msg);
	row.cells[3].firstChild.innerHTML = ((running > 0) ? speed : (done ? 'Done!' : 'Incomplete'));

	if (running > 0) {
		buttons[2].innerHTML = '<a href="#" onClick="Command(this, \'pause\')" title="pause"><img src="images/dl-pause.png"/></a>';
		buttons[3].innerHTML = '<img src="images/dl-done.png"/>'
		buttons[4].innerHTML = '<img src="images/dl-trash.png"/>'
		} 
	else {
		if (done > 0) {
			if (fexists > 0) {
				buttons[2].innerHTML = '<img src="images/dl-play.png"/>';
				}
			else {
				buttons[2].innerHTML = '<a href="#" onClick="Command(this, \'resume\')" title="resume"><img src="images/dl-restart.png"/></a>';
				}
			}	
		else {
			buttons[2].innerHTML = '<a href="#" onClick="Command(this, \'resume\')" title="resume"><img src="images/dl-play.png"/></a>';
			}
		buttons[3].innerHTML = '<a href="#" onClick="Done(this)" title="done"><img src="images/dl-done.png"/></a>';
		if (fexists > 0) {
			buttons[4].innerHTML = '<a href="#" onClick="if(ConfirmDelete(this)) Command(this, \'trash\')" title="trash"><img src="images/dl-trash.png"/></a>';
			}
		else {
			buttons[4].innerHTML = '<img src="images/dl-trash.png"/>'
			}
		}

	if (done > 0 && fexists > 0) {
		buttons[1].innerHTML = '<a href="#" onClick="Get(this)" title="save"><img src="images/dl-save.png"/></a>';
		}
	else {
		buttons[1].innerHTML = '<img src="images/dl-save.png"/>';
		}
}

function RemoveRow(sid) {
	var row = document.getElementById(sid);
	if (!row) return;
	var TABLEnode = document.getElementById('files');
	TABLEnode.getElementsByTagName("tbody")[0].removeChild(row);

	var detailRow = document.getElementById("_" + sid);
	if (detailRow) {
		TABLEnode.getElementsByTagName("tbody")[0].removeChild(detailRow);
		}
}

function GetDetailRow(sid) {
	var row = document.getElementById(sid);
	if (!row) return(null);

	var buttons = row.cells[4].getElementsByTagName('div');
	buttons[0].innerHTML = '<a href="#" onClick="RemoveDetails(this);" title="close info"><img src="images/dl-close.png"/></a>';

	var TABLEnode = document.getElementById('files');

	var detailRow = document.getElementById("_" + sid);
	if (!detailRow) {
		detailRow = TABLEnode.rows[1].cloneNode("true");
		detailRow.id = "_" + sid;
		if (row.rowIndex == TABLEnode.rows.length - 1) {
			TABLEnode.getElementsByTagName("tbody")[0].appendChild(detailRow);
			}
		else {
			TABLEnode.getElementsByTagName("tbody")[0].insertBefore(detailRow, TABLEnode.rows[row.rowIndex + 1]);
			}			
		detailRow.style.display = '';
		}
	return(detailRow);
}

function SetDetails(sid, url, dst, size, pid) {
	var TABLEnode = GetDetailRow(sid).getElementsByTagName("table")[0];
	TABLEnode.rows[0].cells[1].innerHTML = '<a target="_new" href="' + url + '">' + url + '</a>';
	TABLEnode.rows[1].cells[1].innerHTML = dst;
	TABLEnode.rows[2].cells[1].innerHTML = size;
	TABLEnode.rows[2].cells[3].innerHTML = pid;
}

function SetLog(sid, text) {
	var TABLEnode = GetDetailRow(sid).getElementsByTagName("table")[0];
	TABLEnode.rows[3].cells[0].firstChild.firstChild.appendChild(document.createTextNode(text));
}

function RemoveDetails(e) {
	var sid = GetSID(e);
	var row = document.getElementById(sid);
	if (!row) return;

	var buttons = row.cells[4].getElementsByTagName('div');
	buttons[0].innerHTML = '<a href="#" onClick="Log(this)"><img src="images/dl-info.png"/></a>';

	var detailRow = document.getElementById("_" + sid);
	if (!detailRow) return;

	var TABLEnode = document.getElementById('files');

	TABLEnode.getElementsByTagName("tbody")[0].removeChild(detailRow);
}

function ConfirmDelete(elem) {
	return(confirm("Do you really want to delete the file: " + elem.parentNode.parentNode.parentNode.filename));
}

// -------------- RPC rewrite

function GetSID(e) {
	while(e.tagName != 'TR') e = e.parentNode;
	return(e.id);
	}

function Done(e) {
	RPC('done',
		{ 'sid': GetSID(e) },
		function() { 
			if (this.readyState != 4) return;
			if (this.responseText == '1') { RemoveRow(this.params['sid']) };
			}
		);
}

function Command(e, cmd) {
	RPC(cmd,
		{ 'sid': GetSID(e) },
		function() { 
			if (this.readyState != 4) return;
			if (this.responseText != '0') { UpdateRow(this.responseText) };
			}
		);
}

function Get(e) {
	window.location = "rpc.php?action=get&sid=" + GetSID(e);

}

function Log(e) {
	var sid = GetSID(e);
	RPC('info',
		{ 'sid': sid },
		function() { 
			if (this.readyState != 4) return;
			var data = this.responseText.split("\t");
			SetDetails(sid, data[1], data[2], data[3], data[4]);
			}
		);

	RPC('log',
		{ 'sid': GetSID(e) },
		function() { 
			if (this.readyState != 4) return;
			SetLog(sid, this.responseText);
			}
		);
}
