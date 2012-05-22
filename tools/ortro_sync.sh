#!/bin/bash
# Ortro_sync script
# It allows to update your Ortro installation (only php code) starting from a SVN check out.
# author: Luca Corbo <lucor@ortro.net>

### Disclaimer ###
# This is a developer tool, please do not use it in production environments.

###  Environment configuration ###
ORTRO_SVN_ROOT="/home/lucor/ortro.svn/"
ORTRO_INSTALLATION_ROOT="/var/www/ortro/"
ORTRO_SYNC_PATH="/home/lucor/ortro.svn/tools/"

### Please do not modify the lines below ###
rsync -rl --exclude-from=${ORTRO_SYNC_PATH}ortro_sync.exclude ${ORTRO_SVN_ROOT} ${ORTRO_INSTALLATION_ROOT}
mkdir -p ${ORTRO_INSTALLATION_ROOT}www/js/lang/

cp ${ORTRO_INSTALLATION_ROOT}conf/init.inc.php ${ORTRO_INSTALLATION_ROOT}bin/
cp ${ORTRO_INSTALLATION_ROOT}conf/init.inc.php ${ORTRO_INSTALLATION_ROOT}plugins/
cp ${ORTRO_INSTALLATION_ROOT}conf/init.inc.php ${ORTRO_INSTALLATION_ROOT}www/

cp ${ORTRO_INSTALLATION_ROOT}lang/it/js/* ${ORTRO_INSTALLATION_ROOT}www/js/lang/
cp ${ORTRO_INSTALLATION_ROOT}lang/en/js/* ${ORTRO_INSTALLATION_ROOT}www/js/lang/
