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
  processContent = 0;
  processMenu = 0;
  key = "";

  while ( getXML(ARGV[1],1) ) 
  {
    if(XTYPE == "TAG")
    {
      if(XITEM == "opml")
      {
        print "<channel>";
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
        
        if(processContent == 1 && XATTR["type"] == "audio")
        {
          if(depth == 1)
          {
            print "<item>"
            for(attr in XATTR)
              printf "  <%s>%s</%s>\n", attr, XATTR[attr], attr;
            print "</item>"
          }
        }
        else
        if(processMenu == 1 && XATTR["type"] == "link")
        {
          if(depth == 1)
          {
            menuSize += 1;
            printf "menuArray = pushBackStringArray(menuArray, \"%s\");\n", XATTR["text"];
            printf "menuLinkArray = pushBackStringArray(menuLinkArray, \"%s\");\n", XATTR["URL"];
          }
        }
        else
        if(XATTR["type"] == "container")
        {
          if(XATTR["key"] == "content" && processContent == 0)
          {
            processContent = 1;
            depth = 0;
          }
          else
          if(XATTR["key"] == "navigation" && processMenu == 0)
          {
            processMenu = 1;
            depth = 0;
            menuSize = 0;
            print "<menuSize>";
            print "<script>";
            print "menuArray = null;";
            print "menuLinkArray = null;";
          }
        }
      }
    }
    else
    if(XTYPE == "END")
    {
      if(XITEM == "opml")
      {
        print "</channel>"
      }
      else
      if(XITEM == "outline")
      {
        if(processMenu == 1 && depth == 0)
        {
          print "menuArray = pushBackStringArray(menuArray, \"Поиск\");";
          print "menuLinkArray = pushBackStringArray(menuLinkArray, \"=search=\");";
          printf "menuSize = %d;\n", menuSize+1;
          print "</script>";
          print "</menuSize>";
          processMenu = 0; 
        }
        else
        if(processContent == 1 && depth == 0)
        {
          processContent = 0;
        }
        depth--;
      }
    }
  }
}

