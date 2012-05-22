#!/bin/sh

DATA=`date +%Y%m%d__%H_%M_%S`


if [ "$5" = "" ] 

then echo "\nUsing telnet.sh without parameters shows this Menu\n"
     echo "Using telnet.sh host port user password command or remote script\n"

#  Please, if the connection is a little slow set a greater sleep parameter.

else ( echo open $1 $2
sleep 3
echo $3 "\r"
sleep 3
echo  $4
sleep 3
echo $5 
sleep 3
) | telnet 2>./telnet$DATA.log

ERROR=`cat ./telnet$DATA.log`
if [ "$ERROR" = "Connection closed by foreign host." ]
then
   exit 0
else
   exit 1
fi

#Remove standard error file
rm ./telnet$DATA.log

fi
