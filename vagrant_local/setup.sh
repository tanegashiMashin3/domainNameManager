#!/bin/bash

yum -y install epel-release
rpm -Uvh http://rpms.famillecollet.com/enterprise/remi-release-6.rpm

# Apacheのインストール
yum -y install httpd

service httpd restart

# phpのインストール
sed -i "s/mirrorlist=https/mirrorlist=http/" /etc/yum.repos.d/epel.repo
yum -y install --enablerepo=remi,remi-php70 php php-devel php-xml php-common php-cli php-pear php-pdo php-mysqlnd php-opcache php-gd php-mbstring php-mcrypt php-fpm

# mysqlのインストール
yum -y install http://dev.mysql.com/get/mysql-community-release-el6-5.noarch.rpm
yum -y install mysql-client mysql-server

# 自動起動の設定
chkconfig httpd on
chkconfig mysqld on
chkconfig iptables off
