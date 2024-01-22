from RPi import GPIO
import time

import relay_controller
import util
import database_core as dbc
from scheduler.thread_manager import MyThread


class RelayRunnable(MyThread):

    def __init__(self):
        super().__init__()
        self.__running = True

    def cancel(self):
        self.__running = False

    def run(self):
        while self.__running:
            time.sleep(1)
            for ioIdStr in relay_controller.get_relay_list().keys():
                ioId = int(ioIdStr)
                try:
                    toggle = relay_controller.get_relay_list()[ioIdStr]
                    GPIO.output(ioId, toggle)
                    dbc.update_onoff_data(relay_controller.get_relay_number(ioId), GPIO.input(ioId))
                except BaseException as e:
                    print(e)
                    util.log('[!] 无法控制开关针脚 {}'.format(ioId))
