#!/usr/bin/awk -f
#
#   http://code.google.com/media-translate/
#   Copyright (C) 2011  Serge A. Timchenko
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

# @include json.awk

BEGIN { gAllLines = "" }
{ gAllLines = gAllLines "\n" $0 }

END { 
    ParseJSON(gAllLines, gJsonData); 

		print "<?xml version='1.0' encoding='utf-8'?>"
		print "<playlist version='1' xmlns='http://xspf.org/ns/0/'>"
		print "<title>Совок ТВ - Онлайн телевидение</title>"
   	print "<image>http://sovok.tv/templates/default/css/img/logo.png</image>"
   	print "<trackList>"
    
    lenList = JSONArrayLength(gJsonData,"list");
    for(i=1; i <= lenList; i++)
    {
      print "<track>"
      print "<title>" gJsonData["list",i,"name"] "</title>"
      cid = gJsonData["list",i,"cid"];
      print "<image>http://sovok.tv/images/tv/" cid ".gif</image>"
      print "<location>http://sovok.tv/api.php?channel=" cid "</location>"
      print "</track>"
    }
    print "</trackList>"
    print "</playlist>"
}
