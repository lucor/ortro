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


#!/bin/sh

if [ $# != 3 ]; then
        echo "Usage: $0 log_file reg_exp init_size"
        exit 1
fi

LOG_FILE=$1
REG_EXP=$2
INIT_SIZE=$3
TMP_FILE="/tmp/check_log_$$.tmp"

ACTUAL_SIZE=`ls -l ${LOG_FILE} | awk '{print $5}'`

if [ ${ACTUAL_SIZE} -lt $INIT_SIZE ]; then
    INIT_SIZE=0
fi

echo ${ACTUAL_SIZE} > ${TMP_FILE}

tail -c +$((${INIT_SIZE} + 1)) "${LOG_FILE}" | egrep "${REG_EXP}" >> ${TMP_FILE}

EXIT_CODE=$?

if [ $? -eq 0 ]; then
    EXIT_CODE=0
else 
    EXIT_CODE=2
fi

cat ${TMP_FILE}
rm ${TMP_FILE}

exit ${EXIT_CODE}