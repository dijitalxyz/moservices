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
  UNESCAPEXML = 1;
  location = "";
  title = "";
  creator = "";
  
  if(position == "")
    position = 1;
    
  counter = 0;

  while ( getXML(ARGV[1],1) ) 
  {
    if(XTYPE == "TAG")
    {
      if(XITEM == "track")
      {
        counter += 1;
      }
      else
      if(XITEM == "location")
      {
        location = "";
        while( getXML(ARGV[1],0) )
        {
            if(XTYPE == "DAT" || XTYPE == "CDA")
                location = location XITEM;
            else if (XTYPE == "END")
                break;
        }
      }
      else
      if(XITEM == "title")
      {
        title = "";
        while( getXML(ARGV[1],0) )
        {
            if(XTYPE == "DAT" || XTYPE == "CDA")
                title = title XITEM;
            else if (XTYPE == "END")
                break;
        }
      }
      else
      if(XITEM == "creator")
      {
        creator = "";
        while( getXML(ARGV[1],0) )
        {
            if(XTYPE == "DAT" || XTYPE == "CDA")
                creator = creator XITEM;
            else if (XTYPE == "END")
                break;
        }
      }
    }
    else if(XTYPE == "END")
    {
        if(XITEM == "track")
        {
          if(counter == position)
            break;
        }
    }
  }
  
  print location;
  print title;
  print creator;
  exit;
}
