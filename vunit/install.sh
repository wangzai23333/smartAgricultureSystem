
install_path=$(pwd)
service_path="/etc/systemd/system/vunit.service"

echo 正在安装...

echo > $service_path
echo "[Unit]" >> $service_path
echo "Description=vunit Service" >> $service_path
echo "After=network.target" >> $service_path
echo "" >> $service_path
echo "[Service]" >> $service_path
echo "Restart=always" >> $service_path
echo "RestartSec=100" >> $service_path
echo "ExecStart=/usr/bin/python3 -u ${install_path}/main.py" >> $service_path
echo "User=pi" >> $service_path
echo "" >> $service_path
echo "[Install]" >> $service_path
echo "WantedBy=multi-user.target" >> $service_path
echo "" >> $service_path

systemctl daemon-reload

echo 安装完成!
