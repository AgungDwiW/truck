<?php 
class GoodsIssues extends ApiModel{
    public static $DICT = [];
    public static $url =  API_SERVER. "GoodsIssues";


    public static function GI($ref_doc, $custPoNumber, $extDeliveryNote, $shipTo, $unloadingPoint, $pallets, $dn){
        $params =[
                [
                    "items" => [
                        
                    ],
                    "refDoc"          => $ref_doc['deliveryNumber'],
                    "siteId"          => User::$plantid,
                    "issueDate"       => date("Y-m-d"),
                    "transportType"   => date("Y-m-d"),
                    "transportId"     => null,
                    "transactId"      => null,
                    "custPoNumber"    => $custPoNumber,
                    "extDeliveryNote" => $custPoNumber,
                    "shipTo"          => $shipTo,
                    "unloadingPoint"  => $unloadingPoint,
                    "headerTextId"    => null,
                    "headerText"      => null
                ]
            ];
        $url = API_SERVER. "SalesOrders";
        $pallet_dict =[];
        foreach($dn['deliveryNoteItems'] as $row){
            $pallet_dict[$row['materialId']] = $row['itemNumber'];
        }
        foreach($pallets as $row){
            if ($row['qty'] == 0)
                continue;
            else{
                
                if(!isset($row['pallet_tag']) && isset($row['pallet_id'])){
                    include_once "application/models/wms/Pallets.php";
                    $row['pallet_tag'] = Pallets::getTagFromId($row['pallet_id']);
                }
                $items = [
                            "dnItemNumber" => $pallet_dict[$row['material_id']],
                            "itemTextId"   => null,
                            "itemText"     => null,
                            "pallets"      => [
                                            [
                                                "palletTag" => $row['pallet_tag'],
                                                "issuedQty" => $row['qty'],
                                                "uom"       => "PC"
                                            ]
                                        ]
                            
                            ];

                $params[0]['items'][] = $items;
            }
        }

        return self::post($params, GoodsIssues::$url);
    }
}
?>