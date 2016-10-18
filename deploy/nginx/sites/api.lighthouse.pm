server {
    listen       443;
    server_name  api.lighthouse.pm;
    root         /var/www/lighthouse-api/public;
    index        index.php index.html index.htm;

    ssl on;
    ssl_certificate     /etc/letsencrypt/live/lighthouse.pm/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/lighthouse.pm/privkey.pem;

    location / {
         try_files $uri $uri/ /index.php$is_args$args;
    }

    # pass the PHP scripts to FastCGI server listening on /var/run/php5-fpm.sock
    location ~ \.php$ {
        try_files $uri /index.php =404;
        fastcgi_pass unix:/var/run/php/php7.0-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}