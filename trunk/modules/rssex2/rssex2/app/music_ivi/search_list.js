// search_list.js

/*
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
*/


var videos = globalObj.modules[0].descriptor.resources.videos;

var Item = function(prop){
  var xmlEscape = function(s) { return s.replace(/&/g, '&amp;').replace(/>/g, '&gt;').replace(/</g, '&lt;').replace(/"/g, '&quot;'); }
  this.prop = prop;
  this.constructor.prototype.print = function() {
    print("<item>");
    for(var key in this.prop)
      print("<"+key+">"+xmlEscape(this.prop[key])+"</"+key+">");
    print("</item>");
  }
}

for(var i=0; i<videos.length; i++) {
  var prop = {
  	  artist: Utf8.encode(videos[i].artists_text),
  	  song: Utf8.encode(videos[i].title),
  	  location: "http://music.ivi.ru/watch/" + videos[i].url,
  	  "media:thumbnail": videos[i].thumb10
  };
  if(prop.artist && prop.song)
    prop.title = prop.artist + " - " + prop.song;
  else if(prop.song)
    prop.title = prop.song;
  else
    prop.title = prop.artist;
	new Item(prop).print();
}

