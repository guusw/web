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

    access_log  logs/access.log  main;
    error_log logs/error.log debug;
    rewrite_log on;

    sendfile        on;
    #tcp_nopush     on;

    #keepalive_timeout  0;
    keepalive_timeout  65;

    #gzip  on;

    # HTTP File server redirect
    server {
        listen HTTP_PORT;
        server_name ~^(?<subdomain>.+\.)?(?<domain>[^.]+)\.(?<tld>[^.]+)$;

        # Required for certbot
        location ^~ /.well-known {
            autoindex on;
            alias CERTBOT_WELL_KNOWN_PATH/.well-known;
        }

        location ~ ^(.*)$ {
            return 302 https://$subdomain$domain.$tld:HTTPS_PORT$request_uri;
        }
    }

    # Data Subdomain
    server {
        listen HTTPS_PORT;
        server_name TDRZ_DATA_SUBDOMAIN.SERVER_NAME;
        ssl on;
        ssl_certificate ifdef(`CERT_TEST', testcrt.txt, CERTBOT_LIVE/TDRZ_DATA_SUBDOMAIN.SERVER_NAME/fullchain.pem);
        ssl_certificate_key ifdef(`CERT_TEST', testkey.txt, CERTBOT_LIVE/TDRZ_DATA_SUBDOMAIN.SERVER_NAME/privkey.pem);

        location ~ ^/(.*)$ {
            rewrite ^/(.*)$ Request.php?f=$1;
            break;
            
            root DOCUMENT_ROOT;

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
            root DOCUMENT_ROOT;
        }

        location ~* ^(.*)\.php$ {
            root DOCUMENT_ROOT;

            fastcgi_split_path_info ^(.+?\.php)(/.*)$;
            if (!-f DOCUMENT_ROOT$fastcgi_script_name) {
                return 404;
            }

            include fastcgi.conf;

            # Mitigate https://httpoxy.org/ vulnerabilities
            fastcgi_param HTTP_PROXY "";
            fastcgi_pass PHP_BIND;
        }

    }
}
