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
  ARGUMENTS+=( ${@:2} )
  exec "gosu" ${ARGUMENTS[@]}
  exit
elif [[ "$1" == "composer" ]]; then
  ARGUMENTS=( web composer )
  ARGUMENTS+=( ${@:2} )
  exec "gosu" ${ARGUMENTS[@]}
  exit
elif [[ "$1" == "behat" ]]; then
  if [[ "$2" == "watch" ]]; then
    unset IFS
    ARGUMENTS+=( ${@:3} )
    inotifywait -rme create,delete,modify,move --exclude '.*\.(swp|swx|.*~)$' ./src ./tests ./features ./config | while read LINE; do
      # debounce clodo
      JOBS=`jobs -r -p | xargs`
      if [ ! -z $JOBS ]; then
        kill -9 $JOBS
      fi

      (
          sleep 1
#          echo "Reload fixtures..."
#          gosu web ./bin/console doctrine:fixtures:load --purge-with-truncate -q
          echo "Behat time"
          gosu web ./vendor/bin/behat ${ARGUMENTS[@]} --strict < /dev/null
      )&
    done
  else
    ARGUMENTS+=( ${@:2} )
    gosu web ./vendor/bin/behat ${ARGUMENTS[@]} --strict < /dev/null
  fi
  exit
elif [[ "$1" == "phpunit" ]]; then
  exec "gosu" web ./vendor/bin/phpunit ${@:2}
fi

exec "$@"
