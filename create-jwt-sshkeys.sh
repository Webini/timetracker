#!/bin/bash
CURRENT_PATH=$( cd "$(dirname "$0")" ; pwd -P )

#if [ -z "$JWT_PASSPHRASE" ]; then
#    source $CURRENT_PATH/.env
#fi
#if [ -z "$JWT_PASSPHRASE" ]; then
#    echo "JWT_PASSPHRASE not defined"
#    exit 1
#fi

mkdir -p $CURRENT_PATH/config/jwt

ssh-keygen -t rsa -P "" -b 4096 -m PEM -f $CURRENT_PATH/config/jwt/private.pem
rm $CURRENT_PATH/config/jwt/private.pem.pub
openssl rsa -pubout -outform PEM -in $CURRENT_PATH/config/jwt/private.pem -out $CURRENT_PATH/config/jwt/public.pem