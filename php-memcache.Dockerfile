FROM php:cli
LABEL authors="aherczeg"

# Install memcache extension with mlocati docker-php-extension-installer
RUN curl -sSL https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions -o - | sh -s \
      memcache
