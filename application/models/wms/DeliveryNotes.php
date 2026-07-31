<?php 
class DeliveryNotes extends ApiModel{
    public static $DICT = [];
    public static $DICTHP3 = [];
    public static $url  = API_SERVER . "DeliveryOrders";
    public static function getDN($DeliveryNumber){
        if (isset(DeliveryNotes::$DICT[$DeliveryNumber]))
            return DeliveryNotes::$DICT[$DeliveryNumber];
        
        $params         = [
                'DeliveryNumbers'    => $DeliveryNumber,
                "Take"              => 1,
                "Skip"              => 0,
        ];

		$DN 			= self::get($params, self::$url);
        if (isset($DN[0]))
            DeliveryNotes::$DICT[$DeliveryNumber] = $DN[0];
        else 
            DeliveryNotes::$DICT[$DeliveryNumber] = [];
		return $DN;
    }
    public static function getFromHp3Id($id){
        if (isset(DeliveryNotes::$DICTHP3[$id]))
            return DeliveryNotes::$DICTHP3[$id];
        
        $params         = [
                'CustomerPoNumbers'    => $id,
                "Take"              => 1,
                "Skip"              => 0,
        ];

		$DN 			= self::get($params, self::$url);
        if (isset($DN[0]))
            DeliveryNotes::$DICTHP3[$id] = $DN[0];
        else 
            DeliveryNotes::$DICTHP3[$id] = [];
		return $DN;
    }

    public static function getFromSO($so){
        
        $params         = [
                'refDocs'          => $so,
                "Take"              => 1,
                "Skip"              => 0,
        ];

		$DN 			= self::get($params, self::$url);
		return $DN;
    }
    public static function convertOrders($id_shipment){
        $params = [$id_shipment];
        $url = DeliveryNotes::$url . "/ConvertOrders";
        $DN 			= self::post($params, $url);
        if (isset($DN[0]))
            return $DN[0]['deliveryNumber'];
        else 
            return false;

    }
}
?>