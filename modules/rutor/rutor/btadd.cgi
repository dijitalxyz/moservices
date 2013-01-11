#!/bin/sh
trans=/usr/local/etc/mos/trans

echo -e "Content-type: application/xml\n"
echo "<?xml version='1.0' encoding=\"UTF-8\" ?>"

ID=$(echo "$QUERY_STRING" | sed 's/[a-z\.\:\/\?\=]*//g')
if wget -q -O /tmp/$ID.torrent $QUERY_STRING; then
	if $trans/transmission-remote '127.0.0.1:9091' -n torrent:1234 -a /tmp/$ID.torrent | grep -q 'success'; 	then
		echo '<info><stream text="Торрент успешно добавлен в Transmission" /></info>'
		exit 0
	else
		echo '<info><stream text="Ошибка при добавлении в Transmission" /></info>'
		exit 1
	fi
else
	echo '<info><stream text="Невозможно скачать торрент файл!" /></info>'
	exit 1
fi
