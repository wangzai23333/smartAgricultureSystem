service_path="/etc/systemd/system/virtualunit.service"

echo 正在卸载...

systemctl stop virtualunit
rm -f $service_path
systemctl daemon-reload

echo 卸载完成!
