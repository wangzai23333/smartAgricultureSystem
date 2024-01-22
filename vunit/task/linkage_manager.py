import time
import database_core as dbc
import relay_controller
import util
from task.task_manager import Task
from task import task_manager
import uuid

# 联动类
class Linkage:
    def __init__(self, taskId: str, min_val: float, max_val: float, port: str, sensor_title: str, label: str, switch_num: int, onoff: int, keep_time: float, delay_time: float):
            self.taskId = taskId
            self.min_val = min_val
            self.max_val = max_val
            self.port = port
            self.sensor_title = sensor_title
            self.label = label
            self.switch_num = switch_num
            self.onoff = onoff
            self.keep_time = keep_time
            self.delay_time = delay_time
            self.__last_execute_time = 0
            self.__last_toggle = -1
            self.__not_reset = False
            self.__run_time = 0

    def run(self):
        data = dbc.find_datas('sensor_data')[0]
        sensor_data = data['sensor_data']
        toggle = 0
        for sensor in sensor_data:
            # 判断是否符合条件
            if sensor['port'] == self.port and sensor['sensor_title'] == self.sensor_title and sensor['label'] == self.label and self.min_val <= sensor['val'] <= self.max_val:
                toggle = 1
                # self.__last_execute_time = time.time()
        # 当触发时
        if  time.time() - self.__run_time >= self.delay_time * 60 + 10 and bool(toggle):
            self.__run_time = time.time()
            util.log('联动 {} 被触发！'.format(self.taskId))
            dbc.update_datas("task_data", {'number': self.switch_num,'isRun': 0}, {'$set': {'isDel': 1}})
            task_manager.add_task(Task('local_'+str(uuid.uuid4()), self.switch_num, int(self.onoff), time.time()*1000))
            # relay_controller.toggle_relay(relay_controller.get_relay_ioId(self.switch_num), bool(self.onoff))
            if self.keep_time > 0:
     
                task_manager.add_task(Task('local_'+str(uuid.uuid4()), self.switch_num, 0, (time.time()+self.keep_time * 60)*1000 ))
                # self.__not_reset = True

        # if self.keep_time > 0 and self.__not_reset and not bool(toggle) and time.time() - self.__last_execute_time >= self.keep_time * 60:
        #     self.__not_reset = False
        #     # 复位开关状态
        #     relay_controller.toggle_relay(relay_controller.get_relay_ioId(self.switch_num), not bool(self.onoff))
        #     util.log('联动 {} 开关已复位！'.format(self.taskId))

        # self.__last_toggle = toggle

    # 复位开关状态
    def reset(self):
        if self.__not_reset:
            self.__not_reset = False
            relay_controller.toggle_relay(relay_controller.get_relay_ioId(self.switch_num), not bool(self.onoff))
            util.log('联动 {} 开关已复位！'.format(self.taskId))

    @classmethod
    def deserialize(cls, data: dict):
        task_Id = str(data['taskId'])
        task_minVal = float(data['minVal'])
        task_maxVal = float(data['maxVal'])
        task_label = str(data['label'])
        task_port = str(data['port'])
        task_onoff = int(data['onoff'])
        task_switchNum = int(data['switchNum'])
        task_keepTime = float(data['keeptime'])
        task_sensorTitle = str(data['sensorTitle'])
        task_delayTime = float(data['delaytime'])
        return Linkage(task_Id, task_minVal, task_maxVal, task_port, task_sensorTitle, task_label, task_switchNum, task_onoff, task_keepTime, task_delayTime)

    def serialize(self):
        data = {
            'taskId': self.taskId,
            'minVal': self.min_val,
            'maxVal': self.max_val,
            'label': self.label,
            'port': self.port,
            'onoff': self.onoff,
            'switchNum': self.switch_num,
            'keeptime': self.keep_time,
            'delaytime': self.delay_time,
            'sensorTitle': self.sensor_title,
            
        }
        return data


__linkage_list = {}


def __update_linkage_data():
    linkage_data = []
    for linkage in __linkage_list.values():
        linkage_data.append(linkage.serialize())
    dbc.update_linkage_data(linkage_data)


def add_linkage(linkage: Linkage):
    if linkage.taskId not in __linkage_list.keys():
        __linkage_list[linkage.taskId] = linkage
    __update_linkage_data()


def remove_linkage(linkage: Linkage):
    remove_linkage_from_id(linkage.taskId)
    util.log('联动 {} 被cancel！'.format(linkage.taskId))


def remove_linkage_from_id(taskId: str):
    if taskId in __linkage_list.keys():
        __linkage_list[taskId].reset()
        del __linkage_list[taskId]
    __update_linkage_data()


def get_linkage_list(): return __linkage_list
