./configure --prefix=/opt/ECloud-Admin-VNC/nginx --with-http_secure_link_module --add-module=/opt/ECloud-Admin-VNC/websockify-nginx-module-master

#修改xen server vnc监听地址，从默认的127.0.0.1变成监听0.0.0.0
vim /opt/xensource/libexec/vncterm-wrapper
