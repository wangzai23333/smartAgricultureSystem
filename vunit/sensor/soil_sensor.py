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
                ADC.write_byte(0x04, 0x24)  # 往从机写一个字节
                data = ADC.read_word_data(0x04, 0x24)  # 树莓派读取扩展板返回回来的数据并打印出来
                voltage = data / 4095.0 * (5.5 - 3.3) + 3.3
                data = (voltage - 3.3) / (5.5 - 3.3) * 100
                if last_data == -1:
                    last_data = data    
                
                if last_data != data:
                    #大于5%变化第一个值不要
                    if data != 0 and last_data != -1:
                      dbc.update_sensor_data('S34', '土壤湿度传感器', 'humidity', data)
                      last_data = data
                
                time.sleep(5)
            except BaseException as e:
                print(e)
                util.log('[X] 土壤湿度传感器发生了错误！')
    except BaseException as e:
        print(e)
        util.log('[X] 土壤湿度传感器发生了错误！')


def start():
    MyThread(target=__start_runnable).start()


def cancel():
    global __running
    __running = False
