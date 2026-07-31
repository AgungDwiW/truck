<?php 
class InternalMvt extends ApiModel{
    public static $DICT = [];
    public static  $url =  API_SERVER. "InternalMvt";
    public static function slocTransfer($param){
        /*
            {
                "siteId": "string",
                "mvtType": "string",
                "reasonCode": "string",
                "refDoc": "string",
                "refDocItem": "string",
                "items": [
                    {
                    "sourcePalletTag": "string",
                    "targetSloc": "string",
                    "targetBin": "string",
                    "targetQty": 0,
                    "uom": "string"
                    }
                ]
            }
        */
        $url = API_SERVER."Pallets/SlocTransfer";
        return self::post($param, $url);
        

    }
    public static function pool($palletTags, $targetBin){
        /*
            // change BIN to POOL
           {
                "siteId": "string",
                "palletTags": [
                    "string"
                ],
                "targetBin": "string"
            }
        */
        $param = [
                    "siteId"	=> PLANTID,
                    "palletTags"	=> [$palletTags],
                    "targetBin"	=> $targetBin,
            ];
        if (is_array($palletTags))
            $param['palletTags'] = $palletTags;

        $url = API_SERVER."Pallets/Pool";
        return self::post($param, $url);

    }
    public static function poolRev($palletTags, $targetBin){
        /*
            // change BIN to POOL
           {
                "siteId": "string",
                "palletTags": [
                    "string"
                ],
                "targetBin": "string"
            }
        */
        $param = [
                    "siteId"	=> PLANTID,
                    "palletTags"	=> [$palletTags],
                    "targetBin"	=> $targetBin,
            ];
        if (is_array($palletTags))
            $param['palletTags'] = $palletTags;
        
        $url = API_SERVER."Pallets/PoolRev";
        return self::post($param, $url);


    }

    public static function createStock($header, $palletes){
        /* 
            $paletes[]=['pallet_id'=>$_POST[$trx], 'qty'=>$_POST["{$trx}_qty"], 'key'=>$trx];
        */
        $data  = [ 'mvtType'       => '551',
                    'reasonCode'    => '15',
                    "sapConnect"    => true,
                    'refDoc'        =>  $this->idFull,
                    'userName'      => User::$username,
                    'transactType'  => null,
                    'siteId'        => $hp3[0] ,
                    'app_id'        => "{$this->dist_dc}-{$hp3[1]}-{$hp3[2]}-{$hp3[3]}-{$hp3[4]}",
                    "palletRegisters"   => []
                ];
        foreach($palletes as $pallet){
            
            $pallet_id  = $pallet['pallet_id'];
            $qty        = $pallet['qty'];
            $pallet     = Pallets::getPallet($pallet_id);
            $pallet['qty']      =$qty;
            $pallet['qtyBuom']  =$qty;
            $data['palletRegisters'] [] = $pallet;
        }
        $url = API_SERVER."InternalMvt/CreateStock";
        return self::post($param, $url);
    }
}
?>