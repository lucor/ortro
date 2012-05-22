/usr/sbin/a2dissite ortro-apache2.conf 2>&1 >/dev/null && /etc/init.d/apache2 reload
if [ -d /var/lib/ortro ]
then
	rm -rf /var/lib/ortro
fi
if [ -d /var/log/ortro ]
then
	rm -rf /var/log/ortro
fi
