import time
import database_core as dbc
import mqtt_client
import util
from scheduler.thread_manager import MyThread


class SenderRunnable(MyThread):

    def __init__(self):
        super().__init__()
        self.__running = True

    def cancel(self):
        self.__running = False

    def run(self):
        coll = 'sensor_data'
        last_data = '{}'
        while self.__running:
            time.sleep(1)
            data = dbc.find_datas(coll)[0]
            if str(data) != last_data:
                send_data = {
                    "head": {
                        "symbol": "UNIT",
                        "cmd": "sendMsg",
                        "msqld": util.random_hex(16)
                    },
                    "body": {
                        "did": data['did'],
                        "sensorData": data['sensor_data'],
                        "onoffData": data['onoff'],
                        "updateTime": data['updatetime']
                    }
                }
                mqtt_client.send(send_data)
            last_data = str(data)
