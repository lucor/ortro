#!/usr/bin/ksh
# Author: Francesco Acquista <f.acquista@gmail.com>
# Description: This script check the amount of the free memory (percentage)
# and compare it with you threshold value.
# Argument: $1 is a threshold

THRESHOLD=${1:-50}
MEM_TOT=`free | grep Mem | awk {'print $2'}`
MEM_FREE=`free | grep buffers/ | awk {'print $4'}`
FREE_PERC=`echo "$MEM_FREE * 100 / $MEM_TOT" | bc`

echo $FREE_PERC
[ $FREE_PERC -gt $THRESHOLD ] && exit 0 || exit 1
