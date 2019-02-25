#!/usr/bin/env ash

set -e

case "$1" in
    php-dev)
        # Start PHP dev server
        exec php -S 0.0.0.0:8000 -t /srv/app/public
        ;;
    php-fpm)
        # Start PHP-FPM in foreground mode
        exec php-fpm
        ;;
    *)
        exec "$@"
        ;;
esac
