#!/bin/bash 
cat > ortro.list <<EOF
%product ortro
%copyright GPL
%description Ortro an open source framework designed to make easy activities for job scheduling and system/application monitoring.
%version 1.3.4
%vendor Fabrizio Cardarello <hunternet@users.sourceforge.net>
%readme /usr/share/ortro/README
%license /usr/share/ortro/LICENSE
%requires apache2,openssh-client,openssh-server,mysql-server,php5,php5-cli,php5-mysql,php5-ldap,php-pear,php5-sqlite
%postinstall < post_install.sh
%postremove < post_remove.sh

EOF
VAR=$1
VAR=${VAR:="notset"}
# PREFIX
PREFIX=deb_dir
# DIR DEF
ETC=$PREFIX/etc/ortro
SHARE=$PREFIX/usr/share/ortro
PHP=$PREFIX/usr/share/php/ortro
WWW=$PREFIX/usr/share/ortro/www

if [ "$VAR" == "copy" ]
then
	echo "Copy file"
	## ETC
	if [ ! -d $ETC ]
	then
		mkdir -p $ETC && echo mkdir $ETC
	fi
	# copy for ETC
	echo ETC
	for FILE in ../../conf/* ./ortro-apache2.conf ./init.inc.php ./init.php
	do
		cp -r $FILE $ETC && echo copy $FILE $ETC
	done
	## PHP
	if [ ! -d $PHP ] 
	then
		mkdir -p $PHP && echo mkdir $PHP
	fi
	# copy for PHP
	echo PHP
	for FILE in ../../lib/*
	do
		cp -r $FILE $PHP && echo copy $FILE $PHP
	done
	## SHARE
	if [ ! -d $SHARE ] 
	then
		mkdir -p $SHARE && echo mkdir $SHARE
	fi
        ## create $SHARE/plugins
        mkdir -p $SHARE/plugins && echo mkdir $SHARE/plugins
        # copy init.inc.php in $SHARE/plugins language files
        cp ./init.inc.php $SHARE/plugins/ && echo copy ./init.inc.php $SHARE/plugins
	# copy for SHARE
	echo SHARE
	for FILE in ../../bin ../../lang
	do
		cp -r $FILE $SHARE && echo copy $FILE $SHARE
	done
        # remove plugins language files
        rm -rf $SHARE/lang/*/plugins/*
	## WWW
	if [ ! -d $WWW ] 
	then
		mkdir -p $WWW && echo mkdir $WWW
	fi
	# copy for WWW
	echo WWW
	for FILE in ../../www/* ../../LICENSE ../../README ../../COPYRIGHT ./init.inc.php
	do
		cp -r $FILE $WWW && echo copy $FILE $WWW
	done
	# copy new crontab.php
	cp ./crontab.php $SHARE/bin
fi
if [ $VAR == "deb" ]
then
	echo "Create Package"
	mkepmlist -g www-data -u root --prefix / ./$PREFIX/ | grep -v svn >> ortro.list
	epm -a all -n -f deb ortro
	cp linux-2.6-all/ortro*.deb ./
	rm -rf linux-2.6-all
    rm -rf deb_dir
    rm -fr ortro.list
fi
[ $VAR == "notset" ] && echo "usage: mkdeb.sh copy|deb"
