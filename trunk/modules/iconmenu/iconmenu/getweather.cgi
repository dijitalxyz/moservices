#!/bin/sh
echo 'Content-type: text/plain

ok'

[ -z "$QUERY_STRING" ] && exit 0

/usr/local/etc/mos/iconmenu/getweather "$QUERY_STRING" >/dev/null 2>&1 &
