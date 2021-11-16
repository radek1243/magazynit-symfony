<?php

namespace App\Communicate;

class CommunicateBuilder{

    public static $SEND_LOC_ERROR = 'send_loc_error';
    public static $SEND_SUCCESS = 'send_success';
    public static $SERVICE_LOC_ERROR = 'service_loc_error';
    public static $SERVICE_STATE_ERROR = 'service_state_error';
    public static $SERVICE_SUCCESS = 'service_success';
    public static $UTILIZATION_LOC_ERROR = 'utilization_loc_error';
    public static $UTILIZATION_STATE_ERROR = 'utilization_state_error';
    public static $UTILIZATION_SUCCESS = 'utilization_success';
    public static $CHANGE_DESC_SUCCESS = 'change_desc_success';
    public static $CHANGE_STATE_SUCCESS = 'change_state_success';
    public static $GENERAL_ERROR = 'general_error';
    public static $INVOICE_SUCCESS = 'invoice_success';
    public static $RETURN_SERVICE_SUCCESS = 'return_service_success';

    public static function build($communicate_code): Communicate {
        switch($communicate_code){
            case self::$SEND_LOC_ERROR:
                return new Communicate(null, "Lokalizacja źródłowa i docelowa są takie same. Wysyłka niemożliwa.");
            case self::$SEND_SUCCESS:
                return new Communicate("Wysłano urządzenia", null);
            case self::$SERVICE_LOC_ERROR:
                return new Communicate(null, "Urządzenia można wysyłać na serwis tylko z Magazynu IT!");
            case self::$SERVICE_STATE_ERROR:
                return new Communicate(null, "Próbujesz wysłać też sprawne urządzenia na serwis. Operacja niemożliwa.");
            case self::$SERVICE_SUCCESS:
                return new Communicate("Wysłano urządzenia na serwis", null);
            case self::$UTILIZATION_LOC_ERROR:
                return new Communicate(null, "Urządzenia można utylizować tylko z Magazynu IT");
            case self::$UTILIZATION_STATE_ERROR:
                return new Communicate(null, "Próbujesz zutylizować sprawne urządzenia. Operacja niemożliwa.");
            case self::$UTILIZATION_SUCCESS:
                return new Communicate("Zutylizowano urządzenia", null);
            case self::$CHANGE_DESC_SUCCESS:
                return new Communicate("Opis zmieniony", null);
            case self::$CHANGE_STATE_SUCCESS:
                return new Communicate("Zmieniono stan urządzeń", null);
            case self::$GENERAL_ERROR:
                return new Communicate("Błąd ogólny aplikacji", null);
            case self::$INVOICE_SUCCESS:
                return new Communicate("Zafakturowano urządzenia", null);
            case self::$RETURN_SERVICE_SUCCESS:
                return new Communicate("Przywrócono urządzenia z serwisu", null);
            default:
                return new Communicate();
        }
    }
}