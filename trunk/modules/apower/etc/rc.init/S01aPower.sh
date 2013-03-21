#!/bin/sh
# extra actions
player=r1

case "$1" in
  start)
	case $player in
	  r1)
		# blue led USB off
		/usr/local/sbin/03 73 0
		# red led off
		/usr/local/sbin/03 76 0
		# blue led on
		/usr/local/sbin/03 75 1
		;;
	  hd2)
		# red led off
		/usr/local/sbin/03 76 0
		# blue led on
		/usr/local/sbin/03 75 1
		# set mute off
		/usr/local/sbin/03 72 1
		# set cooler on
		/usr/local/sbin/03 73 0
		/usr/local/sbin/03 73 1
		;;
	  mele)
		# red led off
		/usr/local/sbin/03 67 0
		# blue led on
		/usr/local/sbin/03 76 1

		stty -F /dev/tts/1 cs8 9600 
		# STANDBY_CMD_SYSTEM_ON
#		echo -en '\xf0\xfe' >/dev/tts/1
		# STANDBY_CMD_MUTE_OFF
		echo -en '\xf9\xfe' >/dev/tts/1
		;;
	  xtr)
		# switch on Green Led
		stty -F /dev/tts/1 cs8 57600
		echo -en '\xfe\x12\x00\xff' >/dev/tts/1

		# set mute off
		/usr/local/sbin/03 102 1
		;;
	  *)
		;;
	esac
	;;
  stop)
	case $player in
	  r1)
		# blue led USB off
		/usr/local/sbin/03 73 0
		# blue led off
		/usr/local/sbin/03 75 0
		# red led on
		/usr/local/sbin/03 76 1
		;;
	  hd2)
		# blue led off
		/usr/local/sbin/03 75 0
		# red led on
		/usr/local/sbin/03 76 1
		# set cooler off
		/usr/local/sbin/03 73 1
		/usr/local/sbin/03 73 0
		# set flag for System LED
		/usr/local/sbin/03 70 1
		;;
	  mele)
		# blue led off
		/usr/local/sbin/03 76 0
		# red led on
		/usr/local/sbin/03 67 1
		# Power Off
		stty -F /dev/tts/1 cs8 9600 
		echo -en '\xf5\xfe' >/dev/tts/1
		;;
	  xtr)
		# Power Off
		stty -F /dev/tts/1 cs8 57600 
		echo -en '\xfe\x11\x00\xff' >/dev/tts/1 
		echo -en '\xfe\x11\x00\xff' >/dev/tts/1 
		;;
	  *)
		;;
	esac
	;;
  *)
	echo $"Usage: $0 {start|stop}"
	;;
esac
