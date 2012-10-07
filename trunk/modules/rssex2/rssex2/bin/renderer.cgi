#!/bin/sh
#
#   http://code.google.com/media-translate/
#   Copyright (C) 2010  Serge A. Timchenko
#
#   This program is free software: you can redistribute it and/or modify
#   it under the terms of the GNU General Public License as published by
#   the Free Software Foundation, either version 3 of the License, or
#   (at your option) any later version.
#
#   This program is distributed in the hope that it will be useful,
#   but WITHOUT ANY WARRANTY; without even the implied warranty of
#   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#   GNU General Public License for more details.
#
#   You should have received a copy of the GNU General Public License
#   along with this program. If not, see <http://www.gnu.org/licenses/>.
#

DAT_FILE=/tmp/xspf_renderer.dat
STATUS_FILE=/tmp/xspf_renderer.status

urldecode()
{
  awk '
  BEGIN{
  	for(i = 0; i < 10; i++)
  		hex[i] = i
  	hex["A"] = hex["a"] = 10
  	hex["B"] = hex["b"] = 11
  	hex["C"] = hex["c"] = 12
  	hex["D"] = hex["d"] = 13
  	hex["E"] = hex["e"] = 14
  	hex["F"] = hex["f"] = 15
  }
  {
  	gsub(/\+/, " ")
  	i = $0
  	while(match(i, /%../)){
  		if(RSTART > 1)
  			printf "%s", substr(i, 1, RSTART-1)
  		printf "%c", hex[substr(i, RSTART+1, 1)] * 16 + hex[substr(i, RSTART+2, 1)]
  		i = substr(i, RSTART+RLENGTH)
  	}
  	print i
  }
  '
}

if [ "$QUERY_STRING" = "renderer=stop" -o "$QUERY_STRING" = "renderer=pause" -o "$QUERY_STRING" = "renderer=play" ]; then
  echo ${QUERY_STRING} | sed 's/=/-/;s/.*/{\0}/' > $DAT_FILE
  sleep 5
elif echo "$QUERY_STRING" | grep -qs "url="; then
  echo ${QUERY_STRING} | sed 's/.*url=//' | urldecode > $DAT_FILE
  sleep 5
fi

RENDERER_STATUS='not running'
RENDERER_URL=''

if [ -f $STATUS_FILE ]; then
  RENDERER_STATUS=`sed -n '1p' $STATUS_FILE`
  RENDERER_URL=`sed -n '2p' $STATUS_FILE`
  #rm -f $STATUS_FILE
fi

if [ -n "$QUERY_STRING" ]; then
  echo "Content-type: text/plain"
  echo ""
  echo "(function(){ return { status:\"$RENDERER_STATUS\", url:\"$RENDERER_URL\"}; })();"
else

cat <<EOF
Content-type: text/html

<html><head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="Pragma" content="no-cache">
<title>Media Stream Renderer - Control</title>
<style>
  body { background-color: #FFFFFF; color: #000000; }
  .btn { width:60px; }
  li { padding-top:5px; }
</style>
<script type="text/javascript" language="javascript">
  var makeRequest = function(para) {
    var http_request = false;
    if (window.XMLHttpRequest) { // Mozilla, Safari, ...
      http_request = new XMLHttpRequest();
      if (http_request.overrideMimeType) {
              http_request.overrideMimeType('text/plain');
      }
    } else if (window.ActiveXObject) { // IE
      try {
        http_request = new ActiveXObject("Msxml2.XMLHTTP");
      } catch (e) {
        try {
          http_request = new ActiveXObject("Microsoft.XMLHTTP");
        } catch (e) {}
      }
    }
    if (!http_request) {
      alert('errorXMLHTTP');
      return null;
    }
    var url = "$SCRIPT_NAME" + para;
    http_request.onreadystatechange = function() { updateContents(http_request); };
    http_request.open('get', url, true);
    http_request.setRequestHeader("If-Modified-Since", "Thu, 1 Jan 1970 00:00:00 GMT");
    http_request.setRequestHeader("Cache-Control", "no-cache");    
    http_request.send(null);
  }
  function updateContents(http_request) {
    if (http_request.readyState == 4) {
      if (http_request.status == 200) {
        var data = eval(http_request.responseText);
        document.getElementById('status').innerHTML = data.status;
        var textElement = document.getElementById('url');
        if(document.activeElement != textElement)
    	    textElement.value = data.url;
      } 
      //else { alert('request trouble'); }
    }
  }
  window.onload = function() {
    setInterval(function(){makeRequest("?refresh")}, 5000);
  }
</script>
</head>
<body>
EOF
echo "<table border=0 cellpadding=10>"
echo "<tr><td colspan=2><table border=0 width=100%><td><h1><a href=\"$SCRIPT_NAME\">Media Stream Renderer</h1></a></td><td align=right>"
cat <<EOF
<img src="data:image/png;base64,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
GMcAAAAASUVORK5CYII="/>
EOF
echo "</td></tr></table><hr></td></tr>"
echo "<tr><td>Status:</td><td><span id=status>$RENDERER_STATUS</span></td></tr>"
echo "<tr><td>Stream URL:</td><td><form action=\"$SCRIPT_NAME\" method=get><input id=url type=text name=url value=\"$RENDERER_URL\" size=64><input class=btn type=button value=Submit onclick='makeRequest(\"?url=\"+encodeURIComponent(document.getElementById(\"url\").value))'></form></td></tr>"

echo "<tr><td>Renderer control:</td>"
echo "<td><form action=\"$SCRIPT_NAME\" method=get><input class=btn type=button name=renderer value=stop onclick='makeRequest(\"?renderer=stop\")'></form>"
echo "<form action=\"$SCRIPT_NAME\" method=get><input class=btn type=button name=renderer value=pause onclick='makeRequest(\"?renderer=pause\")'></form>"
echo "<form action=\"$SCRIPT_NAME\" method=get><input class=btn type=button name=renderer value=play onclick='makeRequest(\"?renderer=play\")'></form></td>"
echo "</tr>"
echo "<tr><td colspan=2 align=center><hr></td></tr>"
echo "</table>"
echo "<h3>Internet browser extensions <a href="http://code.google.com/p/media-translate/wiki/ModuleMediaStreamRenderer#Add-on_%D0%B4%D0%BB%D1%8F_%D0%B8%D0%BD%D1%82%D0%B5%D1%80%D0%BD%D0%B5%D1%82_%D0%B1%D1%80%D0%B0%D1%83%D0%B7%D0%B5%D1%80%D0%BE%D0%B2"><sup>[?]</sup></a></h3>"
echo "<ul>"
echo "<li><a href="/bin/playonplayer.xpi">Firefox</a></li>"
echo "<li><a href="/bin/playonplayer.exe">Internet Explorer</a></li>"
echo "<li><a href="/bin/playonplayer.crx">Google Chrome</a></li>"
echo "</ul>"

echo "</body>"
echo "</html>"

fi