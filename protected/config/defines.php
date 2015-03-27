<?

// команды
define('CMD_CONTROLLER', 0x01);
define('CMD_PIN', 0x02);
define('CMD_1WIRE', 0x03);
define('CMD_REGIN', 0x04);
define('CMD_REGOUT', 0x05);
define('CMD_NFC', 0x06);
define('CMD_RADIO', 0x07);
define('CMD_DHT', 0x08);
define('CMD_SHIFT', 0x09);

define('CMD_LISTEN', 0x80);
define('CMD_FREERAM', 0x81);

// типы команд
define('CMD_TYPE_PIN_MODE_INPUT', 0x01);
define('CMD_TYPE_PIN_MODE_OUTPUT', 0x02);
define('CMD_TYPE_PIN_READ', 0x03);
define('CMD_TYPE_PIN_WRITE', 0x04);
define('CMD_TYPE_PIN_VALUE', 0x05);

define('CMD_TYPE_1WIRE_COUNT', 0x01);
define('CMD_TYPE_1WIRE_LIST', 0x02);
define('CMD_TYPE_1WIRE_READ', 0x03);
define('CMD_TYPE_1WIRE_WRITE', 0x04);
define('CMD_TYPE_1WIRE_VALUE', 0x05);

define('CMD_TYPE_NFC_INIT', 0x01);
define('CMD_TYPE_NFC_FIRMWARE', 0x02);
define('CMD_TYPE_NFC_ERROR', 0x03);
define('CMD_TYPE_NFC_RECIEVE', 0x04);
define('CMD_TYPE_NFC_WRITE', 0x05);

define('CMD_ERROR_NFC_NONE', 0x00);
define('CMD_ERROR_NFC_DEVICENOTFOUND', 0x01);
define('CMD_ERROR_NFC_CARDNOTVALID', 0x02);
define('CMD_ERROR_NFC_WRONGDATA', 0x03);
define('CMD_ERROR_NFC_NOTAUTH', 0x04);
define('CMD_ERROR_NFC_NOTWRITE', 0x05);

define('CMD_TYPE_RADIO_INIT', 0x01);
define('CMD_TYPE_RADIO_WPIPE', 0x02);
define('CMD_TYPE_RADIO_RPIPE', 0x03);
define('CMD_TYPE_RADIO_CHKEYS', 0x04);
define('CMD_TYPE_RADIO_SEND', 0x05);
define('CMD_TYPE_RADIO_RESPONCE', 0x06);
define('CMD_TYPE_RADIO_K128', 0x07);

define('CMD_TYPE_DHT_READ', 0x01);
define('CMD_TYPE_DHT_VALUE', 0x02);
define('CMD_TYPE_DHT_ERROR', 0x03);

define('CMD_TYPE_SHIFT_READ', 0x01);
define('CMD_TYPE_SHIFT_WRITE', 0x02);
define('CMD_TYPE_SHIFT_VALUE', 0x03);


define('CMD_TYPE_READ', 0x01);
define('CMD_TYPE_WRITE', 0x02);
define('CMD_TYPE_COUNT', 0x03);
define('CMD_TYPE_LIST', 0x04);


// устройства
$defineDevices = array(
    // исполнительные устройства
    'DEV_LED' => 1,
    'DEV_BUTTON' => 2,
    'DEV_MOVESENSOR' => 3,
    'DEV_RELE' => 4,
    'DEV_TEMPERATURESENSOR' => 5,           // DS18B20
    'DEV_MAGNETOSENSOR' => 6,
    'DEV_VIBROSENSOR' => 7,
    'DEV_LASER' => 8,
    'DEV_NFC' => 9,
    'DEV_PHOTORESISTOR' => 10,              // цифровой
    'DEV_DHT' => 11,
    
    // контроллеры и промежуточные устройства
    'DEV_ARDUINO' => 100,                   // Arduino
    'DEV_REGISTR_IN' => 101,                // сдвиговый регистр IN
    'DEV_REGISTR_OUT' => 102,               // сдвиговый регистр OUT
    'DEV_1WIRE2' => 103,                    // устройство 1-wire DS2413
    'DEV_1WIRE8' => 104,                    // устройство 1-wire DS2408
    
    
);

// типы подключения
$defineConnect = array(
    'CONNECT_OTHER' => -1,
    'CONNECT_PIN' => 1,
    'CONNECT_ONEWIRE' => 2,
    'CONNECT_RADIO' => 3,
    'CONNECT_USB' => 4,
    'CONNECT_TXRX' => 5,        // не реализовано
    'CONNECT_ETHERNET' => 6,    // не реализовано
    'CONNECT_I2C' => 7,
    'CONNECT_WIFI' => 8,
);

foreach($defineDevices+$defineConnect as $def=>$val){
    define($def, $val);
}


$defParams = array(
    'defineDevices' => $defineDevices,
    'defineConnect' => $defineConnect,

    'inputDevices' => array(DEV_BUTTON, DEV_MOVESENSOR, DEV_TEMPERATURESENSOR, DEV_MAGNETOSENSOR, DEV_VIBROSENSOR, DEV_PHOTORESISTOR, DEV_DHT),
    'outputDevices' => array(DEV_LED, DEV_RELE, DEV_LASER),
    'direct1WireDevices' => array(DEV_TEMPERATURESENSOR),
    '1WireDevices' => array(DEV_TEMPERATURESENSOR, DEV_1WIRE2, DEV_1WIRE8),
    'listenDevices' => array(DEV_BUTTON, DEV_MOVESENSOR, DEV_MAGNETOSENSOR, DEV_VIBROSENSOR, DEV_PHOTORESISTOR, DEV_REGISTR_IN, DEV_1WIRE2, DEV_1WIRE8),
    'i2cDevices' => array(DEV_NFC),
    'booleanDevices' => array(DEV_LED, DEV_BUTTON, DEV_MOVESENSOR, DEV_RELE, DEV_MAGNETOSENSOR, DEV_VIBROSENSOR, DEV_LASER, DEV_PHOTORESISTOR),
    'parentDevices' => array(DEV_ARDUINO, DEV_REGISTR_IN, DEV_REGISTR_OUT, DEV_1WIRE2, DEV_1WIRE8),
);




