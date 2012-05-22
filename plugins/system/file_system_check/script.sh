##################################################################################
# Ortro
# Copyright (C) 2006 and following years, Luca Corbo <lucor@ortro.net>
# 
# This software is published under the terms of the GPL License
# a copy of which has been included with this distribution in the LICENSE file.
# 
# File System Check script
#  Get the space available on all currently mounted file systems
# 
# File Authors:
# 		Luca Corbo <lucor@ortro.net>
#
##################################################################################

# Add bdf comman support
PATH_BDF=`which bdf`
IS_PRESENT_BDF=$?
if [ "x$IS_PRESENT_BDF" = "x0" ]
then
  COMMAND="bdf"
else
  COMMAND="df -k"
fi

$COMMAND | sed -e '/^[^     ][^     ]*$/{ N ; s/[   ]*\n[   ]*/ / ;}' | grep '^/' | awk '{print $1 " " $6 " " $5}'
