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
  base = "";
  title = "";
  content_id = "";

  while ( getXML(ARGV[1],1) ) 
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
      else
      if(XITEM == "video")
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
    }
  }
}
END {
  print title;
  n = asorti(VIDEO, KEY)
  str = "";
  for(i=1; i<=n; i++)
  {
    str = KEY[i] " " VIDEO[KEY[i]];
    print str;
  } 
  for(;i<=3; i++)
  {
  	print str;
  }
}
