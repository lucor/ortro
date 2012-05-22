#!/usr/bin/ksh
# Author: Fabrizio Cardarello
# email: <hunternet\[at\]users.sourceforge.net>
# Description:
# this script check if all the zpool need a scrubbing.

[ `zpool status | grep scrub | grep -v none | wc -l`  -ne 0 ] && exit 1
