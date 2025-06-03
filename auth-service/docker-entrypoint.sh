#!/bin/sh
check_mysql() {
  until mysql -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USERNAME" -p"$DB_PASSWORD" -e "SHOW DATABASES;" > /dev/null 2>&1
  do
    echo "Waiting for MySQL at $DB_HOST:$DB_PORT..."
    sleep 3
  done
}

check_mysql

echo "MySQL is up - running migrations..."

php artisan migrate --force

php -S 0.0.0.0:8004 -t public
