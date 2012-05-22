##################################################################################
# Ortro
# Copyright (C) 2006 and following years, Luca Corbo <lucor@ortro.net>
# 
# This software is published under the terms of the GPL License
# a copy of which has been included with this distribution in the LICENSE file.
# 
# File Search script
#  This allows you to search files using a pattern and checks for an expected number of occurences
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
# The boolean operator
OPERATOR=$4
# The number of occourrence expected
EXPECTED_OCCURRENCE=$5


TEMP_FILE="/tmp/ortro_file_search_`date +%Y%m%d`.txt"
if [ "x$RECURSIVE" = "x1" ]
	then 
		find $DIR_PATH -name "$SEARCH_FOR" -type f > $TEMP_FILE
	else
		find $DIR_PATH/. ! -name . -prune -name "$SEARCH_FOR" -type f > $TEMP_FILE
fi

NUM_OF_OCCURRENCES=`cat $TEMP_FILE | wc -l`

#List of files found
echo "Files found:"
cat $TEMP_FILE

#Remove temp file
rm $TEMP_FILE

`test $NUM_OF_OCCURRENCES $OPERATOR $EXPECTED_OCCURRENCE`
exit $?