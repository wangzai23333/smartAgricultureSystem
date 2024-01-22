# 智慧农业系统
体验申请请关注公众号 fay数字人

# Virtual Unit v1.0

## 1. 安装

##### 1.1. Python 3

##### 1.2. MongoDB Server 4


```sh
# 安装依赖
pip3 install -r requirements.txt

# 安装 Virtual Unit 服务
sudo sh ./install.sh

# 配置iotm
vunit\main.py

```




## 2. 运行



### 2.1 通过 Python 运行 (开发模式)

```shell
python3 main.py
```

### 2.2 通过 systemctl 运行，并创建开机启动

```sh
# 启动服务
sudo systemctl start virtualunit

# 创建开机启动
sudo systemctl enable virtualunit
```



## 3. 命令 （仅开发模式）

| 命令 | 描述                            |
| ---- | ------------------------------- |
| help | 获取帮助                        |
| did  | 获取单元 did (首次运行自动生成) |
| stop | 关闭服务                        |



## 4. 说明

### 4.1. 目录结构

```
.
├── main.py							主程序入口
├── database_core.py				MongoDB 客户端
├── mqtt_client.py					MQTT 客户端
├── relay_controller.py				继电器控制器
├── runnable						计划任务
│   ├── linkage_runnable.py			执行本地联动
│   ├── ping_runnable.py			发送心跳包
│   ├── relay_runnable.py			继电器状态保持
│   ├── sender_runnable.py			报告传感器数据
│   └── task_runnable.py			执行控制任务
├── scheduler
│   └── thread_manager.py			线程调度管理器
├── sensor							传感器
│   ├── my_sensor.py				传感器数据读取者
├── task							任务
│   ├── linkage_manager.py			本地联动管理器
│   └── task_manager.py				控制任务管理器
└── util.py							工具模块
```



### 4.2. 添加继电器

#### 编辑 <u>./relay_controller.py</u>

字典 `__RELAY_ID`  中添加

```
'自定义编号': GPIO针脚Id
```

如：

```python
__RELAY_ID = {
    '1': 6,
}
```

*注：继电器编号，为字符串类型整数



### 4.3 添加传感器

#### 4.3.1. 创建Py文件 <u>./sensor/<传感器名称>_sensor.py</u>

```python
import time
import database_core as dbc
import util
from scheduler.thread_manager import MyThread

__running = True

def __start_runnable():
    while __running:
        try:
            # TODO 读取传感器数据
            # 设 data 为传感器数据
            data = 1.0
            # 写入数据库
            dbc.update_sensor_data('传感器端口', '传感器标题', '数据标签', data)
        except BaseException as e:
            print(e)
            util.log('[X] 传感器发生了错误！')

def start():
    MyThread(target=__start_runnable).start()

def cancel():
    global __running
    __running = False

```



#### 4.3.2. 编辑 <u>./main.py</u>

4.3.2.1. 方法 **__start_sensors** 中添加:

```python
__sensor_list.append(<传感器名称>_sensor)
```

如:

```python
from sensor import my_sensor

def __start_sensors():
    __sensor_list.append(my_sensor) # 添加传感器

```



4.3.2.2. 初始化传感器GPIO针脚  (非必须，仅GPIO接口传感器)

方法 **__init_gpio** 中添加:

```python
GPIO.setup(GPIO端口Id, GPIO.IN)
```

如:

```python
def __init_gpio():
    with warnings.catch_warnings():
        warnings.simplefilter("ignore")
        GPIO.cleanup()
        GPIO.setmode(GPIO.BCM)

        GPIO.setup(6, GPIO.IN)  # 初始化GPIO针脚

```



### 4.4. 添加本地联动

#### 编辑 <u>./main.py</u>

方法 **__register_linkages** 中添加:

```python
linkage_manager.add_linkage(Linkage('联动Id', 最小触发值, 最大触发值, '传感器端口', '传感器标题', '数据标签', 继电器编号, 开关状态, 保持时间))
```

如:

```python
def __register_linkages():
	# 当环境度在区间 [30, 100] 时，打开编号为'3'的继电器，持续5分钟
    linkage_manager.add_linkage(Linkage('linkage_in_1', 30, 100, 'Port1', '温度传感器', 'temperature', 3, 1, 5))
```





# iotm3.0

## 1. 安装

##### 1.1. php7.2以上
##### 1.2. emqx 

```
官网：https://www.emqx.com/zh
配置：
vunit内mqtt_client.py
iotm3内/application/api/controller/Emqx.php
iotm3内/application/common/controller/Emqx.php
iotm3内/Gateway/Applications/other/start_mqtt.php

```
##### 1.2. tdengine 2.4.0.18

```
官网：https://www.taosdata.com/
配置：
/application/common/controller/Tdengine.php


若需要新增传感器需要添加超级表
超级表格式model_标签名（如：cl2）
             Field              |         Type         |   Length    |   Note   |
=================================================================================
 ts                             | TIMESTAMP            |           8 |          |
 val                            | FLOAT                |           4 |          |
 istext                         | BOOL                 |           1 |          |
 content                        | BINARY               |          40 |          |
 content_des                    | NCHAR                |          50 |          |
 did                            | BINARY               |         100 | TAG      |
 sensorid                       | INT                  |           4 | TAG      |
 port                           | BINARY               |         100 | TAG      |

 ```

 ##### 1.3. 短信宝配置
 ```
 配置：
addons\smsbao\config.php
 ```

  ##### 1.4. 阿里云语音短信配置
   ```
 配置：
application\common\controller\Gizwits.php
 ```
##### 1.5. 安装redis

##### 1.6. 安装mysql5.7以上

##### 1.7. 配置iotm3
   ```
基础框架：https://doc.fastadmin.net/doc

接入需配置伪静态
Nginx

location / {
	if (!-e $request_filename){
		rewrite  ^(.*)$  /index.php?s=$1  last;   break;
	}
}


 ```

##### 1.8. 打开GatewayWorker
   ```
cd Gateway
调试：
php start.php start
正式运行：
php start.php start -d

需要确保配置好redis和emqx,域名对应为您设置的域名
 ```
