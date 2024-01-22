__RELAY_ID = {
    '1': 5,
    '2': 6,
    '3': 7,
    '4': 8,
    '5': 9,
    '6': 10,
    '7': 11,
    '8': 12
}

__relay_dict = {}


def get_numbers() -> dict:
    return __RELAY_ID


def get_relay_number(ioId: int):
    try:
        for number in __RELAY_ID:
            if __RELAY_ID[number] == ioId:
                return int(number)
    except:
        return 0


def get_relay_ioId(number: int):
    return __RELAY_ID[str(number)]


def get_relay_list() -> dict:
    return __relay_dict


def is_valid_id(number: int):
    return str(number) in __RELAY_ID


def toggle_relay(ioId, onOff: bool):
    __relay_dict[str(ioId)] = onOff
