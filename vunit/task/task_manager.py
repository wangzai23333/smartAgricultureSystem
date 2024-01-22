import time
import database_core as dbc
import relay_controller


class Task:
    def __init__(self, taskId: str, number: int, onoff: int, runTime: int):
        self.taskId = taskId
        self.number = number
        self.onoff = onoff
        self.runTime = runTime
        self.__executed = False

    def run(self):
        if not self.__executed and time.time() * 1000 >= self.runTime:
            self.__executed = True
            relay_controller.toggle_relay(relay_controller.get_relay_ioId(self.number), bool(self.onoff))
            return True
        else:
            return False

    # 返回是否已被执行
    def is_executed(self) -> bool:
        return self.__executed


__task_list = {}

def run_task(task):
    if  task['isRun'] == 0 and time.time() * 1000 >= task['runTime']:
        relay_controller.toggle_relay(relay_controller.get_relay_ioId(task['number']), bool(task['onoff']))
        return True
    else:
        return False



def add_task(task: Task):
    info = dbc.find_data("task_data", {'taskId': task.taskId})
    if not info or len(list(info)) == 0:
        task_data = {
            'taskId': task.taskId,
            'number': task.number,
            'onoff': task.onoff,
            'runTime': task.runTime,
            'isRun': 0,
            'isDel': 0,
            'executedTime': 0,
        }
        dbc.insert_data("task_data", task_data)


def remove_task(task: Task):
    remove_task_from_id(task.taskId)


def remove_task_from_id(taskId: str):
    dbc.update_data("task_data", {'taskId': taskId}, {'$set': {'isDel': 1}})  # 更新已删除任务



def update_task_data(task_id):
    coll = 'task_data'
    dbc.update_data(coll, {'taskId': task_id}, {'$set': {'isRun': 1}})  # 更新 已运行
    dbc.update_data(coll, {'taskId': task_id}, {'$set': {'executedTime': int(time.time() * 1000)}})  # 更新 已运行

def get_task_list(): return __task_list
