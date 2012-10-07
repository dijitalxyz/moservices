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

rm -f $TEMPFEED

if [ -x "$CURL" ]; then
    $CURL --compressed -s -o $TEMPFEED "$stream_url"
elif [ -x "$GZIP" ]; then
    $MSDL -q -p http -o - "$stream_url" | $GZIP -df > $TEMPFEED
fi

if [ -f $TEMPFEED ]; then
    echo "Content-type: application/rss+xml"
    echo
    if sed -n '/<?xml/p' $TEMPFEED | grep -qs windows-125; then
        cat $TEMPFEED | $TOUTF8 | sed 's/windows-125./utf-8/'
    else
        cat $TEMPFEED
    fi
fi
