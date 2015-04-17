#!/usr/bin/env bash

sudo apt-get update
export LC_ALL="en_US.UTF-8"
dpkg-reconfigure locales
sudo apt-get install -y python-software-properties git
sudo apt-add-repository ppa:ondrej/php5
sudo apt-add-repository ppa:chris-lea/redis-server
sudo apt-add-repository ppa:izx/askubuntu
sudo apt-get update
sudo apt-get install -y php5-dev php5-memcached php5-common \
php5-json php5-cli php5-cgi php5-gmp php5-fpm php5 php5-curl php5-intl \
php5-xsl php5-mysqlnd php5-mcrypt php5-readline php5-gd

sudo apt-get install -y npm
ln -sf /usr/bin/nodejs /usr/bin/node
npm install -g gulp
npm install -g bower

apt-get install -y redis-server
apt-get install -y libnotify-bin

 
echo "mysql-server mysql-server/root_password password root" | debconf-set-selections
echo "mysql-server mysql-server/root_password_again password root" | debconf-set-selections
 
sudo apt-get install -y mysql-server mysql-client
mysql -uroot -proot -e "CREATE DATABASE if not exists laravel"

sed -i 's/user = www-data/user = vagrant/g' /etc/php5/fpm/pool.d/www.conf
sed -i 's/group = www-data/group = vagrant/g' /etc/php5/fpm/pool.d/www.conf
service php5-fpm restart

if [ ! -e /usr/bin/composer ]; then
    curl -sS https://getcomposer.org/installer | php
    mv composer.phar /usr/bin/composer
fi
/usr/bin/composer self-update

(cd /vagrant && /usr/bin/composer install)
 
apt-get install -y language-pack-UTF-8
apt-get install -y nginx
rm -rf /etc/nginx/sites-enabled/default
cp /vagrant/.provision/visa_status.conf /etc/nginx/sites-available/visa_status.conf
ln -sf /etc/nginx/sites-available/visa_status.conf /etc/nginx/sites-enabled/visa_status.conf
sed -i 's/sendfile on/sendfile off/g' /etc/nginx/nginx.conf
 
service nginx restart
 

