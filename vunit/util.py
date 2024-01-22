import codecs
import ctypes
import inspect
import os
import random
import time

__current_directory = os.path.dirname(os.path.abspath(__file__))


def __write_to_file(text):
    direct = __current_directory + "/logs"
    if not os.path.exists(direct):
        os.mkdir(direct)
    file = codecs.open(direct + "/log.log", 'a', 'utf-8')
    file.write(text + "\n")
    file.close()


def random_hex(length):
    result = hex(random.randint(0, 16 ** length)).replace('0x', '').upper()
    if len(result) < length:
        result = '0' * (length - len(result)) + result
    return result


def log(text):
    msg = '[{}] {}'.format(time.strftime('%H:%M:%S'), text)
    print(msg)
    __write_to_file(msg)


def __async_raise(tid, exctype):
    """raises the exception, performs cleanup if needed"""
    tid = ctypes.c_long(tid)
    if not inspect.isclass(exctype):
        exctype = type(exctype)
    res = ctypes.pythonapi.PyThreadState_SetAsyncExc(tid, ctypes.py_object(exctype))
    if res == 0:
        raise ValueError("invalid thread id")
    elif res != 1:
        ctypes.pythonapi.PyThreadState_SetAsyncExc(tid, None)
        raise SystemError("PyThreadState_SetAsyncExc failed")


def stop_thread(thread):
    __async_raise(thread.ident, SystemExit)
