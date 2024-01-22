import threading
import json
import time

import paho.mqtt.client as mqtt

import relay_controller
from scheduler.thread_manager import MyThread
from task import task_manager, linkage_manager
import util
from task.linkage_manager import Linkage
from task.task_manager import Task
import database_core as dbc

__m_client = mqtt.Client()

__did = '00000000'

__running = False

__last_message_time = 0

__HOST = ''#emqx配置

__PORT = 1883


# 连接成功回调
def __on_connect(client, userdata, flags, rc):
    global __last_message_time
    __m_client.subscribe('UNIT/toReceive/' + __did)
    util.log('MQTT已连接，状态码: {}'.format(rc))
    __last_message_time = time.time()


# 消息接收回调
def __on_message(client, userdata, msg):
    global __last_message_time
    __last_message_time = time.time()
    # print(msg.topic + " " + msg.payload.decode('UTF-8'))
    try:
        try:
            data = json.loads(msg.payload.decode('UTF-8'))
        except:
            return
        head = data['head']
        if head['symbol'] != 'IOTM':
            return
        cmd = head['cmd']
        msgId = head['msgId']
        # 控制命令
        if cmd == 'toControl':
            body = data['body']
            task_Id = str(body['taskId'])
            task_number = int(body['number'])
            task_onoff = int(body['onoff'])
            task_runTime = int(body['runTime'])
            if relay_controller.is_valid_id(task_number):
                task_manager.add_task(Task(task_Id, task_number, task_onoff, task_runTime))
            util.log('[接收] 控制命令,任务ID: {}'.format(task_Id))

        # 取消命令
        elif cmd == 'toCancel':
            body = data['body']
            try:
                util.log('[接收] 取消任务: {}'.format(body['taskId']))
                for taskId in body['taskId']:
                    task_manager.remove_task_from_id(taskId)
            except:
                pass
            try:
                util.log('[接收] 取消联动: {}'.format(body['linkageTaskId']))
                for taskId in body['linkageTaskId']:
                    linkage_manager.remove_linkage_from_id(taskId)
            except:
                pass

        # 心跳
        elif cmd == 'pung':
            util.log('[接收] 心跳')

        # 消息回复
        elif cmd == 'sendMsgRes':
            body = data['body']
            util.log('[接收] 消息回复 -> {}'.format(body['msg']))

        # 上下线回复
        elif cmd == 'onOffRes':
            body = data['body']
            util.log('[接收] 上下线回复 -> {}'.format(body['msg']))

        # 本地联动控制
        elif cmd == 'toLinkage':
            body = data['body']
            linkage = Linkage.deserialize(body)
            task_switchNum = linkage.switch_num
            task_Id = linkage.taskId
            if relay_controller.is_valid_id(task_switchNum):
                linkage_manager.add_linkage(linkage)
            util.log('[接收] 本地联动控制命令,任务ID: {}'.format(task_Id))

    except BaseException as e:
        util.log('[X] 命令处理错误！')
        util.log('[X] 异常描述 -> ' + str(e))
        try:
            util.log('[X] 异常命令 -> ' + msg.payload.decode('UTF-8'))
        except BaseException as e:
            pass


def __connect():
    global __did
    try:
        __m_client.disconnect()
    except Exception as e:
        print(e)
    __did = str(dbc.find_datas('sensor_data')[0]['did'])
    __m_client.on_connect = __on_connect
    __m_client.on_message = __on_message
    __m_client.connect(__HOST, __PORT)
    try:
        MyThread(target=__m_client.loop_forever).start()
    except Exception as e:
        print(e)


def __reconnect_runnable():
    while __running:
        time.sleep(1)
        if __running and (time.time() - __last_message_time) > 30:
            util.log("[!] 尝试重连 MQTT...")
            try:
                __connect()
                util.log('MQTT连接成功！')
            except BaseException as e:
                print(e)
                util.log('[X] MQTT连接失败！')


def connect():
    global __did
    global __running
    __running = True
    MyThread(target=__reconnect_runnable).start()
    try:
        __connect()
        util.log('MQTT连接成功！')
    except BaseException as e:
        print(e)
        util.log('[X] MQTT连接失败！')


def close():
    global __running
    __running = False
    try:
        __m_client.disconnect()
    except Exception as e:
        print(e)


# 发送消息
def send(data):
    # if data['head']['cmd'] != 'sendMsg':
    util.log('[发送] 命令: {}'.format(data['head']['cmd']))
    __m_client.publish('UNIT/toSend/' + __did, payload=str(json.dumps(data)), qos=0)
