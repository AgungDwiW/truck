<?php 
class Orders extends ApiModel{
    public static $DICT = [];
    public static  $url =  API_SERVER. "Orders";
    public static function create($hp3Id, $transporterId, $partnerNumber, $driver, $nopol, $itemPassed){
        $data = [
            "orderId" => "",
            "siteId" => PLANTID,
            "orderStatusId" => null,
            "orderType" => "ZRE2",
            "refId" => $hp3Id,
            "windowTimeStart" => gmdate('Y-m-d\TH:i:s.v\Z'),
            "windowTimeEnd" => gmdate('Y-m-d\TH:i:s.v\Z'),
            "plannedDeliveryDate" => date("Y-m-d"),
            "transporterId" => $transporterId,
            "partnerNumber" => $partnerNumber,
            "vanDriverName" => $driver,
            "licensePlate" => $nopol,
        ];

        $items = [];
        $notes = [];
        foreach($itemPassed as $item){
            if ($item['qty']==0)
                continue;

            $items[] = [
                        "itemNumber" => null,
                        "materialId" => $item['material_id'],
                        "qty" => $item['qty'],
                        "uom" => $item['uom']
                    ];
            $notes[] = [
                        "noteNumber" => null,
                        "notes" => "retur kembali botol saja"
                    ];
        }
        $data['items'] = $items;
        $data['notes'] = $notes;
        $ret = self::post([$data], Orders::$url );
        return $ret;


    }
    public static function getOrder($orderId){
        $param = ['OrderIds' => $orderId];
        $ret = self::get($param, Orders::$url );
        if (isset ($ret[0]))
            return $ret[0];
        else 
            return [];
    }
    public static function getFromHP3($hp3Id){
        $param = ['refIds' => $hp3Id];
        $ret = self::get($param, Orders::$url );
        if (isset ($ret[0]))
            return $ret[0];
        else 
            return [];
    } 

    public static function checkValidDate($orderId){
        $order = Orders::getOrder($orderId);
        if (empty($order))
			return false;

		$coNo    = (isset($order['orderId']) && $order['orderId'] !== '') ? $order['orderId'] : $orderId;
		$planned = substr(trim((string)($order['plannedDeliveryDate'] ?? '')), 0, 10);

		$today  = new DateTime('today');
		$monday = (clone $today)->modify('-' . ($today->format('N') - 1) . ' days');
		$sunday = (clone $monday)->modify('+6 days');

		/* An unset or unreadable date also fails the control - "cannot tell" is not "this week". */
		if ($planned < $monday->format('Y-m-d') || $planned > $sunday->format('Y-m-d'))
			return false;
        return true;

    }
}
?>