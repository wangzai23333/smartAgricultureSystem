from datetime import date
import time
from GreenPonik_BH1750.BH1750 import BH1750
import database_core as dbc
import util
from scheduler.thread_manager import MyThread

__running = True


def __start_runnable():
    try:
        bh = BH1750()
        last_data = -1
        while __running:
            try:
                data = bh.read_bh1750()
                if last_data == -1:
                    last_data = data
                if last_data != data:
                    dbc.update_sensor_data('bh1', '光照传感器', 'light', data)
                    last_data = data
                time.sleep(5)
            except BaseException as e:
                print(e)
                util.log('[X] 光照传感器发生了错误！')
    except BaseException as e:
        print(e)
        util.log('[X] 光照传感器发生了错误！')


def start():
    MyThread(target=__start_runnable).start()


def cancel():
    global __running
    __running = False
