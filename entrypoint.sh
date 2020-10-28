#!/bin/bash
set -e

gosu web mkdir -p /var/app/var/{cache,log}

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
elif [[ "$1" == "behat" ]]; then
  gosu web ./vendor/bin/behat
  if [[ "$2" == "watch" ]]; then
    unset IFS
    inotifywait -rme create,delete,modify,move --exclude '.*\.(swp|swx|.*~)$' ./src ./tests ./features ./config | while read LINE; do
      # debounce clodo
      JOBS=`jobs -r -p | xargs`
      if [ ! -z $JOBS ]; then
        kill -9 $JOBS
      fi

      (
          sleep 1
          gosu web ./bin/console doctrine:fixtures:load -q
          gosu web ./vendor/bin/behat --strict < /dev/null
      )&
    done
  fi
  exit
fi

exec "$@"
