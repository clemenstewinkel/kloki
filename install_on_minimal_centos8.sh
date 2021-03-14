#!/bin/bash

dnf -y install https://rpms.remirepo.net/enterprise/remi-release-8.rpm
dnf -y module enable php:remi-7.4
dnf -y install tar zip wget git httpd php php-pdo php-json php-xml php-intl npm mod_ssl mariadb-server php-pecl-imagick php-pecl-mysql

echo "#################################################"
echo "# install composer "
echo "#################################################"
wget https://getcomposer.org/installer
php installer
rm installer
mv composer.phar /usr/bin/composer

echo "#################################################"
echo "# USE Composer version 1.x!! "
echo "#################################################"
composer self-update 1.10.1

echo "#################################################"
echo "# install yarn "
echo "#################################################"
npm install --global yarn

echo "#################################################"
echo "# Open firewall for port 80"
echo "#################################################"
firewall-cmd --permanent --add-port 80/tcp
firewall-cmd --reload

# Setup Database-Server
systemctl start mariadb
systemctl enable mariadb
echo "#################################################"
echo "# Running mysql_secure_installation"
echo "#################################################"
mysql_secure_installation

echo "#################################################"
echo "# Create Database, log in using the password "
echo "# you just entered in mysql_secure_installation"
echo "#################################################"
echo "
create database kloki;
grant all privileges on kloki.* TO 'kloki'@'localhost' identified by 'pwtest123';
flush privileges;
" | mysql -u root -p

cd /var/www/
echo "#################################################"
echo "# Getting kloki from github..."
echo "#################################################"
git clone https://github.com/clemenstewinkel/kloki

cd kloki
echo "#################################################"
echo "# Installing required PHP-Libraries"
echo "#################################################"
composer install

echo "#################################################"
echo "# Installing Javascript-Libraries"
echo "#################################################"
yarn install

echo "#################################################"
echo "# Compiling Javascript for frontend"
echo "#################################################"
yarn build

# Ownership
chown apache:apache -R .
cp kloki_httpd.conf /etc/httpd/conf.d/

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


# Create DB-Tables
echo "#################################################"
echo "# Creating database tables..."
echo "#################################################"
bin/console do:mi:mi -n

# Load dummy data 
echo "#################################################"
echo "# Filling Database"
echo "#################################################"
bin/console do:fi:lo -n

echo "#################################################"
echo "# Starting webserver and enable on boot..."
echo "#################################################"
systemctl start httpd
systemctl enable httpd

echo "#################################################"
echo "# Installation of KloKi Event-Management finished"
echo "# Take you browser and log in using:"
echo "#   User: admin@test.de"
echo "#   Password: admin"
echo "#################################################"

