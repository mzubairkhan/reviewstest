this is read me
Listen 1004
<VirtualHost *:1004>
        ServerName purevpn_client
        ServerAdmin tariq.mehmood@gaditek.com

        DocumentRoot /var/www/php_projects/project_bi/bi_client
        <Directory /var/www/php_projects/project_bi/bi_client>
         Options Indexes FollowSymLinks MultiViews
         AllowOverride All
        </Directory>

</VirtualHost>
Listen 1000
<VirtualHost *:1000>
        ServerName purevpn_api
        ServerAdmin tariq.mehmood@gaditek.com

        DocumentRoot /var/www/php_projects/project_bi/bi_api/api/public
        <Directory /var/www/php_projects/project_bi/bi_api/api/public>
        Options Indexes FollowSymLinks MultiViews
        AllowOverride All
        </Directory>
</VirtualHost>


