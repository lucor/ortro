#################################################################################
# Ortro
# Copyright (C) 2006 and following years, Luca Corbo <lucor@ortro.net>
# 
# This software is published under the terms of the GPL License
# a copy of which has been included with this distribution in the LICENSE file.
# 
# Script for Web Test using Slimdog
# 
# File Authors:
# 		Luca Corbo <lucor@ortro.net>
#
##################################################################################

JAVA_BIN_PATH=$1
shift
SLIMDOG_HOME=$1
shift

CP=${CLASSPATH}:${SLIMDOG_HOME}
for lib in ${SLIMDOG_HOME}/lib/*.jar; do
    CP=${CP}:${lib};
done

${JAVA_BIN_PATH}/java -classpath ${CP} -jar ${SLIMDOG_HOME}/webtester.jar $*
