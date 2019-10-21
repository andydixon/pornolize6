/usr/bin/consul agent -config-dir /etc/consul.d/ client &
/usr/bin/supervisord -n -c /etc/supervisor/supervisord.conf
