FROM php:8.2-apache

# Extensões PHP necessárias para o Nou-Rau
RUN apt-get update && apt-get install -y \
        libpq-dev \
        libgd-dev \
        libzip-dev \
        libcurl4-openssl-dev \
        libonig-dev \
        locales \
        gettext-base \
        poppler-utils \
        antiword \
        catdoc \
    && docker-php-ext-install \
        pgsql \
        gd \
        zip \
        curl \
        mbstring \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Locale pt_BR para processamento de texto
RUN sed -i '/pt_BR.UTF-8/s/^# //' /etc/locale.gen && locale-gen

# Habilitar mod_rewrite
RUN a2enmod rewrite

# Copiar configuração do Apache
COPY apache.conf /etc/apache2/sites-available/000-default.conf

# Criar diretórios do Nou-Rau
RUN mkdir -p /nourau/archive /nourau/incoming /nourau/remove /nourau/log /nourau/capas \
    && chown -R www-data:www-data /nourau

# Copiar código-fonte do Nou-Rau
COPY nourau-src/ /var/www/html/manager/

# Copiar imagens (capas etc.)
COPY images/ /var/www/html/images/

# Copiar templates de configuração
COPY config.php.template /var/www/html/manager/config.php.template
COPY config_d.php.template /var/www/html/manager/config_d.php.template

# Copiar entrypoint
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Permissões
RUN chown -R www-data:www-data /var/www/html/manager

EXPOSE 80

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["apache2-foreground"]
