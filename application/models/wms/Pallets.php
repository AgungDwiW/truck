<?php 
class Pallets{
    public static $DICT = [];
    public static  $url =  API_SERVER. "Pallets?";
    public static function getPallet($palletId){
        if (isset(Pallets::$DICT[$palletId]))
            return Pallets::$DICT[$palletId];

        $q              = "Pallets?PalletIds={$palletId}&Take=1&Skip=0";
        $pallet            = ApiCall("GET", API_SERVER. $q, null);
		$pallet 			= json_decode($pallet,1);
        Debuger::dump($pallet);
        if (is_null($pallet))
            $pallet = [];
        if (isset($pallet[0]))
            Pallets::$DICT[$palletId] = $pallet[0];
        else 
            Pallets::$DICT[$palletId] = [];
		return $pallet;
    }
    public static function getPalletVIRT(){
        if (isset(Pallets::$DICT["VIRT"]))
            return Pallets::$DICT["VIRT"];
        $params = [
            'MaterialIds'   => '10169933',
            "SiteIds"       => PLANTID,
            "Slocs"         => "VIRT",
            "StaticPallet"  => "true"
        ];
        $q              = Pallets::$url . http_build_query($params);
        $pallet            = ApiCall("GET", $q, null);
		$pallet 			= json_decode($pallet,1);
        Debuger::dump($pallet);
        if (is_null($pallet))
            $pallet = [];
        if (isset($pallet[0]))
            Pallets::$DICT["VIRT"] = $pallet[0];
        else 
            Pallets::$DICT["VIRT"] = [];
		return $pallet;
    }

    public static function getIdOrTag($identifier){
        if (in_array($identifier, Pallets::$DICT))
            return Pallets::$DICT[$identifier];
        $params = ['SiteIds' => PLANTID, "PalletIds"=> $identifier];
		$pallet = Pallets::get($params);
		if (count($pallet) == 0){
			$params = ['SiteIds' => PLANTID, "PalletTags"=> $identifier];
			$pallet = Pallets::get($params);
			if (count($pallet) == 0){
				return [];
			}
		}
        else{
            $pallet = $pallet[0];
        }
        Pallets::$DICT[$identifier] = $pallet;
        return $pallet;
    }

    public static function get($params){
        $q              = Pallets::$url . http_build_query($params);
        $data           = ApiCall("GET", $q, null);
        Debuger::dump([$q,$data]);
		$data 			= json_decode($data,1);
        if (is_null($data))
            $data = [];
        return $data;
    }

    public static function getTagFromId($palletId){
        if (isset(Pallets::$DICT[$palletId]))
            return Pallets::$DICT[$palletId]['palletTag'];
        else {
            Pallets::getIdOrTag($palletId);
            return Pallets::$DICT[$palletId]['palletTag'];
        }
    }
    
}
?>