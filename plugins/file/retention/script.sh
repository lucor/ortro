##################################################################################
# Ortro
# Copyright (C) 2006 and following years, Luca Corbo <lucor@ortro.net>
# 
# This software is published under the terms of the GPL License
# a copy of which has been included with this distribution in the LICENSE file.
# 
# File Retention script
# 		This script allows you to compress and/or remove files older then 
#	    a specified retention period
# 
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

## Compress section
# Program used for compress (none, gzip, compress)
COMPRESS_PROGRAM=$4

if [ "x$5" != "xnone" ]
	then
		COMPRESS_PROGRAM_PATH=$5$4
	else
		COMPRESS_PROGRAM_PATH=$4
fi

# Retention period 
COMPRESS_RETENTION=$6

## Delete section
# Remove flag (1 = remove, 0 skip)
DELETE_FLAG=$7
# Retention period
DELETE_RETENTION=$8


#####################################################
#### Main program
#####################################################

## Compress files

if [ "x$COMPRESS_PROGRAM" != "x0" ]
then

  echo "Check for files to compress...."
  CHECK_COMPRESS_PATH=`which $COMPRESS_PROGRAM_PATH`
  if [ "x$CHECK_COMPRESS_PATH" = "x" ]
  	then
  		echo "$COMPRESS_PROGRAM_PATH : command not found. Please check your ENV"
  		exit 1
  fi	 

  if [ "x$RECURSIVE" = "x1" ]
	then 
		FILES_TO_COMPRESS=`find $DIR_PATH -name "$SEARCH_FOR" -type f -mtime +$COMPRESS_RETENTION`
	else
		FILES_TO_COMPRESS=`find $DIR_PATH/. ! -name . -prune -name "$SEARCH_FOR" -type f -mtime +$COMPRESS_RETENTION`	
  fi 
  if [ "x$FILES_TO_COMPRESS" = "x" ]
  then
		echo "No files found."
  else
	  for i in $FILES_TO_COMPRESS
		  do
			`$COMPRESS_PROGRAM_PATH $i`
			EXIT_CODE=$?
		        echo -n "Attempt to compress $i"
			if [ "x$EXIT_CODE" = "x0" ]
			then
				echo " -> Done."
			else 
				echo " -> Failed!"
				ERROR=1
			fi
  	  done 
   fi
   echo "Compress files done."
fi

if [ "x$DELETE_FLAG" != "x0" ]
then
  echo "Check for files to delete...."
	if [ "x$DELETE_FLAG" = "x1" ]
		then
			SEARCH_COMPRESSED=""
		else
			if [ "x$COMPRESS_PROGRAM" = "xgzip" ]
				then
					SEARCH_COMPRESSED=".gz"
				else
					SEARCH_COMPRESSED=".Z"
			fi
	fi
  if [ "x$RECURSIVE" = "x1" ]
    then
	    FILES_TO_DELETE=`find $DIR_PATH -name "$SEARCH_FOR"$SEARCH_COMPRESSED -type f -mtime +$DELETE_RETENTION`
	else
		FILES_TO_DELETE=`find $DIR_PATH/. ! -name . -prune -name "$SEARCH_FOR"$SEARCH_COMPRESSED -type f -mtime +$DELETE_RETENTION`
  fi
  if [ "x$FILES_TO_DELETE" = "x" ]
  then
  	echo "No files found."
  else
	  for i in $FILES_TO_DELETE 
	  do
		`rm -f $i`
		EXIT_CODE=$?
  		echo -n "Attempt to remove $i"
		if [ "x$EXIT_CODE" = "x0" ]
		then
			echo " -> Done."
		else
			echo " -> Failed!"
		        ERROR=1
		fi
	  done
  fi
  echo "Delete files done."
fi

# Check for error 
if [ "x$ERROR" = "x1" ]
then
	exit 1
fi
