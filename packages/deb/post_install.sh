/bin/chown -R root:www-data /etc/ortro
/bin/chmod 770 /etc/ortro
/usr/bin/find /etc/ortro -type f | /usr/bin/xargs /bin/chmod 660
/bin/chown -R root:www-data /usr/share/ortro
/bin/chmod 775 /usr/share/ortro
/usr/bin/find /usr/share/ortro -type d | /usr/bin/xargs /bin/chmod 775 
/usr/bin/find /usr/share/ortro -type f | /usr/bin/xargs /bin/chmod 664
/bin/chown -R root:www-data /usr/share/php/ortro
/usr/bin/find /usr/share/php/ortro -type d | /usr/bin/xargs /bin/chmod 755 
/usr/bin/find /usr/share/php/ortro -type f | /usr/bin/xargs /bin/chmod 644
[ ! -d /var/log/ortro ] && mkdir /var/log/ortro && /bin/chown -R root:www-data /var/log/ortro && /bin/chmod 770 /var/log/ortro
[ ! -d /var/lib/ortro ] && mkdir /var/lib/ortro && /bin/chown -R root:www-data /var/lib/ortro && /bin/chmod 770 /var/lib/ortro
ln -s /var/log/ortro /usr/share/ortro/log && /bin/chown -R root:www-data /usr/share/ortro/log
cp /etc/ortro/ortro-apache2.conf /etc/apache2/sites-available/
/usr/sbin/a2ensite ortro-apache2.conf 2>&1
