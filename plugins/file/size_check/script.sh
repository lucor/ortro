##################################################################################
# Ortro
# Copyright (C) 2006 and following years, Luca Corbo <lucor@ortro.net>
# 
# This software is published under the terms of the GPL License
# a copy of which has been included with this distribution in the LICENSE file.
# 
# File Size Check.
#  This script allows you to monitor the files size undere a specified diretory
# 
# File Authors:
# 		Luca Corbo <lucor@ortro.net>
#
##################################################################################


# The absolute path of folder to apply retention policy
DIR_PATH=$1
# The search pattern
SEARCH_FOR=$2
# Is it a recursive search? (0 = false, 1 = true)
RECURSIVE=$3
# File size threshold (bytes)
SIZE_TRESHOLD=$4

if [ "x$RECURSIVE" = "x1" ]
	then 
		FILES_OVER_SIZE=`find $DIR_PATH -name "$SEARCH_FOR" -type f -size +"$SIZE_TRESHOLD"c`
	else
		FILES_OVER_SIZE=`find $DIR_PATH/. ! -name . -prune -name "$SEARCH_FOR" -type f -size +"$SIZE_TRESHOLD"c`	
fi
if [ "x$FILES_OVER_SIZE" != "x" ]
then
	for i in $FILES_OVER_SIZE
	do
		FILE_NAME=`basename $i`
		FILE_SIZE=`ls -l $i | awk '{ print $5 }'`
		echo "$FILE_NAME size -> $FILE_SIZE bytes"
	done
	exit 1
fi