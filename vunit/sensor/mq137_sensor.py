import time
import database_core as dbc
import util
from scheduler.thread_manager import MyThread
import smbus2 as smbus

__running = True


def __start_runnable():
    try:
        ADC = smbus.SMBus(1)  # 声明使用I2C 1
        last_data = -1
        while __running:
            try:
                ADC.write_byte(0x04, 0x35)  # 往从机写一个字节
                data = ADC.read_word_data(0x04, 0x35)  # 树莓派读取扩展板返回回来的数据并打印出来
                if last_data != data:
                    dbc.update_sensor_data('S37', '氨气传感器', 'nh3', data)
                last_data = data
                time.sleep(1)
            except BaseException as e:
                print(e)
                util.log('[X] 氨气传感器发生了错误！')
    except BaseException as e:
        print(e)
        util.log('[X] 氨气传感器发生了错误！')


def start():
    MyThread(target=__start_runnable).start()


def cancel():
    global __running
    __running = False
