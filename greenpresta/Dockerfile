FROM php:5.6-apache
LABEL maintainer="Thomas Nabord <thomas.nabord@prestashop.com>"

ENV PS_DOMAIN="<to be defined>" \
DB_SERVER=some-mysql \
DB_PORT=3306 \
DB_NAME=prestashop \
DB_USER=root \
DB_PASSWD=admin \
DB_PREFIX=ps_ \
ADMIN_MAIL=demo@prestashop.com \
ADMIN_PASSWD=prestashop_demo \
PS_LANGUAGE=en \
PS_COUNTRY=gb \
PS_INSTALL_AUTO=0 \
PS_ERASE_DB=0 \
PS_DEV_MODE=0 \
PS_HOST_MODE=0 \
PS_HANDLE_DYNAMIC_DOMAIN=0 \
PS_FOLDER_ADMIN=admin \
PS_FOLDER_INSTALL=install

RUN apt-get update \
	&& apt-get install -y libmcrypt-dev \
		libjpeg62-turbo-dev \
		libpcre3-dev \
		libpng12-dev \
		libfreetype6-dev \
		libxml2-dev \
		libicu-dev \
		mysql-client \
		wget \
		unzip \
        git \
    && rm -rf /var/lib/apt/lists/* \
    && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install iconv intl mcrypt pdo pdo_mysql mbstring soap gd zip

RUN docker-php-source extract \
	&& if [ -d "/usr/src/php/ext/mysql" ]; then docker-php-ext-install mysql; fi \
	&& if [ -d "/usr/src/php/ext/opcache" ]; then docker-php-ext-install opcache; fi \
	&& docker-php-source delete

ENV PS_VERSION 1.6.1.17

# Prepare install and CMD script
COPY config_files/docker_run.sh /tmp/

# Extract
RUN mkdir -p /tmp/data-ps /var/www/modules /var/www/themes /var/www/override 


#Initial git
RUN cd /var/www \
    && rm -rf html \
    && git init \
    && git pull https://github.com/Sumerin/ZielonySklep.git \
    && chmod +x permit_all.sh \
	&& python3 TescoPars/TescoPars.py \
    && find -type d -exec chmod 777 {} \; \
    && find -type f -exec chmod 777 {} \;
 
# Apache configuration
RUN a2enmod rewrite

# PHP configuration
COPY config_files/php.ini /usr/local/etc/php/

# Run
CMD ["/tmp/docker_run.sh"]

