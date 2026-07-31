<?php 
class SalesOrders extends ApiModel{
    public static $DICT = [];
    public static $url =  API_SERVER. "SalesOrders";


    public static function createSalesOrderHP3($hp3idfull, $items, $dist_dc, $destination_location_id){
        $params =[
            "orderHeader" => [
                "distributionChannel"      => "00",              // PASTI 00
                "division"                 => "00",              // PASTI 00
                "salesOrganization"        => "9000",            // PASTI 9000
                "documentType"             => "ZRE2",            // DR HP3 -> ZRE2
                "purchaseOrderNumber"      => $hp3idfull,           // No HP3
                "purchaseDate"             => date("Y-m-d"),           // TGL PO (YYYY-MM-DD)
                "requestedDate"            => date("Y-m-d"),           // TGL PO (YYYY-MM-DD)
                "referenceDocumentNumber"  => "",
                "billingBlock"             => "",
                "deliveryBlock"            => ""
            ],

            "orderItems" => [
                
            ],

            "partners" => [
                "soldToPartner" => [
                    "partnerRole"   => "AG",
                    "partnerNumber" => $dist_dc             // Distributor / DC Aqua / Vendor RTP
                ],
                "shipToPartner" => [
                    "partnerRole"   => "WE",
                    "partnerNumber" => $destination_location_id             // Destination Location Shipment
                ]
            ],

            "texts" => [
            ]
        ];

        $count = 1;
        foreach($items as $row){
            if ($row['qty'] == 0)
                continue;
            $item = [
                        "itemNumber"        => "{$count}0",                // 10,20,30,...
                        "targetQuantity"    => $row['qty'],             // Qty
                        "materialNumber"    => $row['material_id'],          // No Material
                        "itemCategory"      => "",
                        "salesUnit"         => "PC",
                        "plant"             => User::$plantid,
                        "storageLocation"   => $row['sloc'],                  // Storage Location
                        "customerMaterial"  => "",
                        "rejectionReason"   => ""
                    ];
            $text = [
                        "formatColumn" => "*",
                        "itemNumber"   => "000000",
                        "language"     => "EN",
                        "textId"       => "",
                        "textLine"     => ""
                    ];
            $params['orderItems'][] = $item;
            $params['texts'][] = $text;
            $count +=1;
        }
        $url = API_SERVER. "SalesOrders";
        return self::post([$params], $url);
    }
}
?>