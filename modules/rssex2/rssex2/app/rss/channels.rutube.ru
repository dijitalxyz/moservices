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

DATAPATH=$BASEPATH/app/rss
cd $DATAPATH

TEMPFEED=$CACHEPATH/mediafeed.rss 

cat > $TEMPFEED <<EOF
<?xml version="1.0" encoding="utf-8"?>
<rss version="2.0">
	<channel>
		<title>Телеканалы на Rutube</title>
		<link>http://rutube.ru/channels.html</link>
		<description>Телеканалы на RuTube - В эфире</description>
		<language>ru</language>
		<image>
			<url>../etc/translate/rss/image/rutube.png</url>
  		<link>http://rutube.ru/channels.html</link>
		</image>
EOF

local RUTUBE_TV_URL="http://rutube.ru/channels.html"

$MSDL -q -p http -o - "${RUTUBE_TV_URL}" | $XCODE -s +k -w | $TOUTF8 | sed 's/koi8-r/utf-8/' > $TMPFILE

local pages=`awk '/Страниц:/ { match($0, /.*Страниц:[^<]*<span>([^<]*)<\/span>.*/, arr); print arr[1]; exit; }' $TMPFILE`
local current=1

if [ -x $XSLTPROC ]; then
  $XSLTPROC --encoding utf-8 --html channels.rutube.xslt $TMPFILE 2>/dev/null | sed '1d' >> $TEMPFEED
fi

while [ $current -ne $pages ];
do
  $MSDL -q -p http -o - "${RUTUBE_TV_URL}?p=$current" | $XCODE -s +k -w | $TOUTF8 | sed 's/koi8-r/utf-8/' > $TMPFILE
  if [ -x $XSLTPROC ]; then
    $XSLTPROC --encoding utf-8 --html channels.rutube.xslt $TMPFILE 2>/dev/null | sed '1d' >> $TEMPFEED
  fi
  let current=$current+1
done

cat >> $TEMPFEED <<EOF
	</channel>
</rss>
EOF

rm -f $TMPFILE

printContent "application/rss+xml" "$TEMPFEED"
  
