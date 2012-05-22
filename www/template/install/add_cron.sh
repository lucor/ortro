##################################################################################
# Ortro
# Copyright (C) 2006 and following years, Luca Corbo <lucor@ortro.net>
# 
# This software is published under the terms of the GPL License
# a copy of which has been included with this distribution in the LICENSE file.
# 
# Add Ortro to the user crontab
# 
# File Authors:
# 		Luca Corbo <lucor@ortro.net>
#
##################################################################################

ORTRO_PATH=$1
ORTRO_INSTALL=$2
if [ "x$3" = "x" ]
	then
		PHP_PATH=`which php`
	else
		PHP_PATH="$3"'php'
fi
ORTRO_CRON_HEADER='### Ortro crontab'
ORTRO_CRON='* * * * * '"$PHP_PATH"' '"$ORTRO_PATH"'bin/crontab.php 2>&1 > '"$ORTRO_PATH"'log/crontab.log'

cat "$ORTRO_INSTALL"crontab_backup > "$ORTRO_INSTALL"ortro_crontab
`echo "$ORTRO_CRON_HEADER" >> "$ORTRO_INSTALL"ortro_crontab`
`echo "$ORTRO_CRON" >> "$ORTRO_INSTALL"ortro_crontab`
`crontab "$ORTRO_INSTALL"ortro_crontab`
cat "$ORTRO_INSTALL"ortro_crontab
