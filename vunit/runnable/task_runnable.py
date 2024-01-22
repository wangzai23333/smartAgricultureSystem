import time

import relay_controller
from scheduler.thread_manager import MyThread
from task import task_manager
import util
import database_core as dbc

class TaskRunnable(MyThread):

    def __init__(self):
        super().__init__()
        self.__running = True

    def cancel(self):
        self.__running = False

    def run(self):
        while self.__running:
            time.sleep(1)
            try:
                list = dbc.find_data("task_data", {'isDel': 0, 'isRun': 0})
                for task in list:
                    if task_manager.run_task(task):
                        dbc.delete_datas("task_data", {'isDel': 1,'number': task['number']})
                        util.log('任务 {} 被触发！'.format(task['taskId']))
                        task_manager.update_task_data(task['taskId'])
                
            except BaseException as e:
                print(e)
