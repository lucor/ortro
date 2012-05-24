##################################################################################
# Ortro
# Copyright (C) 2006 and following years, Luca Corbo <lucor@ortro.net>
# 
# This software is published under the terms of the GPL License
# a copy of which has been included with this distribution in the LICENSE file.
# 
# Backup the current user crontab
# 
# File Authors:
# 		Luca Corbo <lucor@ortro.net>
#
##################################################################################

ORTRO_INSTALL_PATH=$1
`crontab -l > "$ORTRO_INSTALL_PATH"crontab_backup`
cat "$ORTRO_INSTALL_PATH"crontab_backup