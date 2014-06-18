#!/bin/sh
#
mos=/usr/local/etc/mos/trans
wget=/usr/local/etc/mos/bin/wget

echo -e "Content-type: application/xml; charset=utf8\n"
echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>"
echo "<return>"

P1=`echo "$QUERY_STRING" | cut -d"," -f 1`
P2=`echo "$QUERY_STRING" | cut -d"," -f 2`
P3=`echo "$QUERY_STRING" | cut -d"," -f 3`
P4=`echo "$QUERY_STRING" | cut -d"," -f 4`

case "$P1" in
  addtorrent)

	case "$P2" in
	  freetorrent)
		QS=`echo "$P3" |  sed 's/torrents-details/download/g' |  sed 's/hit=1//g'`
		;;
	  xtor)
		QS=`echo "$P3" |  sed 's/pornoshara\.tv\/item/pornoshara.tv\/download/g'`
		;;
	  fileszona)
		ID=`$wget -O- "$P3" |  grep "download.php?id=" | sed 's/.*id\=\([0-9]\+\).*/\1/'`
		QS="http://fileszona.com/engine/download.php?id=$ID"
		;;
	  *)
		QS="$P3"
		;;
	esac

	echo "<item><title>"
	ID=`echo "$QS" | sed 's/[a-z\.\:\/\?\=]*//g'`
	$wget -q -O "/tmp/${ID}" "${QS}" 2> /dev/null
	WATCHDIR=/tmp/watch
	TRANSMISSION=`ps | grep '[t]ransmission-daemon'`

	[ ! -d "$WATCHDIR" ] && mkdir -p $WATCHDIR

	if [ -f "/tmp/$ID" ]; then
		if [ -n "$TRANSMISSION" ]; then
			mv /tmp/$ID $WATCHDIR/$ID.torrent
			echo "Торрент будет добавлен в список transmission через некоторое время"
		else
			echo " $BT ОШИБКА: В настоящий момент ни один торрент-клиент не запущен"
		fi
	else
		echo "Невозможно скачать торрент файл"
	fi
	echo "</title></item>"
	;;
esac
echo "</return>"

exit 0
