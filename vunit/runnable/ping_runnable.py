import time
import mqtt_client
import util
from scheduler.thread_manager import MyThread


class PingRunnable(MyThread):

    def __init__(self):
        super().__init__()
        self.__running = True

    def cancel(self):
        self.__running = False

    def run(self):
        i = 0
        while self.__running:
            time.sleep(1)
            i += 1
            if i < 100:
                continue
            i = 0
            data = {
                "head": {
                    "symbol": "UNIT",
                    "cmd": "ping",
                    "msgId": util.random_hex(16)
                }
            }
            mqtt_client.send(data)
