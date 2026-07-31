<?php
class Line extends ApiModel{
    public static $url = API_SERVER. "ProductionLines";
    
    public static function getMLine(){
        $params = [
				'SiteIds'=>User::$plantid,
				"Active" 			        => 'true',
        ];
        $result = self::get($params, self::$url);
        $mLine[''] = "- LINE -";
        foreach ($result as $row){
            $mLine[$row['lineId']] = $row['lineId']." - ".$row['lineName'];
        }
        return $mLine;

    }

}

?>