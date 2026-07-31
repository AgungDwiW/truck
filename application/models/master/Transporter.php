<?php
class Transporter extends ApiModel{
    public static $url     = API_SERVER . 'Transporters';
    public static $DICT     = [];
    public static function getOne($transporterId){
        if (!isset(self::$DICT[$transporterId])){
            self::$DICT[$transporterId] = self::get(["TransporterIds" => $transporterId, ], API_SERVER . 'Transporter');
        }
        return self::get(["TransporterIds" => $transporterId, ], API_SERVER . 'Transporters');
    }
    public static function getKeyValue(){
        $params = ['IsActive' => "true", "Skip" => 0 , "Take" =>"100"];
        $mTrans = [];
        $result = ApiCall("GET", API_SERVER . "Transporters?". http_build_query($params), null );
        $result = json_decode($result,1);
        foreach($result as $r){
            $mTrans[$r['transporterId']]="{$r['transporterName']} - {$r['transporterId']}";
        }
        return $mTrans;
    }
}    
?>