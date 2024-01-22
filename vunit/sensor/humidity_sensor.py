import time

import Adafruit_DHT

import database_core as dbc
import util
from scheduler.thread_manager import MyThread

__running = True


def __start_runnable():
    try:
        last_humidity = -100
        last_temperature = -100
        while __running:
            try:
                humidity, temperature = Adafruit_DHT.read_retry(Adafruit_DHT.DHT22, 21)
                if humidity is not None and humidity > 0 and humidity < 100 and temperature is not None and temperature > -30 and temperature < 50:
                    if last_humidity == -100:
                        last_humidity = humidity
                    if last_temperature == -100:
                        last_temperature = temperature
                        
                    if last_humidity != humidity:
                        dbc.update_sensor_data('MP21', '温湿度传感器', 'humidity', humidity)
                        last_humidity = humidity
                        time.sleep(0.05)
                    if abs(temperature - last_temperature) / temperature < 0.05 and last_temperature != temperature:
                        dbc.update_sensor_data('MP21', '温湿度传感器', 'temperature', temperature)
                        last_temperature = temperature
                    
                    
                else:
                    util.log('[X] 温湿读取失败！')
                time.sleep(5)
            except BaseException as e:
                print(e)
                util.log('[X] 温湿度传感器发生了错误！')
    except BaseException as e:
        print(e)
        util.log('[X] 温湿度传感器发生了错误！')


def start():
    MyThread(target=__start_runnable).start()


def cancel():
    global __running
    __running = False

