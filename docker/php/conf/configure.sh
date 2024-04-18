#!/usr/bin/env bash

envsubst </tmp/php.ini.template >/usr/local/etc/php/conf.d/custom.ini

if [ "1" == "$PHP_XDEBUG_ENABLE" ]; then
  export PHP_EXTENSION_DIR="$(php-config --extension-dir)"
  envsubst </tmp/xdebug.ini.template >/usr/local/etc/php/conf.d/xdebug.ini
fi
