http {
    include       mime.types;
    default_type  application/octet-stream;

    sendfile        on;
    #tcp_nopush     on;

    #keepalive_timeout  0;
    keepalive_timeout  65;

    #gzip  on;

    fastcgi_buffers 256 4k;

    # define an easy to reference name that can be used in fastgi_pass
    upstream heroku-fcgi {
        server unix:/tmp/heroku.fcgi.<?=getenv('PORT')?:'8080'?>.sock max_fails=3 fail_timeout=3s;
        keepalive 16;
    }
    
    server {

        server_name localhost;
        listen <?=getenv('PORT')?:'8080'?>;

        root "<?=getenv('DOCUMENT_ROOT')?:getenv('HEROKU_APP_DIR')?:getcwd()?>";

        index index.php;

        location / {
            try_files $uri $uri/ /index.php?$args;
        }

        # define an easy to reference name that can be used in try_files
        location ~ \.php {
            include fastcgi_params;

            fastcgi_index index.php;
            fastcgi_param SCRIPT_FILENAME  $document_root$fastcgi_script_name;
            fastcgi_param PATH_INFO $fastcgi_path_info if_not_empty;
            fastcgi_read_timeout 3600;
            
            fastcgi_pass heroku-fcgi;
        }
        
        error_log stderr;
        access_log /tmp/heroku.nginx_access.<?=getenv('PORT')?:'8080'?>.log;

        # restrict access to hidden files, just in case
        location ~ /\. {
            deny all;
        }
    }
}