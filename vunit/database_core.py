import uuid
import time
import pymongo
from pymongo import MongoClient
from pymongo.database import Database

import util

__mongoClient: MongoClient = None
__mongodb: Database = None


def connect(url, db_name):
    try:
        global __mongoClient
        global __mongodb
        __mongoClient = pymongo.MongoClient(url)
        __mongodb = __mongoClient[db_name]
        # delete_coll('task_data')
        # delete_coll('sensor_data')
        # delete_data('sensor_data', {})
        __init_colls()
        # for name in mongoClient.list_database_names():
        #     print('DB: ' + name)
        util.log('Mongo连接成功！')
    except BaseException as e:
        print(e)
        util.log('[X] Mongo连接失败！')


def close():
    __mongoClient.close()


def __data_len(data):
    length = 0
    for d in data:
        length += 1
    return length


def __init_colls():
    sensor_coll = 'sensor_data'
    if not is_coll_exists(sensor_coll) or __data_len(find_datas(sensor_coll)) < 1:
        sensor_data = {
            'did': str(uuid.uuid1()),
            'sensor_data': [],
            'onoff': [],
            'isSend': 'false',
            'updatetime': int(time.time() * 1000)
        }
        insert_data(sensor_coll, sensor_data)
    linkage_coll = 'linkage_data'
    if not is_coll_exists(linkage_coll) or __data_len(find_datas(linkage_coll)) < 1:
        insert_data(linkage_coll, {"linkages": []})


def get_colls():
    return __mongodb.list_collection_names()


def delete_coll(coll):
    return __mongodb.drop_collection(coll)


def insert_data(coll, value):
    __mongodb[coll].insert_one(value)


def delete_data(coll, query):
    return __mongodb[coll].delete_one(query)


def delete_datas(coll, query):
    return __mongodb[coll].delete_many(query)


def save_data(coll, value):
    return __mongodb[coll].save(value)


def update_data(coll, query, new_value):
    return __mongodb[coll].update_one(query, new_value)


def update_datas(coll, query, new_value):
    return __mongodb[coll].update_many(query, new_value)


def find_data(coll, query):
    return __mongodb[coll].find(query)


def find_datas(coll):
    return __mongodb[coll].find()


def get_did():
    return find_datas('sensor_data')[0]['did']


def is_coll_exists(coll) -> bool:
    return coll in get_colls()


def is_data_exists(coll, value) -> bool:
    return value in find_datas(coll)


def update_task_data(task_id, is_run, run_time):
    coll = 'task_data'
    update_data(coll, {'taskId': task_id}, {'$set': {'isRun': is_run}})  # 更新 已运行
    update_data(coll, {'taskId': task_id}, {'$set': {'runtime': run_time}})  # 更新 已运行


def update_linkage_data(linkage_list: list):
    coll = 'linkage_data'
    data = find_datas(coll)[0]
    coll_linkages = data['linkages']
    update_data(coll, {'linkages': eval(str(coll_linkages))}, {'$set': {'linkages': linkage_list}})  # 更新联动数据


def update_onoff_data(number: int, state: int):
    coll = 'sensor_data'
    data = find_datas(coll)[0]
    coll_onoff = data['onoff']
    onoff_data = {
        'number': number,
        'state': state
    }
    is_exists = False
    new_coll_onoff = []
    new_coll_onoff.extend(coll_onoff)
    for i in range(len(new_coll_onoff)):
        cdata = new_coll_onoff[i]
        if str(cdata['number']) == str(number):
            new_coll_onoff[i] = onoff_data
            is_exists = True
            break
    if is_exists is False:
        new_coll_onoff.append(onoff_data)
    update_data(coll, {'updatetime': data['updatetime']}, {'$set': {'updatetime': int(time.time() * 1000)}})  # 更新时间
    update_data(coll, {'onoff': eval(str(coll_onoff))}, {'$set': {'onoff': new_coll_onoff}})


def clear_sensor_data():
    coll = 'sensor_data'
    data = find_datas(coll)[0]
    coll_sensor_data = data['sensor_data']
    coll_onoff_data = data['onoff']
    update_data(coll, {'updatetime': data['updatetime']}, {'$set': {'updatetime': int(time.time() * 1000)}})  # 更新时间
    update_data(coll, {'sensor_data': eval(str(coll_sensor_data))}, {'$set': {'sensor_data': []}})  # 更新传感器数据
    update_data(coll, {'onoff': eval(str(coll_onoff_data))}, {'$set': {'onoff': []}})  # 更新传感器数据


def update_sensor_data(port, sensor_title, label, val, content='', content_des='', istext=0):
    coll = 'sensor_data'
    data = find_datas(coll)[0]
    coll_sensor_data = data['sensor_data']
    sensor_data = {
        'port': port,
        'sensor_title': sensor_title,
        'label': label,
        'content': content,
        'content_des': content_des,
        'istext': istext,
        'val': val
    }
    is_exists = False
    new_coll_sensor_data = []
    new_coll_sensor_data.extend(coll_sensor_data)
    for i in range(len(new_coll_sensor_data)):
        cdata = new_coll_sensor_data[i]
        if str(cdata['label']) == label:
            new_coll_sensor_data[i] = sensor_data
            is_exists = True
            break
    if is_exists is False:
        new_coll_sensor_data.append(sensor_data)
    update_data(coll, {'updatetime': data['updatetime']}, {'$set': {'updatetime': int(time.time() * 1000)}})  # 更新时间
    update_data(coll, {'sensor_data': eval(str(coll_sensor_data))}, {'$set': {'sensor_data': new_coll_sensor_data}})  # 更新传感器数据
