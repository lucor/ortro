#!/usr/bin/ksh
# Author: Fabrizio Cardarello <hunternet\[at\]users.sourceforge.net
# Description: This script check the idle time of the CPUs by sar command
# and compare it with you threshold value.
# keep in minde less value of idle time means that the CPUs are too busy.
# Argument: $1 is a threshold
#
# Modified by: Francesco Acquista <f.acquista@gmail.com>
#

RESULT_FILE=/tmp/ortroCpuIdleResult
sar -u 10 1 > $RESULT_FILE

IDLE_COL_N=$(cat $RESULT_FILE | grep % | tr " " "\n" | grep -v [AP]M | cat -b | grep idle | awk {'print $1'})

THRESHOLD=${1:-90}
VAR=$(cat $RESULT_FILE | tail -1 | awk '{print $'$IDLE_COL_N'}' | tr , .)
echo $VAR

THRESHOLD_CONV=$(echo "$THRESHOLD * 10000" | bc | awk -F. {'print $1'})
VAR_CONV=$(echo "$VAR * 10000" | bc | awk -F. {'print $1'})
[ $VAR_CONV -gt $THRESHOLD_CONV ] && exit 0 || exit 1
