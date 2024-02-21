#syntax=docker/dockerfile:1.3-labs

FROM php:7.4-cli-alpine

COPY . /app
WORKDIR /app

RUN apk add git bash

RUN <<EOT
  curl -s https://getcomposer.org/installer -o /tmp/composer-setup.php 
  php /tmp/composer-setup.php --install-dir=/usr/bin 
  rm /tmp/composer-setup.php 
  ln -s /usr/bin/composer.phar /usr/bin/composer 
EOT

ENV PATH="${PATH}:/app/vendor/bin"

# Generate an entry_point script
COPY <<EOF /usr/bin/entry_point.sh
#!/bin/sh

/usr/bin/composer install

exec \"\$@\"
EOF
RUN chmod 755 /usr/bin/entry_point.sh

ENTRYPOINT [ "/usr/bin/entry_point.sh" ]
