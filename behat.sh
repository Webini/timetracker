#!/bin/bash

COMPOSE_FILE=./docker-compose.test.yml
COMPOSE_PREFIX=test_timetracking

function safe-doco() {
  docker-compose -p $COMPOSE_PREFIX -f $COMPOSE_FILE $@
  RESULT=$?
  if [ $RESULT -ne 0 ]; then
    docker-compose -p $COMPOSE_PREFIX -f $COMPOSE_FILE down
    exit $RESULT
  fi
}

safe-doco run --rm --entrypoint /bin/bash app -c "/var/app/wait-db.sh"
echo "Migrate..."
safe-doco run --rm app console doctrine:migration:migrate -q
echo "Load fixtures..."
safe-doco run --rm app console doctrine:fixtures:load -q
echo "Start behat..."
safe-doco run --rm app behat $1
echo "Cleaning..."
docker-compose -p $COMPOSE_PREFIX -f $COMPOSE_FILE down