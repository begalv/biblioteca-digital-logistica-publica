#!/bin/bash
set -e

# Lista explícita de variáveis a substituir (evita conflito com $vars do PHP)
ENVVARS='${POSTGRES_DB} ${POSTGRES_HOST} ${POSTGRES_PORT} ${POSTGRES_USER} ${POSTGRES_PASSWORD} ${NOURAU_SITE_URL} ${NOURAU_WEBMASTER_EMAIL} ${PORTAL_URL}'

# Gerar config.php a partir do template com variáveis de ambiente
envsubst "$ENVVARS" < /var/www/html/manager/config.php.template > /var/www/html/manager/config.php
envsubst "$ENVVARS" < /var/www/html/manager/config_d.php.template > /var/www/html/manager/config_d.php

# Garantir permissões
chown www-data:www-data /var/www/html/manager/config.php
chown www-data:www-data /var/www/html/manager/config_d.php
chown -R www-data:www-data /nourau

exec "$@"
