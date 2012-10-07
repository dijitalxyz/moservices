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

function escapeXML( text )
{
  gsub( "\\&",  "&amp;",  text );
  gsub( "'",  "&apos;", text );
  gsub( "\"", "&quot;", text );
  gsub( ">",  "&gt;",   text );
  gsub( "<",  "&lt;",   text );
  return text;
}

function getData(name, s) 
{
  match(s, "\"" name "\":\"([^\"]+)\"", arr);
  return prsJSON_UnescapeString(arr[1]);
}

function changeVoice() 
{
    if(voice == "Vladimir")
        voice = "Anna"
    else
        voice = "Vladimir"
}

BEGIN { gAllLines = "" }

{ gAllLines = gAllLines "\n" $0 }

END {
  
	print "<?xml version='1.0' encoding='utf-8'?>"
	print "<playlist version='1' xmlns='http://xspf.org/ns/0/'>"
	print "<title>Radio RSS - " channel"</title>"
   	print "<image>http://rssradio.ru/img/channels/" channel ".png</image>"
   	print "<trackList>"
   	
   	srand()
   	if(rand()>.5)
        voice = "Anna"
    else
        voice = "Vladimir"
    
      print "<track>"
      print "<title>В эфире...</title>"
      uri = "http://rssradio.ru/get_speech.php?channel=" channel "&voice=" voice
      print "<location>" escapeXML(uri) "</location>"
      print "<meta rel=\"stream_url\">" escapeXML(uri) "</meta>"
      print "<meta rel=\"stream_class\">audio</meta>"
      print "<meta rel=\"stream_type\">audio/mpeg</meta>"
      print "<meta rel=\"stream_protocol\">http</meta>"
      print "</track>"
      
      
    lenList = split(gAllLines, data, "},{");
     
    for(i=1; i <= lenList; i++)
    {
      changeVoice();
      s = data[i];
      print "<track>"
      print "<title>" escapeXML(getData("Title", s)) "</title>"
      uri = getData("TitleSound", s) "&voice=" voice;
      print "<location>" escapeXML(uri) "</location>"
      print "<meta rel=\"stream_url\">" escapeXML(uri) "</meta>"
      print "<meta rel=\"stream_class\">audio</meta>"
      print "<meta rel=\"stream_type\">audio/mpeg</meta>"
      print "<meta rel=\"stream_protocol\">http</meta>"
      print "</track>"
      print "<track>"
      print "<title>...</title>"
      uri = getData("DescriptionSound", s) "&voice=" voice;
      print "<location>" escapeXML(uri) "</location>"
      print "<meta rel=\"stream_url\">" escapeXML(uri) "</meta>"
      print "<meta rel=\"stream_class\">audio</meta>"
      print "<meta rel=\"stream_type\">audio/mpeg</meta>"
      print "<meta rel=\"stream_protocol\">http</meta>"
      print "</track>"
    }
    print "</trackList>"
    print "</playlist>"
}
