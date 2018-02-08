# domainNameManager

## 構成

### HostOS

* VirtualBox 5.1.24
* Vagrant 1.9.7

### GuestOS

* PHP 7.0
* mysql 5.6
* Apache 2.2
* BIND (未インストール）

## setup手順

### GuestOSでの作業
```
sudo cp /home/domainNameManager/vagrant_local/httpd.conf /etc/httpd/conf/httpd.conf
sudo service httpd restart
```

### HostOSでの作業
```
cd /path/to/project/domainNameManager/src/
composer install
(※本番環境では[--no-dev]オプションをつけて実行すること）
```

### 動作確認用URL
http://localhost:8080

