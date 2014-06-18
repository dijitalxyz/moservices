#!/bin/sh
echo 'Content-type: text/plain

ok'

[ -z "$QUERY_STRING" ] && exit 0

/usr/local/etc/mos/iconmenu/getweather "$QUERY_STRING" >/dev/null 2>&1 &

#mos=/usr/local/etc/mos
#
#export LD_LIBRARY_PATH=$mos/lib:/lib:$LD_LIBRARY_PATH
#export PATH=$mos/bin:$PATH
#
#php -q $mos/www/modules/iconmenu/weather.php 'url='"$QUERY_STRING" >/dev/null 2>&1 &
