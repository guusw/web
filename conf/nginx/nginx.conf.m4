worker_processes  1;

events {
    worker_connections  1024;
}

http {
    include       mime.types;
    default_type  application/octet-stream;

    log_format  main  '[$time_local] $remote_addr "$request"'
                      '\n  $status / $bytes_sent bytes'
                      '\n  $http_referer / $http_user_agent'
                      '\n  fastcgi_script_name: $fastcgi_script_name'
                      '\n  document_root: $document_root'
                      '\n  document_uri:  $document_uri'
                      '\n  request_uri:   $request_uri'
                      '\n  query_string:  $query_string';

    access_log  logs/access_nginx.log  main;
    error_log logs/error_nginx.log error;
    rewrite_log on;

    sendfile        on;
    #tcp_nopush     on;

    #keepalive_timeout  0;
    keepalive_timeout  65;

    #gzip  on;

    # Maximum upload size
    client_max_body_size 512m;
    client_body_timeout 5m;
    client_body_temp_path ROOT_PATH/tmp;

    # HTTP File server redirect
    server {
        listen HTTP_PORT;
        server_name ~^(?<subdomain>.+\.)?(?<domain>[^.]+)\.(?<tld>[^.]+)$;

        # Required for certbot
        location ^~ /.well-known {
            autoindex on;
            alias CERTBOT_WELL_KNOWN_PATH/.well-known;
        }

define(`REDIR_URL_PORT', ifelse(HTTPS_PUBLIC_PORT, `443', `', `:HTTPS_PUBLIC_PORT'))dnl
define(`CREATE_REDIR_URL', `https://$subdomain$domain.$tld$1$request_uri')dnl
        location ~ ^(.*)$ {
            return 302 CREATE_REDIR_URL(REDIR_URL_PORT);
        }
    }

    # Coffee provider
    server {
        listen HTTPS_PORT;
        server_name ~^(?<subdomain>.+\.)?COFFEE_SERVER_NAME$;
        ssl on;

        ssl_certificate ifdef(`CERT_TEST', testcrt.txt, CERTBOT_LIVE/COFFEE_SERVER_NAME/fullchain.pem);
        ssl_certificate_key ifdef(`CERT_TEST', testkey.txt, CERTBOT_LIVE/COFFEE_SERVER_NAME/privkey.pem);

        location / {
            index Index.php;
            root DOCUMENT_ROOT/coffee;
        }

        location ~* ^(.*)\.php$ {
            root DOCUMENT_ROOT/coffee;

            fastcgi_split_path_info ^(.+?\.php)(/.*)$;
            if (!-f DOCUMENT_ROOT/coffee$fastcgi_script_name) {
                return 404;
            }

            include fastcgi.conf;

            # Mitigate https://httpoxy.org/ vulnerabilities
            fastcgi_param HTTP_PROXY "";
            fastcgi_pass PHP_BIND;
        }
    }

    # Data Subdomain
    server {
        listen HTTPS_PORT;
        server_name TDRZ_DATA_SUBDOMAIN.SERVER_NAME;
        ssl on;
        ssl_certificate ifdef(`CERT_TEST', testcrt.txt, CERTBOT_LIVE/SERVER_NAME/fullchain.pem);
        ssl_certificate_key ifdef(`CERT_TEST', testkey.txt, CERTBOT_LIVE/SERVER_NAME/privkey.pem);

        location ~ ^/(.*)$ {
            rewrite ^/(.*)$ Request.php?f=$1;
            break;
            
            root DOCUMENT_ROOT/tdrz;

            include fastcgi.conf;

            # Mitigate https://httpoxy.org/ vulnerabilities
            fastcgi_param HTTP_PROXY "";
            fastcgi_pass PHP_BIND;
        }
    }

    # HTTPS File server
    server {
        listen HTTPS_PORT;
        server_name SERVER_NAME;
        ssl on;
        ssl_certificate ifdef(`CERT_TEST', testcrt.txt, CERTBOT_LIVE/SERVER_NAME/fullchain.pem);
        ssl_certificate_key ifdef(`CERT_TEST', testkey.txt, CERTBOT_LIVE/SERVER_NAME/privkey.pem);

        # Raw data files
        location ~ ^/raw(.*)$ {
            root TDRZ_DATA_DIR;
            try_files $1 =404;
            expires 1d;
        }

        location / {
            index index.php Index.php index.html Index.html;
            root DOCUMENT_ROOT/tdrz;
        }

        location ~* ^(.*)\.php$ {
            root DOCUMENT_ROOT/tdrz;

            fastcgi_split_path_info ^(.+?\.php)(/.*)$;
            if (!-f DOCUMENT_ROOT/tdrz$fastcgi_script_name) {
                return 404;
            }

            include fastcgi.conf;

            # Mitigate https://httpoxy.org/ vulnerabilities
            fastcgi_param HTTP_PROXY "";
            fastcgi_pass PHP_BIND;
        }

    }

    # Fallback
    server {
        server_name _;
        listen HTTP_PORT default_server;
        listen HTTPS_PORT ssl default_server;
        ssl_certificate testcrt.txt;
        ssl_certificate_key testkey.txt;
        return 404;
    }
}
