#!/usr/bin/ksh
# Author: Fabrizio Cardarello
# email: hunternet@users.sourceforge.net
# Description:
# Script for checking the state of metadevice
# Argument: $1 metadevice
# if $1 is missing the default value is d0 (root mirror)

METADEVICE=${1:-d0}
[ -z "$(metastat $1 | grep State | grep Okay)" ] && exit 1 || exit 0