##################################################################################
# Ortro
# Copyright (C) 2006 and following years, Luca Corbo <lucor@ortro.net>
#
# This software is published under the terms of the GPL License v2
# a copy of which has been included with this distribution in the LICENSE file.
#
# This script allows you to monitor the tail of a specified file
#
# File Authors:
#                       Marcello Sessa <zodd81@users.sourceforge.net>
#                       Danilo Alfano <ph4ntom@users.sourceforge.net>
#
##################################################################################

#!/bin/sh

# Date for other applications
GetDate=`date +"%Y%m%d"`

LOG_NM=/fs_app_oracle/ortro/glance.$GetDate

if [ "$2" = "" ]
  then
      echo "     usage: ./glance.sh glance_absolute_path parameter [times]"
      echo ""
      echo "     path : i.e. /opt/perf/bin/"
      echo " parameter: "
      echo "            CPU for cpu usage"
      echo "            IO  for global io peak"
      echo "            MEM for memory used"
      echo "            PRO for total processes started"
      echo "            NET for global packet rate"
      echo "            NFS for global NFS calls\n\n\n"
      echo "     times: calculate the average value on the specified times of checks\n\n"

else

      case $2 in
                  CPU) echo "print GBL_CPU_TOTAL_UTIL" > config.cf
                       ;;
                  IO)  echo "print GBL_DISK_UTIL_PEAK" > config.cf
                       ;;
                  MEM) echo "print GBL_MEM_UTIL" > config.cf
                       ;;
                  PRO) echo "print GBL_ACTIVE_PROC" > config.cf
                       ;;
                  NET) echo "print GBL_NET_PACKET_RATE" > config.cf
                       ;;
                  NFS) echo "print GBL_NFS_CALL_RATE" > config.cf
                       ;;
                  *)   echo "\n call ./glance.sh without parameters for help online\n"
                       ;;
      esac


      if [ "$3" = "" ]
		then TIMES="1"
      else
		TIMES=$3
      fi

      I=0
      SUM=0

      while test "$I" -lt "$TIMES"
      do
	: $((I=I+1))

      $1glance -adviser_only -syntax ./config.cf -iterations 2 -j 2 1>1shadow 2>2shadow
      ERROR=`tail -1 2shadow` 
      if [ "$ERROR" -ne "" ]
         then
             rm 1shadow
             rm 2shadow
             rm config.cf
             echo $ERROR
             exit 1
      fi
 
      RESULT=`tail -1 1shadow`
      : $((RES_C=RESULT*100))
      #echo $RES_C
      : $((SUM=SUM+RES_C))
      done

      : $((AVG_RES_C=SUM/$TIMES))
      : $((AVG_RESULT=AVG_RES_C/100))
      #echo "la media "$AVG_RESULT      
      echo $AVG_RESULT
      rm 1shadow
      rm 2shadow
      rm config.cf
fi
