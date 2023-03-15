FROM php:8.0-apache

# Install CURL and MySQL extensions
RUN apt-get update && \
    apt-get install -y libcurl4-openssl-dev libpq-dev git && \
    docker-php-ext-install pdo_mysql mysqli

WORKDIR /var/www/html

# Clone the repository
RUN rm -rf /var/www/html/* && \
    git clone https://github.com/yuantuo666/baiduwp-php.git /tmp/baiduwp-php && \
    cp -r /tmp/baiduwp-php/* /var/www/html/ && \
    rm -rf /tmp/baiduwp-php

# 配置 Apache 服务器
RUN a2enmod rewrite

# 将容器的80端口暴露出来
EXPOSE 80