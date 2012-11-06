#!/bin/sh

# Created by pyro1000

# RTMP RAW LINK
OPTION1=`echo "$QUERY_STRING" | grep -oE "(^|[?&])rtmp-raw=[^&]+" |  sed "s/%20/ /g" | cut -f 2 -d "=" | head -n1`

# Page URL
OPTION2=`echo "$QUERY_STRING" | grep -oE "(^|[?&])pageUrl=[^&]+" | sed "s/%20/ /g" | cut -f 2 -d "="`

# SWF URL
OPTION3=`echo "$QUERY_STRING" | grep -oE "(^|[?&])swfUrl=[^&]+" | sed "s/%20/ /g" | cut -f 2 -d "="`

# SWF HASH
OPTION4=`echo "$QUERY_STRING" | grep -oE "(^|[?&])swfhash=[^&]+" | sed "s/%20/ /g" | cut -f 2 -d "="`

# LIVE
OPTION5=`echo "$QUERY_STRING" | grep -oE "(^|[?&])live=[^&]+" | sed "s/%20/ /g" | cut -f 2 -d "="`

# PLAY PATH
OPTION6=`echo "$QUERY_STRING" | grep -oE "(^|[?&])playpath=[^&]+" | sed "s/%20/ /g" | cut -f 2 -d "="`

# SWF SIZE
OPTION7=`echo "$QUERY_STRING" | grep -oE "(^|[?&])swfsize=[^&]+" | sed "s/%20/ /g" | cut -f 2 -d "="`

# SWF VFY
OPTION8=`echo "$QUERY_STRING" | grep -oE "(^|[?&])swfVfy=[^&]+" | sed "s/%20/ /g" | cut -f 2 -d "="`

# TC URL
OPTION9=`echo "$QUERY_STRING" | grep -oE "(^|[?&])tcurl=[^&]+" | sed "s/%20/ /g" | cut -f 2 -d "="`

# APP
OPTION10=`echo "$QUERY_STRING" | grep -oE "(^|[?&])app=[^&]+" | sed "s/%20/ /g" | cut -f 2 -d "="`

# TIMEOUT
OPTION11=`echo "$QUERY_STRING" | grep -oE "(^|[?&])timeout=[^&]+" | sed "s/%20/ /g" | cut -f 2 -d "="`

# TOKEN
OPTION12=`echo "$QUERY_STRING" | grep -oE "(^|[?&])token=[^&]+" | sed "s/%20/ /g" | cut -f 2 -d "="`

# BUFFER
OPTION13=`echo "$QUERY_STRING" | grep -oE "(^|[?&])buffer=[^&]+" | sed "s/%20/ /g" | cut -f 2 -d "="`

# HOST
OPTION14=`echo "$QUERY_STRING" | grep -oE "(^|[?&])host=[^&]+" | sed "s/%20/ /g" | cut -f 2 -d "="`

# PROTOCOL
OPTION15=`echo "$QUERY_STRING" | grep -oE "(^|[?&])protocol=[^&]+" | sed "s/%20/ /g" | cut -f 2 -d "="`

# CONNECT
OPTION16=`echo "$QUERY_STRING" | grep -oE "(^|[?&])conn=[^&]+" | sed "s/%20/ /g" | cut -f 2 -d "="`

# FLASHVERSION
OPTION17=`echo "$QUERY_STRING" | grep -oE "(^|[?&])flashVer=[^&]+" | sed "s/%20/ /g" | cut -f 2 -d "="`

# command only
CO=`echo "$QUERY_STRING" | grep -oE "(^|[?&])command=[^&]+" | sed "s/%20/ /g" | cut -f 2 -d "="`



opt2=--pageUrl
opt3=--swfUrl
opt4=--swfhash
opt5=--live
opt6=--playpath
opt7=--swfsize
opt8=--swfVfy
opt9=--tcUrl
opt10=--app
opt11=--timeout
opt12=--token
opt13=--buffer
opt14=--host
opt15=--protocol
opt16=--conn
opt17=--flashVer

if [ "$OPTION2" = "" ]; then
    opt2=""
fi 
if [ "$OPTION3" = "" ]; then
    opt3=""
fi
if [ "$OPTION4" = "" ]; then
    opt4=""
fi
if [ "$OPTION5" = "" ]; then
    opt5=""
fi
if [ "$OPTION6" = "" ]; then
    opt6=""
fi
if [ "$OPTION7" = "" ]; then
    opt7=""
fi
if [ "$OPTION8" = "" ]; then
    opt8=""
fi
if [ "$OPTION9" = "" ]; then
    opt9=""
fi

if [ "$OPTION10" = "" ]; then
    opt10=""
fi

if [ "$OPTION11" = "" ]; then
    opt11=""
fi
if [ "$OPTION12" = "" ]; then
    opt12=""
fi

if [ "$OPTION13" = "" ]; then
    opt13=""
fi

if [ "$OPTION14" = "" ]; then
    opt14=""
fi

if [ "$OPTION15" = "" ]; then
    opt15=""
fi

if [ "$OPTION16" = "" ]; then
    opt16=""
fi

if [ "$OPTION17" = "" ]; then
    opt17=""
fi

if [ "$CO" = "" ]; then

	cat <<EOF
Content-type: video/mp4

EOF
	exec /usr/local/etc/mos/bin/rtmpdump -r $OPTION1  $opt2 $OPTION2  $opt3 $OPTION3  $opt4 $OPTION4  $opt5 $OPTION5  $opt6 $OPTION6  $opt7 $OPTION7  $opt8 $OPTION8  $opt9 $OPTION9  $opt10 $OPTION10  $opt11 $OPTION11  $opt12 $OPTION12  $opt13 $OPTION13  $opt14 $OPTION14  $opt15 $OPTION15  $opt16 $OPTION16  $opt17 $OPTION17 --quiet --live
else
	echo /usr/local/etc/mos/bin/rtmpdump -r $OPTION1 $opt2 $OPTION2  $opt3 $OPTION3 $opt4 $OPTION4  $opt5 $OPTION5  $opt6 $OPTION6  $opt7 $OPTION7  $opt8 $OPTION8  $opt9 $OPTION9  $opt10 $OPTION10  $opt11 $OPTION11  $opt12 $OPTION12  $opt13 $OPTION13  $opt14 $OPTION14  $opt15 $OPTION15  $opt16 $OPTION16  $opt17 $OPTION17 --quiet --live
fi
