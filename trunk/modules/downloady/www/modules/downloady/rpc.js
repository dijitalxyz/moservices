function RPC(action, param, callback) {
	var http_request = CreateHttpRequest();
	var queryString = "rpc.php?action=" + action;

	if (callback) {
		http_request.params = param;
		http_request.onreadystatechange = callback;
		}

	switch(typeof(param)) {
		case "object":		    
			for(key in param) 
				queryString += "&" + encodeURIComponent(key) + "=" + encodeURIComponent(param[key]);
			http_request.open("POST", queryString, !!callback);
			http_request.send('');
			break;
		default:
			http_request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
			http_request.open("POST", queryString, !!callback);
			http_request.send(param);
			break;
		}

	if (callback) return(false);

	if (http_request.status > 299) throw new Error("RPC error. HTTP status " + http_request.status);
	return (http_request.responseXML);
}



function CreateHttpRequest() {
	var http_request = false;

	if (window.XMLHttpRequest) { // Mozilla, Safari, ...
		http_request = new XMLHttpRequest();
		if (http_request.overrideMimeType) {
			http_request.overrideMimeType('text/html');
			// See note below about this line
			}
		}
	else if (window.ActiveXObject) { // IE
		try {
			http_request = new ActiveXObject("Msxml2.XMLHTTP");
			}
		catch (e) {
			try {
				http_request = new ActiveXObject("Microsoft.XMLHTTP");
				} catch (e) {}
			}
		}

	return (http_request);
}
