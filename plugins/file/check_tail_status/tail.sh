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
#         		Marcello Sessa <zodd81@users.sourceforge.net>
#
##################################################################################

# The first time that comes read the file
FIRST_TIME=`tail -1 $1`
sleep $2
# The second time that comes read the file
SECOND_TIME=`tail -1 $1`

echo $FIRST_TIME
echo 
echo
echo $SECOND_TIME

if [ "x$FIRST_TIME" = "x$SECOND_TIME" ]
    then
    exit 1
else
    exit 0
fi
