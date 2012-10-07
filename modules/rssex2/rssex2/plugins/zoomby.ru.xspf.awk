#!/usr/bin/awk -f
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

# @include getxml.awk

BEGIN {
  UNESCAPEXML = 0;
  Q = QUALITY-1;

	print "<?xml version='1.0' encoding='UTF-8'?>"
	print "<playlist version='1' xmlns='http://xspf.org/ns/0/'>"
	print "<trackList>"

  while( getXML(ARGV[1],1) ) 
  {
    if(XTYPE == "TAG")
    {
      if(XITEM == "switch")
      {
      	base = XATTR["rtmp"];
        content_id = XATTR["content_id"];
      	if(content_id != "")
      	  base = base content_id "/";
      	title = XATTR["title"];
      	if(XATTR["descr"] != "")
      		title = title " / " XATTR["descr"];
      }
      else if(XITEM == "video")
      {
        br = sprintf("%010d", strtonum(XATTR["system-bitrate"]));
      	if(XATTR["pd"] != "")
        	VIDEO[br] = XATTR["pd"];
       	else if(XATTR["src"] != "")
       		VIDEO[br] = base XATTR["src"];
       	else if(XATTR["stream"] != "")
       		VIDEO[br] = XATTR["stream"];
        else if(XATTR["streamer"] != "") {
          VIDEO[br] = XATTR["streamer"];
          if(content_id != "")
            VIDEO[br] = VIDEO[br] "/" content_id;
          VIDEO[br] = VIDEO[br] "/" XATTR["file"];
      }
    }
    else if(XTYPE == "END")
    {
    	if(XITEM == "switch")
    	{
			  n = asorti(VIDEO, KEY)
			  if(Q > n)
			  	url = VIDEO[KEY[n]];
			  else
			  	url = VIDEO[KEY[Q]];
			  if(url ~ /^rtmp:/ && url ~ EXT)
			    gsub(EXT, EXT EXT, url);
				print "<track>"
	  		print "<title>" title "</title>"
	  		print "<location>" url "</location>"
	  		match(url, /.*\/([^\.]+)\.[^.]+$/, arr);
	  		print "<image>http://static.zoomby.ru/images/music/" arr[1] ".jpg</image>"
	  		print "<meta rel='translate'>Content-type:video/mp4</meta>"
				print "</track>"
		  	delete VIDEO;
		  	delete KEY;
			}
    }
  }
}
END {
	print "</trackList>"
	print "</playlist>"
}
