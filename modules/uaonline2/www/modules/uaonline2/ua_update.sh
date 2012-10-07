#!/bin/sh
#	------------------------------
#	Ukraine online services 2	
#	install standalone script for 
#	UAOnline2 v.1.3
#	------------------------------
#	Created by Sashunya 2012
#	wall9e@gmail.com			
#	------------------------------ 
xtreamer=$2
install_dir=$1
#install_dir='/tmp/usbmounts/sda1/scripts/uaonline'
tmp_dir='/tmp/ua_inst'
update_url=$3
#mos3='http://www.moservices.org/mos3'
if wget -c -O /tmp/core.tar.bz2 $update_url/core.tar.bz2 > /dev/null 2>&1
then
	if wget -c -O /tmp/uaonline2.tar.bz2 $update_url/uaonline2.tar.bz2 > /dev/null 2>&1
	then
		mkdir -p $install_dir
		mkdir -p $install_dir/js
		mkdir -p $tmp_dir
		tar -xjf /tmp/core.tar.bz2 -C $tmp_dir www/modules/core/js/about.js
		tar -xjf /tmp/core.tar.bz2 -C $tmp_dir www/modules/core/js/navbar.js
		tar -xjf /tmp/uaonline2.tar.bz2 -C $tmp_dir
		rm $install_dir/*
		cp $tmp_dir/www/modules/core/js/* $install_dir/js
		cp -r $tmp_dir/www/modules/uaonline2/* $install_dir
		if [ $xtreamer = '1' ] ; then
			cp $install_dir/Xtreamering/* /tmp/usbmounts/sda1/scripts/Xtreamering/image
		fi
		rm -r $tmp_dir
		rm -r $install_dir/Xtreamering/
		rm /tmp/core.tar.bz2
		rm /tmp/uaonline2.tar.bz2
		echo "1" > /usr/local/etc/dvdplayer/ua_standalone.conf
		echo "OK"		
	fi
fi