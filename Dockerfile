FROM debian:stretch

ARG UUID=1000
ARG GGID=1000
ARG USER=web

RUN if [ ! -z $(getent group $GGID) ] ; then groupmod -o -g 2019292 $(getent group $GGID | cut -d: -f1) ; fi && \
    addgroup --system --gid $GGID $USER && \
    if [ ! -z $(getent passwd $UUID) ] ; then usermod -o -u 2019292 $(getent passwd $UUID | cut -d: -f1) ; fi && \
    useradd -l --system --home-dir /var/cache/$USER  --shell /sbin/nologin --uid $UUID --gid $GGID $USER

RUN apt-get update && apt-get install -y --force-yes wget apt-transport-https && \
    wget -O /etc/apt/trusted.gpg.d/php.gpg https://packages.sury.org/php/apt.gpg && \
    echo "deb https://packages.sury.org/php/ stretch main" > /etc/apt/sources.list.d/php.list

RUN apt-get update && apt-get install -y --force-yes \
    git gettext-base unzip curl php7.4-cli php7.4-apcu php7.4-curl php7.4-fpm php7.4-curl php7.4-gd php7.4-intl php7.4-mbstring \
    php7.4-pgsql php7.4-pdo-pgsql php7.4-xml php7.4-xdebug gosu apt-transport-https lsb-release ca-certificates \
    libfontconfig1 libxrender1 php7.4-zip inotify-tools


RUN sed -i".back" s/\;date\.timezone\ \=.*/date\.timezone\ \=\ Europe\\/Paris/ /etc/php/7.4/fpm/php.ini && \
    sed -i".back" s/\;date\.timezone\ \=.*/date\.timezone\ \=\ Europe\\/Paris/ /etc/php/7.4/cli/php.ini && \
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer && \
    mkdir -p /var/run/php


ARG RELEASE_TAG
ENV RELEASE=$RELEASE_TAG
COPY --chown=web:web . /var/app

USER $USER

WORKDIR "/var/app"

EXPOSE 9000
VOLUME "/var/app/public"
ENTRYPOINT [ "/var/app/entrypoint.sh" ]

USER root
CMD /usr/sbin/php-fpm7.4 --nodaemonize