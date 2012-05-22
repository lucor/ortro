#!/usr/bin/ksh
# Author: Fabrizio Cardarello
# Description: this script check by SMF command if a service is online
# Argument:    the first argument rappresent the service's FMRI
# example:     svc-check.ksh svc:/network/nfs/server

FMRI=${1:-not_set}
echo $FMRI
[ $( svcs $FMRI | tail -1 | awk '{print $1}' ) != 'online' ] && exit 1 || exit 0
