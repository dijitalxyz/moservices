#!/bin/sh
#
if ps | grep -Eq '[r]ebootd' ; then
	touch /tmp/reboot
else
	reboot
fi
