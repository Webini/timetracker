#!/bin/bash
set -e

if [[ "$3" == *"php-fpm7.4"* ]] && [ -z "$DONT_RUN_COMPOSER" ]; then
    gosu web composer install -n
    gosu web bin/console cache:clear
    gosu web bin/console cache:warmup
fi

echo "Exporting php env configuration..."
PHP_CONF_ENV=/etc/php/7.4/fpm/pool.d/www.conf.env
PHP_CONF=/etc/php/7.4/fpm/pool.d/www.conf
AVAILABLE_VARIABLES=$(env | sed -E "s/([^=]+)=.*?/$\1/" | tr "\n" " ")

envsubst "$AVAILABLE_VARIABLES"<"$PHP_CONF_ENV" > $PHP_CONF
echo "Done."

if [[ "$1" == "console" ]]; then
    ARGUMENTS=( web bin/console )
    for arg in "$@"; do
        if [[ "$arg" != "console" ]]; then
            ARGUMENTS+=( "$arg" )
        fi
    done
    exec "gosu" ${ARGUMENTS[@]}
    exit
elif [[ "$1" == "composer" ]]; then
    ARGUMENTS=( web composer )
    for arg in "$@"; do
        if [[ "$arg" != "composer" ]]; then
            ARGUMENTS+=( "$arg" )
        fi
    done
    exec "gosu" ${ARGUMENTS[@]}
    exit
elif [[ "$1" == "test" ]]; then
    gosu web ./vendor/symfony/phpunit-bridge/bin/simple-phpunit
    exit
fi

exec "$@"
