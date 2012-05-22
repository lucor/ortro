#!/usr/bin/ksh
# Author: Fabrizio Cardarello
# email: <hunternet\[at\]users.sourceforge.net>
# Description: 
# This script check the capacity of the zpool, with an 
# absolute value that you've chosen and passed as second argumet.
# Argument: 
# $1 zpool name
# $2 threshold

if [ -n $1 ] && [ -n $2 ]
then
	ZPOOL=$1 
	MAX=$2
	CAP=`zpool list | grep $ZPOOL |  awk '{print $5}' | sed 's/%//'`
	if [ $CAP -gt $MAX ]
	then
		exit 1	
	fi
fi
