#!/bin/bash
# Install CentOS 8, minimale Installation
# Update php7.4

dnf -y install https://rpms.remirepo.net/enterprise/remi-release-8.rpm
dnf -y module enable php:remi-7.4
dnf -y install tar zip wget git httpd php php-pdo php-json php-xml php-intl npm mod_ssl mariadb-server php-pecl-imagick php-pecl-mysql

# install composer
wget https://getcomposer.org/installer
php installer
rm installer
mv composer.phar /usr/bin/composer

# install yarn
npm install --global yarn

# Start Webserver on boot
systemctl enable httpd

# open firewall 
firewall-cmd --permanent --add-port 80/tcp
firewall-cmd --reload

# Setup Database-Server
systemctl start mariadb
systemctl enable mariadb
echo "#################################################"
echo "# Running mysql_secure_installation"
echo "#################################################"
mysql_secure_installation

echo "
create database kloki;
grant all privileges on kloki.* TO 'kloki'@'localhost' identified by 'pwtest123';
flush privileges;
" | mysql -u root -p

cd /var/www/
git clone https://github.com/clemenstewinkel/kloki

cd kloki
composer install
yarn install
yarn build

# Ownership
chown apache:apache -R .
cp kloki_httpd.conf /etc/httpd/conf.d/

# File permissions, recursive
#find . -type f -exec chmod 0644 {} \;
 
# Dir permissions, recursive
#find . -type d -exec chmod 0755 {} \;
 
echo "#################################################"
echo "# Configure SELinux..., can take some time...."
echo "#################################################"
# SELinux serve files off Apache, resursive
chcon -t httpd_sys_content_t . -R
 
# Allow write only to specific dirs
chcon -t httpd_sys_rw_content_t uploaded_files/ -R
chcon -t httpd_sys_rw_content_t var/ -R

# Allow apache to connect to DB (takes some time...)
setsebool -P httpd_can_network_connect_db 1

echo "#################################################"
echo "# Filling Database"
echo "#################################################"

# Create DB-Tables
bin/console do:mi:mi

# Load dummy data 
bin/console do:fi:lo

echo "#################################################"
echo "# Starting webserver..."
echo "#################################################"
systemctl start httpd

echo "#################################################"
echo "# Installation of KloKi Event-Management finished"
echo "# Take you browser and log in using:"
echo "#   User: admin@test.de"
echo "#   Password: admin"
echo "#################################################"

