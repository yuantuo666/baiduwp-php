FROM php:8.0

# Install CURL and MySQL extensions
RUN apt-get update && \
    apt-get install -y libcurl4-openssl-dev libpq-dev git && \
    docker-php-ext-install pdo_mysql mysqli

WORKDIR /root

RUN php -r "copy('https://install.phpcomposer.com/installer', 'composer-setup.php');" && \
    php composer-setup.php && \
    php -r "unlink('composer-setup.php');" && \
    mv composer.phar /usr/local/bin/composer

WORKDIR /app

# Clone the repository
RUN git clone https://github.com/yuantuo666/baiduwp-php.git /tmp/baiduwp-php && \
    cp -r /tmp/baiduwp-php/* /app/ && \
    rm -rf /tmp/baiduwp-php

RUN composer install

ENTRYPOINT php think run

# 将容器的8000端口暴露出来
EXPOSE 8000