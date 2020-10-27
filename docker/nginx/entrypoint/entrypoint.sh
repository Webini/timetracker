#!/usr/bin/env bash
set -e

echo "Setting permissions for the docker container..."

UNUSED_USER_ID=21338
UNUSED_GROUP_ID=21337

echo "Fixing permissions."

# Setting Group Permissions
DOCKER_GROUP_CURRENT_ID=`id -g $DOCKER_GROUP`

if [ $DOCKER_GROUP_CURRENT_ID -eq $LOCAL_GROUP_ID ]; then
  echo "Group $DOCKER_GROUP is already mapped to $DOCKER_GROUP_CURRENT_ID. Nice!"
else
  echo "Check if group with ID $LOCAL_GROUP_ID already exists"
  DOCKER_GROUP_OLD=`getent group $LOCAL_GROUP_ID | cut -d: -f1`

  if [ -z "$DOCKER_GROUP_OLD" ]; then
    echo "Group ID is free. Good."
  else
    echo "Group ID is already taken by group: $DOCKER_GROUP_OLD"

    echo "Changing the ID of $DOCKER_GROUP_OLD group to 21337"
    groupmod -o -g $UNUSED_GROUP_ID $DOCKER_GROUP_OLD
  fi

  echo "Changing the ID of $DOCKER_GROUP group to $LOCAL_GROUP_ID"
  groupmod -o -g $LOCAL_GROUP_ID $DOCKER_GROUP || true
  echo "Finished"
  echo "-- -- -- -- --"
fi

# Setting User Permissions
DOCKER_USER_CURRENT_ID=`id -u $DOCKER_USER`

if [ $DOCKER_USER_CURRENT_ID -eq $LOCAL_USER_ID ]; then
  echo "User $DOCKER_USER is already mapped to $DOCKER_USER_CURRENT_ID. Nice!"

else
  echo "Check if user with ID $LOCAL_USER_ID already exists"
  DOCKER_USER_OLD=`getent passwd $LOCAL_USER_ID | cut -d: -f1`

  if [ -z "$DOCKER_USER_OLD" ]; then
    echo "User ID is free. Good."
  else
    echo "User ID is already taken by user: $DOCKER_USER_OLD"

    echo "Changing the ID of $DOCKER_USER_OLD to 21337"
    usermod -o -u $UNUSED_USER_ID $DOCKER_USER_OLD
  fi

  echo "Changing the ID of $DOCKER_USER user to $LOCAL_USER_ID"
  usermod -o -u $LOCAL_USER_ID $DOCKER_USER || true
  echo "Finished"
fi

echo "Done."

echo "Exporting env configuration..."
ENV_CONF_DIRECTORY=/etc/nginx/conf.d.env
CONF_DIRECTORY=/etc/nginx/conf.d
AVAILABLE_VARIABLES=$(env | sed -E "s/([^=]+)=.*?/$\1/" | tr "\n" " ")

find $ENV_CONF_DIRECTORY -type f -exec bash -c "envsubst '$AVAILABLE_VARIABLES' <{} >$CONF_DIRECTORY/\$(basename {}) && echo \"Conf file \$(basename {}) processed.\"" \;
echo "Done."

exec "$@"