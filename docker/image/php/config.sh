#!/usr/bin/env bash

if [[ ! "${build_env}" = "prod" ]]; then
    pecl install xdebug-beta
    echo "zend_extension=xdebug.so" > /usr/local/etc/php/conf.d/xdebug.ini
fi
