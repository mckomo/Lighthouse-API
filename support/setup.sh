# sudo apt-get install language-pack-pl

# NGINX
sudo apt-get update && sudo apt-get upgrade -fy
sudo apt-get -y install nginx

# PHP
sudo apt-add-repository -y ppa:ondrej/php5-5.6
sudo apt-get update && sudo apt-get install php5-cli -y php5-fpm php5-mcrypt php5-curl

# COMPOSER
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# GIT
sudo apt-get install -y git-core

# JAVA
sudo add-apt-repository ppa:webupd8team/java
sudo apt-get update && sudo apt-get install -y oracle-java8-installer

# ELASTIC SEARCH
wget -qO - https://packages.elastic.co/GPG-KEY-elasticsearch | sudo apt-key add -
echo "deb http://packages.elastic.co/elasticsearch/1.7/debian stable main" | sudo tee -a /etc/apt/sources.list.d/elasticsearch-1.7.list
sudo apt-get update && sudo apt-get install -y elasticsearch
sudo update-rc.d elasticsearch defaults 95 10

# REDIS
sudo add-apt-repository -y ppa:rwky/redis
sudo apt-get update && sudo apt-get install -y redis-server
