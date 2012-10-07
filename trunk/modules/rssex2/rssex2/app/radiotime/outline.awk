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
  
  depth = 0;
  process = 0;

  while ( getXML(ARGV[1],1) ) 
  {
    if(XTYPE == "TAG")
    {
      if(XITEM == "opml")
      {
        print "<outline key=\"root\">";
      }
      else
      if(XITEM == "title" && XPATH == "/opml/head/title")
      {
        value = "";
        getXML(ARGV[1],0); 
        if(XTYPE == "DAT")
        {
          value = XITEM;
          print "<title>" XITEM "</title>";
          getXML(ARGV[1],1); 
        }
      }
      else
      if(XITEM == "outline")
      {
        depth++;
        if(process == 1)
        {
          if(depth == 1)
          {
            printf "<%s", XITEM;
            for(attr in XATTR)
              printf " %s=\"%s\"", attr, XATTR[attr];
          }
        }
        else
        {
          if(optKEY != "" && XATTR["key"] == optKEY)
          {
            process = 1;
            depth = 0;
          }
          else
          if(optTEXT != "" && XATTR["text"] == optTEXT)
          {
            process = 1;
            depth = 0;
          }
        }
      }
      else
      if(optKEY == "" && optTEXT == "" && XITEM == "body" && XPATH == "/opml/body")
      {
        process = 1;
        depth = 0;
      }
    }
    else
    if(XTYPE == "END")
    {
      if(XITEM == "opml")
      {
        print "</outline>"
      }
      else
      if(XITEM == "outline")
      {
        if(process == 1 && depth == 1)
        {
          print "/>"
        }
        depth--;
      }
    }
  }
}

