import json
import os
import sys
import time
import warnings

import requests
from RPi import GPIO

import relay_controller
from scheduler import thread_manager
from scheduler.thread_manager import MyThread
from task import linkage_manager
import mqtt_client
import database_core as dbc
import util
from runnable.linkage_runnable import LinkageRunnable
from runnable.ping_runnable import PingRunnable
from runnable.relay_runnable import RelayRunnable
from runnable.sender_runnable import SenderRunnable
from runnable.task_runnable import TaskRunnable
from sensor import light_sensor
from sensor import soil_sensor
from sensor import coto_sensor
from sensor import humidity_sensor
from sensor import temperature_sensor
from sensor import mq135_sensor
from sensor import mq137_sensor
from sensor import humidity_sensor
from sensor import temperature_sensor


from task.linkage_manager import Linkage

__sensor_list = []
__runnable_list = []
__console_thread = None


# 启动计划任务
def __start_runnable():
    __runnable_list.append(PingRunnable())
    __runnable_list.append(TaskRunnable())
    __runnable_list.append(RelayRunnable())
    __runnable_list.append(SenderRunnable())
    __runnable_list.append(LinkageRunnable())
    for runnable in __runnable_list:
        runnable.start()


# 启动传感器任务
def __start_sensors():
    __sensor_list.append(light_sensor)
    __sensor_list.append(coto_sensor)
    __sensor_list.append(humidity_sensor)
    __sensor_list.append(mq135_sensor)
    __sensor_list.append(mq137_sensor)
    __sensor_list.append(soil_sensor)
    __sensor_list.append(temperature_sensor)
    for sensor in __sensor_list:
        sensor.start()


def __init_gpio():
    with warnings.catch_warnings():
        warnings.simplefilter("ignore")
        GPIO.cleanup()
        GPIO.setmode(GPIO.BCM)

        # 初始化继电器针脚
        for number in relay_controller.get_numbers().values():
            GPIO.setup(number, GPIO.OUT)
            GPIO.output(number, False)


def __login_iotm(username: str, password: bytes):
    data = {
        "username": username,
        "password": password
    }
    response = requests.post("https://iotm3.yafrm.com/api/sensor/login", data=data)
    body = json.loads(response.text)
    if 'token' in body:
        return body['token']
    return None


def __get_local_linkage(token: str, did: str):
    data = {
        "token": token,
        "did": did
    }
    response = requests.post("https://iotm3.yafrm.com/api/linkage/getLocalLinkage", data=data)
    return json.loads(response.text)['data']


# 注册联动
def __register_linkages():
    # 本地联动
    # linkage_manager.add_linkage(Linkage('linkage_in_1', 12, 100000000, 'S32', '有毒气体传感器', 'mq2', 2, 1, 1))  # 有毒气体->开启排气扇
    # linkage_manager.add_linkage(Linkage('linkage_in_2', 42, 100000000, 'MP12', '温湿度传感器', 'temperature', 2, 1, 1))  # 温度过高->开启排气扇
    # linkage_manager.add_linkage(Linkage('linkage_in_3', 1, 100000000, 'P6', '红外传感器', 'person', 1, 1, 1))  # 有人->开启继电器1

    # 获取联动列表
    try:
        #iotm配置
        token = __login_iotm("", "")
        linkages = __get_local_linkage(token, __did)
        if linkages is None:
            return
        # 清除本地联动
        dbc.update_linkage_data([])
        for linkage_data in linkages:
            linkage = Linkage.deserialize(linkage_data)
            task_Id = linkage.taskId
            task_switchNum = linkage.switch_num
            if relay_controller.is_valid_id(task_switchNum):
                linkage_manager.add_linkage(linkage)
            util.log('[返回] 本地联动控制命令,任务ID: {}'.format(task_Id))
    except Exception as e:
        util.log("[X] 无法在线获取本地联动！")
        linkages = dbc.find_datas('linkage_data')[0]['linkages']
        for linkage_data in linkages:
            linkage = Linkage.deserialize(linkage_data)
            task_Id = linkage.taskId
            task_switchNum = linkage.switch_num
            if relay_controller.is_valid_id(task_switchNum):
                linkage_manager.add_linkage(linkage)
            util.log('[读取] 本地联动控制命令,任务ID: {}'.format(task_Id))


def __send_online(online):
    coll = 'sensor_data'
    data = dbc.find_datas(coll)[0]
    send_data = {
        "head": {
            "symbol": "UNIT",
            "cmd": "onLineMsg",
            "msgId": util.random_hex(16)
        },
        "body": {
            "did": data['did'],
            "online": online,
            "updatetime": int(time.time() * 1000)
        }
    }
    mqtt_client.send(send_data)


def __console_listener():
    try:
        while True:
            text = input()
            args = text.split(' ')
            if len(args) == 0 or len(args[0]) == 0:
                continue
            if args[0] == 'help':
                util.log('did\t获取单元 did')
                util.log('stop\t关闭服务')
            elif args[0] == 'did':
                util.log('单元 did: {}'.format(dbc.find_datas('sensor_data')[0]['did']))
            elif args[0] == 'stop':
                stop()
                break
            else:
                util.log('未知命令！使用 \'help\' 获取帮助.')
    except Exception as e:
        print(e)


# 更新数据库
def __update_database():
    dbc.clear_sensor_data()
    dbc.delete_datas("task_data", {'isRun': 0})
    # 更新继电器状态
    numbers = relay_controller.get_numbers()
    for number in numbers.keys():
        dbc.update_onoff_data(int(number), GPIO.input(numbers[number]))


def __clear_log():
    directory = os.path.dirname(os.path.abspath(__file__)) + "/logs"
    if not os.path.exists(directory):
        os.mkdir(directory)
    for file_name in os.listdir(directory):
        if file_name.endswith('.log'):
            os.remove(directory + '/' + file_name)


# 关闭服务
def stop():
    util.log('正在关闭服务...')
    util.log('关闭传感器...')
    for sensor in __sensor_list:
        sensor.cancel()
    util.log('关闭计划任务...')
    for runnable in __runnable_list:
        runnable.cancel()
    time.sleep(5.1)
    __send_online(0)  # 发送离线消息
    util.log('关闭MQTT...')
    mqtt_client.close()
    util.log('关闭Mongo...')
    dbc.close()
    util.log('结束线程...')
    for thread in thread_manager.get_thread_list():
        if thread != __console_thread:
            thread.raise_exception()
            thread.join()
    util.log('服务已关闭！')
    sys.exit(0)


if __name__ == '__main__':
    __clear_log()
    util.log('\t')
    util.log('\tVirtual Unit v1.0.0')
    util.log('\t')
    util.log('启动中...')
    util.log('初始化GPIO接口...')
    __init_gpio()
    util.log('连接到Mongo...')
    dbc.connect('mongodb://localhost:27017/', 'raspberrypi')
    util.log('连接到MQTT...')
    mqtt_client.connect()
    util.log('更新数据库...')
    __update_database()
    util.log('启动传感器任务...')
    __start_sensors()
    util.log('启动计划任务...')
    __start_runnable()
    util.log('注册本地联动...')
    __did = dbc.find_datas('sensor_data')[0]['did']
    __register_linkages()
    util.log('注册命令...')
    __console_thread = MyThread(target=__console_listener)  # 监听控制台
    __console_thread.start()
    util.log('完成!')
    __send_online(1)  # 发送上线消息
    util.log('单元 did: {}'.format(__did))
    util.log('使用 \'help\' 获取帮助.')
