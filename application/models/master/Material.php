<?php
Class Material {
    public static $DICT = [];
    public static $DICT_InventoryUom = [];
    public static $CONV = [];
  
    public static function getMaterial_fromId($material_id){
        if (isset(Material::$DICT[$material_id]))
            return Material::$DICT[$material_id];

        $q              = "Materials?MaterialIds={$material_id}&IncludeMaterialUnitConversion=true&IncludeMaterialDescription=true";
        $mtl            = ApiCall("GET", API_SERVER. $q, null);
		$mtl 			= json_decode($mtl,1);
        Debuger::dump($mtl);
        if (is_null($mtl))
            $mtl = [];
        if (isset($mtl[0]))
            Material::$DICT[$material_id] = $mtl[0];
        else 
            Material::$DICT[$material_id] = [];
		return $mtl;
    }
    public static function getMaterial_fromName($name){
        $params         =  [
                'MaterialNameKey'                   => $name,
                'IncludeMaterialUnitConversion'     => "true",
                'IncludeMaterialUnitConversion'     => "true",
                'IncludeMaterialDescription'        => "true",
                ];
        $url = http_build_query($params);
        $url = API_SERVER ."Materials?". $url;
        Debuger::dump($url);
		$mtl            = ApiCall("GET", $url, null);
    	$mtl = json_decode($mtl,1);
        if (is_null($mtl))
            $mtl = [];

        foreach($mtl as $row){
            Material::$DICT[$row['materialId']] = $row;
        }
        return $mtl;
    }

    public static function getMaterialNameFromData($materialData, $LANG = "EN"){
        if (!isset($materialData['materialDescriptions']))
            return "UNDEFINED";
        else{
            $dict=[];
            foreach($materialData['materialDescriptions'] as $descriptions){
                $dict[$descriptions['languageKey']] = $descriptions['materialName'];
            }
        }
        if (isset($dict[$LANG]))
            return $dict[$LANG];
        else 
            return "UNDEFINED FOR LANGUAGE {$LANG}";
    }

    
    public static function getConversion($material_id){
        if (isset( Material::$CONV[$material_id]))
            return Material::$CONV[$material_id];

        if(!isset(Material::$DICT[$material_id]))
            Material::getMaterial_fromId($material_id);
        $material = Material::$DICT[$material_id];
        $conversions = [];
        foreach($material["materialUnitConversions"] as $conv){
            $conversions[$conv['uom']] = $conv;
        }
        Material::$CONV[$material_id] = $conversions;
        return $conversions;
    }


    public static function conv_uom_buom($material_id, $uom, $qty){
        $conv = Material::getConversion($material_id);
        if (!isset($conv[$uom])){
			echo "<div style='margin-top: 20px;padding: 20px;border:dotted 1px gray;color: #f44336;'><b>ALERT!</b> "."conversi uom {$uom} untuk material {$material_id} Tidak ditemukan!"."</div>";
			exit();
		}
        $qty_buom = $qty * $conv[$uom]['convNumerator'] / $conv[$uom]['convDenominator'];
        return ['qty_buom' => $qty_buom, 'buom' => $conv[$uom]['buom']];
    }

    public static function conv_buom_uom($material_id, $uom, $qty){
        $conv = Material::getConversion($material_id);
        if (!isset($conv[$uom])){
			echo "<div style='margin-top: 20px;padding: 20px;border:dotted 1px gray;color: #f44336;'><b>ALERT!</b> "."conversi uom {$uom} untuk material {$material_id} Tidak ditemukan!"."</div>";
			exit();
		}
        Debuger::dump([$material_id, $uom, $qty, $conv]);
        $qty_buom = $qty / $conv[$uom]['convNumerator'] * $conv[$uom]['convDenominator'];
        return ['qty_uom' => $qty_buom, 'buom' => $conv[$uom]['buom']];
    }
    public static function getInventoryUom($material_id){
        if (isset(Material::$DICT_InventoryUom[$material_id]))
            return Material::$DICT_InventoryUom[$material_id];
        $table = new Table("tbm_inventory_uom");
        $data = $table->get("uom")->where("material_id  ='{$material_id}'")->fetchOne();
        if (!isset($data['uom']))
            $ret =  "UNDEFINED";
        else 
            $ret =  $data['uom'];
        Material::$DICT_InventoryUom[$material_id] = $ret;
        return $ret;
    }
}
?>