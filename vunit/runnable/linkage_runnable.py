import time

from scheduler.thread_manager import MyThread
from task import linkage_manager


class LinkageRunnable(MyThread):

    def __init__(self):
        super().__init__()
        self.__running = True

    def cancel(self):
        self.__running = False

    def run(self):
        while self.__running:
            time.sleep(1)
            try:
                for linkage in linkage_manager.get_linkage_list().values():
                    linkage.run()
            except BaseException as e:
                print(e)
