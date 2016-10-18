#!/usr/bin/env bash

if [ ! -z "$KAT_API_KEY" ]; then
  echo 'KAT_API_KEY is not set!' && exit 1
fi

curl -so ~/dailydump.txt.gz "https://kat.cr/api/get_dump/daily/?userhash=${KAT_API_KEY}"
gzip -df ~/dailydump.txt.gz

php /var/www/lighthouse-api/artisan torrents:import ~/dailydump.txt